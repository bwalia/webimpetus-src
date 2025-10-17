from __future__ import annotations

from datetime import datetime
from decimal import Decimal
from typing import Optional
from uuid import UUID

from pydantic import AliasChoices, BaseModel, Field, computed_field
from pydantic.config import ConfigDict

from app.db.models.timesheet import TimesheetStatus

from .common import AuditFields


class TimesheetBase(BaseModel):
    business_id: UUID = Field(
        validation_alias=AliasChoices("uuid_business_id", "business_id"),
        serialization_alias="uuid_business_id",
    )
    employee_id: UUID
    project_id: Optional[UUID] = None
    task_id: Optional[UUID] = None
    customer_id: Optional[UUID] = None
    description: Optional[str] = Field(default=None, max_length=2000)
    start_time: datetime = Field(description="ISO timestamp when the entry begins")
    end_time: Optional[datetime] = Field(default=None, description="ISO timestamp when the entry ends")
    hourly_rate: Optional[Decimal] = Field(default=None, ge=0)
    is_billable: bool = True
    status: TimesheetStatus = TimesheetStatus.DRAFT
    notes: Optional[str] = Field(default=None, max_length=2000)
    tags: Optional[str] = Field(default=None, max_length=500)

    model_config = ConfigDict(populate_by_name=True, use_enum_values=True)


class TimesheetCreate(TimesheetBase):
    is_running: bool = False
    is_invoiced: bool = False
    invoice_id: Optional[UUID] = None


class TimesheetUpdate(BaseModel):
    business_id: Optional[UUID] = Field(default=None, validation_alias=AliasChoices("uuid_business_id", "business_id"))
    employee_id: Optional[UUID] = None
    project_id: Optional[UUID] = None
    task_id: Optional[UUID] = None
    customer_id: Optional[UUID] = None
    description: Optional[str] = Field(default=None, max_length=2000)
    start_time: Optional[datetime] = None
    end_time: Optional[datetime] = None
    hourly_rate: Optional[Decimal] = Field(default=None, ge=0)
    is_billable: Optional[bool] = None
    is_running: Optional[bool] = None
    is_invoiced: Optional[bool] = None
    invoice_id: Optional[UUID] = None
    status: Optional[TimesheetStatus] = None
    notes: Optional[str] = Field(default=None, max_length=2000)
    tags: Optional[str] = Field(default=None, max_length=500)

    model_config = ConfigDict(populate_by_name=True, use_enum_values=True)


class TimesheetStartRequest(BaseModel):
    business_id: UUID = Field(
        validation_alias=AliasChoices("uuid_business_id", "business_id"),
        serialization_alias="uuid_business_id",
    )
    employee_id: UUID
    project_id: Optional[UUID] = None
    task_id: Optional[UUID] = None
    customer_id: Optional[UUID] = None
    description: Optional[str] = Field(default=None, max_length=2000)
    hourly_rate: Optional[Decimal] = Field(default=None, ge=0)
    is_billable: bool = True
    status: Optional[TimesheetStatus] = None
    notes: Optional[str] = Field(default=None, max_length=2000)
    tags: Optional[str] = Field(default=None, max_length=500)
    start_time: Optional[datetime] = Field(default=None, description="Override timer start timestamp")

    model_config = ConfigDict(populate_by_name=True, use_enum_values=True)


class Timesheet(TimesheetBase, AuditFields):
    duration_minutes: Optional[int] = None
    billable_hours: Optional[Decimal] = None
    total_amount: Optional[Decimal] = None
    is_running: bool
    is_invoiced: bool
    invoice_id: Optional[UUID] = None

    model_config = ConfigDict(from_attributes=True, populate_by_name=True, use_enum_values=True)

    @computed_field(return_type=UUID, alias="uuid")
    @property
    def uuid(self) -> UUID:
        return self.id
