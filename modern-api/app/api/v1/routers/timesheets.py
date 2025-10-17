from __future__ import annotations

from datetime import date, datetime, time
from typing import Annotated
from uuid import UUID

from fastapi import APIRouter, Depends, HTTPException, Query, Response, status
from sqlalchemy.orm import Session

from app.api.v1.dependencies.auth import get_current_principal
from app.api.v1.dependencies.common import get_cursor, get_idempotency_key, get_if_match
from app.api.v1.dependencies.database import get_db
from app.core.security import Principal
from app.schemas.common import PaginatedResponse
from app.schemas.timesheets import Timesheet as TimesheetSchema
from app.schemas.timesheets import TimesheetCreate, TimesheetStartRequest, TimesheetUpdate
from app.services.timesheets import TimesheetListFilters, TimesheetService
from app.services.utils.pagination import CursorPage

router = APIRouter(prefix="/timesheets", tags=["Timesheets"])


def _date_to_datetime(value: date | None, *, end_of_day: bool = False) -> datetime | None:
    if value is None:
        return None
    if end_of_day:
        return datetime.combine(value, time.max.replace(microsecond=0))
    return datetime.combine(value, time.min)


@router.get("", response_model=PaginatedResponse[TimesheetSchema])
def list_timesheets(
    cursor: Annotated[CursorPage, Depends(get_cursor)],
    business_id: UUID | None = Query(default=None, alias="business_id"),
    status_filter: str | None = Query(default=None, alias="status"),
    employee_id: UUID | None = Query(default=None, alias="employee_id"),
    project_id: UUID | None = Query(default=None, alias="project_id"),
    customer_id: UUID | None = Query(default=None, alias="customer_id"),
    is_billable: bool | None = Query(default=None, alias="is_billable"),
    is_invoiced: bool | None = Query(default=None, alias="is_invoiced"),
    from_date: date | None = Query(default=None, alias="from_date"),
    to_date: date | None = Query(default=None, alias="to_date"),
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TimesheetService(db, principal)
    filters = TimesheetListFilters(
        business_id=str(business_id) if business_id else None,
        status=status_filter,
        employee_id=str(employee_id) if employee_id else None,
        project_id=str(project_id) if project_id else None,
        customer_id=str(customer_id) if customer_id else None,
        is_billable=is_billable,
        is_invoiced=is_invoiced,
        from_date=_date_to_datetime(from_date),
        to_date=_date_to_datetime(to_date, end_of_day=True),
    )
    return service.list_timesheets(cursor=cursor, filters=filters)


@router.post("", response_model=TimesheetSchema, status_code=status.HTTP_201_CREATED)
def create_timesheet(
    payload: TimesheetCreate,
    response: Response,
    idempotency_key: Annotated[str | None, Depends(get_idempotency_key)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TimesheetService(db, principal)
    try:
        result = service.create_timesheet(payload, idempotency_key=idempotency_key)
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_409_CONFLICT, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.get("/{timesheet_id}", response_model=TimesheetSchema)
def get_timesheet(
    timesheet_id: UUID,
    response: Response,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TimesheetService(db, principal)
    try:
        result = service.get_timesheet(str(timesheet_id))
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Timesheet not found")
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.patch("/{timesheet_id}", response_model=TimesheetSchema)
def update_timesheet(
    timesheet_id: UUID,
    payload: TimesheetUpdate,
    response: Response,
    if_match: Annotated[str | None, Depends(get_if_match)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TimesheetService(db, principal)
    try:
        result = service.update_timesheet(str(timesheet_id), payload, if_match=if_match)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Timesheet not found")
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_412_PRECONDITION_FAILED, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.delete("/{timesheet_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_timesheet(
    timesheet_id: UUID,
    hard_delete: bool = Query(default=False, alias="hard_delete"),
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TimesheetService(db, principal)
    try:
        service.delete_timesheet(str(timesheet_id), hard_delete=hard_delete)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Timesheet not found")


@router.post("/start", response_model=TimesheetSchema, status_code=status.HTTP_201_CREATED)
def start_timer(
    payload: TimesheetStartRequest,
    response: Response,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TimesheetService(db, principal)
    result = service.start_timer(payload)
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.post("/{timesheet_id}/stop", response_model=TimesheetSchema)
def stop_timer(
    timesheet_id: UUID,
    response: Response,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TimesheetService(db, principal)
    try:
        result = service.stop_timer(str(timesheet_id))
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Timesheet not found")
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data
