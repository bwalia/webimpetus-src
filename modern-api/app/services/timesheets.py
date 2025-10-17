from __future__ import annotations

from dataclasses import dataclass
from datetime import datetime
from decimal import Decimal, ROUND_HALF_UP

from sqlalchemy.orm import Session

from app.core.security import Principal
from app.db.models.timesheet import Timesheet, TimesheetStatus
from app.repositories.timesheets import TimesheetRepository
from app.schemas.common import PaginatedResponse
from app.schemas.timesheets import (
    Timesheet as TimesheetSchema,
    TimesheetCreate,
    TimesheetStartRequest,
    TimesheetUpdate,
)

from .base import BaseService, ServiceResult
from .utils.pagination import CursorPage


@dataclass(slots=True)
class TimesheetListFilters:
    business_id: str | None = None
    status: str | None = None
    employee_id: str | None = None
    project_id: str | None = None
    customer_id: str | None = None
    is_billable: bool | None = None
    is_invoiced: bool | None = None
    from_date: datetime | None = None
    to_date: datetime | None = None


class TimesheetService(BaseService):
    def __init__(self, session: Session, principal: Principal) -> None:
        super().__init__(session, principal)
        self.repo = TimesheetRepository(session)

    def list_timesheets(self, *, cursor: CursorPage, filters: TimesheetListFilters) -> PaginatedResponse[TimesheetSchema]:
        self._ensure_scope("crm.read")
        items = self.repo.list_filtered(
            self.principal.tenant_id,
            business_id=filters.business_id,
            status=filters.status,
            employee_id=filters.employee_id,
            project_id=filters.project_id,
            customer_id=filters.customer_id,
            is_billable=filters.is_billable,
            is_invoiced=filters.is_invoiced,
            from_date=filters.from_date,
            to_date=filters.to_date,
        )
        data = [TimesheetSchema.model_validate(item, from_attributes=True) for item in items]
        page = cursor.apply(data)
        return PaginatedResponse[TimesheetSchema](data=page.items, meta=page.meta, links=page.links)

    def create_timesheet(self, payload: TimesheetCreate, *, idempotency_key: str | None) -> ServiceResult:
        self._ensure_scope("crm.write")
        fingerprint = payload.model_dump_json()
        self._assert_idempotency(idempotency_key, fingerprint)

        instance = Timesheet(
            tenant_id=self.principal.tenant_id,
            business_id=str(payload.business_id),
            employee_id=str(payload.employee_id),
            project_id=str(payload.project_id) if payload.project_id else None,
            task_id=str(payload.task_id) if payload.task_id else None,
            customer_id=str(payload.customer_id) if payload.customer_id else None,
            description=payload.description,
            start_time=payload.start_time,
            end_time=payload.end_time,
            hourly_rate=payload.hourly_rate,
            is_billable=payload.is_billable,
            is_running=payload.is_running,
            is_invoiced=payload.is_invoiced,
            invoice_id=str(payload.invoice_id) if payload.invoice_id else None,
            status=payload.status.value if hasattr(payload.status, "value") else payload.status,
            notes=payload.notes,
            tags=payload.tags,
        )
        instance.created_by = self.principal.sub

        self._recalculate_totals(instance)
        self.repo.add(self.principal.tenant_id, instance)
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=TimesheetSchema.model_validate(instance, from_attributes=True), etag=etag)

    def get_timesheet(self, timesheet_id: str) -> ServiceResult:
        self._ensure_scope("crm.read")
        instance = self.repo.get(self.principal.tenant_id, timesheet_id)
        if not instance:
            raise KeyError("Timesheet not found")
        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(
            data=TimesheetSchema.model_validate(instance, from_attributes=True),
            etag=etag,
            version=instance.version,
            updated_at=instance.updated_at,
        )

    def update_timesheet(self, timesheet_id: str, payload: TimesheetUpdate, *, if_match: str | None) -> ServiceResult:
        self._ensure_scope("crm.write")
        instance = self.repo.get(self.principal.tenant_id, timesheet_id)
        if not instance:
            raise KeyError("Timesheet not found")
        current_etag = self._compute_etag(instance.version, instance.updated_at)
        if if_match and if_match != current_etag:
            raise ValueError("ETag mismatch")

        update_data = payload.model_dump(exclude_unset=True)
        for key, value in update_data.items():
            if key == "business_id" and value is not None:
                setattr(instance, key, str(value))
            elif key in {"employee_id", "project_id", "task_id", "customer_id", "invoice_id"}:
                setattr(instance, key, str(value) if value is not None else None)
            elif key == "status" and value is not None:
                setattr(instance, key, value.value if hasattr(value, "value") else value)
            else:
                setattr(instance, key, value)

        instance.updated_by = self.principal.sub
        instance.version += 1

        self._recalculate_totals(instance)
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=TimesheetSchema.model_validate(instance, from_attributes=True), etag=etag)

    def delete_timesheet(self, timesheet_id: str, *, hard_delete: bool = False) -> None:
        self._ensure_scope("crm.write")
        instance = self.repo.get(self.principal.tenant_id, timesheet_id)
        if not instance:
            raise KeyError("Timesheet not found")
        if hard_delete:
            self.session.delete(instance)
        else:
            instance.is_deleted = True
            instance.version += 1
        self.session.flush()

    def start_timer(self, payload: TimesheetStartRequest) -> ServiceResult:
        self._ensure_scope("crm.write")
        start_time = payload.start_time or datetime.utcnow()
        status = payload.status or TimesheetStatus.RUNNING
        instance = Timesheet(
            tenant_id=self.principal.tenant_id,
            business_id=str(payload.business_id),
            employee_id=str(payload.employee_id),
            project_id=str(payload.project_id) if payload.project_id else None,
            task_id=str(payload.task_id) if payload.task_id else None,
            customer_id=str(payload.customer_id) if payload.customer_id else None,
            description=payload.description,
            start_time=start_time,
            end_time=None,
            hourly_rate=payload.hourly_rate,
            is_billable=payload.is_billable,
            is_running=True,
            is_invoiced=False,
            invoice_id=None,
            status=status.value if hasattr(status, "value") else status,
            notes=payload.notes,
            tags=payload.tags,
        )
        instance.created_by = self.principal.sub

        self.repo.add(self.principal.tenant_id, instance)
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=TimesheetSchema.model_validate(instance, from_attributes=True), etag=etag)

    def stop_timer(self, timesheet_id: str) -> ServiceResult:
        self._ensure_scope("crm.write")
        instance = self.repo.get(self.principal.tenant_id, timesheet_id)
        if not instance:
            raise KeyError("Timesheet not found")
        if not instance.is_running or instance.status != TimesheetStatus.RUNNING.value:
            raise ValueError("Timesheet is not currently running")

        instance.end_time = datetime.utcnow()
        instance.is_running = False
        instance.status = TimesheetStatus.STOPPED.value
        instance.updated_by = self.principal.sub
        instance.version += 1

        self._recalculate_totals(instance)
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=TimesheetSchema.model_validate(instance, from_attributes=True), etag=etag)

    def _recalculate_totals(self, instance: Timesheet) -> None:
        if instance.start_time and instance.end_time and instance.end_time >= instance.start_time:
            delta = instance.end_time - instance.start_time
            minutes = int(delta.total_seconds() // 60)
            instance.duration_minutes = minutes
            if minutes > 0:
                hours = (Decimal(minutes) / Decimal(60)).quantize(Decimal("0.01"), ROUND_HALF_UP)
                instance.billable_hours = hours
                if instance.hourly_rate is not None:
                    rate = Decimal(instance.hourly_rate)
                    instance.total_amount = (hours * rate).quantize(Decimal("0.01"), ROUND_HALF_UP)
                else:
                    instance.total_amount = None
            else:
                instance.billable_hours = None
                instance.total_amount = None
        else:
            instance.duration_minutes = None
            instance.billable_hours = None
            if not instance.is_running:
                instance.total_amount = None