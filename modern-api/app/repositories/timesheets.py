from __future__ import annotations

from datetime import datetime
from typing import Sequence

from sqlalchemy import Select, select

from app.db.models.timesheet import Timesheet, TimesheetStatus

from .base import BaseRepository


class TimesheetRepository(BaseRepository[Timesheet]):
    model = Timesheet

    def _base_query(self, tenant_id: str) -> Select[tuple[Timesheet]]:
        return select(Timesheet).where(Timesheet.tenant_id == tenant_id, Timesheet.is_deleted == False)

    def list_filtered(
        self,
        tenant_id: str,
        *,
        business_id: str | None = None,
        status: str | None = None,
        employee_id: str | None = None,
        project_id: str | None = None,
        customer_id: str | None = None,
        is_billable: bool | None = None,
        is_invoiced: bool | None = None,
        from_date: datetime | None = None,
        to_date: datetime | None = None,
    ) -> Sequence[Timesheet]:
        stmt = self._base_query(tenant_id)
        if business_id:
            stmt = stmt.where(Timesheet.business_id == business_id)
        if status:
            stmt = stmt.where(Timesheet.status == status)
        if employee_id:
            stmt = stmt.where(Timesheet.employee_id == employee_id)
        if project_id:
            stmt = stmt.where(Timesheet.project_id == project_id)
        if customer_id:
            stmt = stmt.where(Timesheet.customer_id == customer_id)
        if is_billable is not None:
            stmt = stmt.where(Timesheet.is_billable == is_billable)
        if is_invoiced is not None:
            stmt = stmt.where(Timesheet.is_invoiced == is_invoiced)
        if from_date:
            stmt = stmt.where(Timesheet.start_time >= from_date)
        if to_date:
            stmt = stmt.where(Timesheet.start_time <= to_date)
        stmt = stmt.order_by(Timesheet.start_time.desc())
        return self.session.execute(stmt).scalars().all()

    def get_running(
        self,
        tenant_id: str,
        *,
        employee_id: str | None = None,
    ) -> Sequence[Timesheet]:
        stmt = self._base_query(tenant_id).where(
            Timesheet.is_running == True,  # noqa: E712
            Timesheet.status == TimesheetStatus.RUNNING.value,
        )
        if employee_id:
            stmt = stmt.where(Timesheet.employee_id == employee_id)
        return self.session.execute(stmt).scalars().all()
