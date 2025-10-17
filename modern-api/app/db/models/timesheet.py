from __future__ import annotations

from datetime import datetime
from decimal import Decimal
from enum import Enum

from sqlalchemy import Boolean, DateTime, ForeignKey, Integer, Numeric, String
from sqlalchemy.orm import Mapped, mapped_column

from .base import Base, BaseUUIDMixin


class TimesheetStatus(str, Enum):
    DRAFT = "draft"
    RUNNING = "running"
    STOPPED = "stopped"
    INVOICED = "invoiced"


class Timesheet(Base, BaseUUIDMixin):
    __tablename__ = "timesheets"

    business_id: Mapped[str] = mapped_column(ForeignKey("businesses.id"), nullable=False, index=True)
    employee_id: Mapped[str] = mapped_column(String(36), nullable=False, index=True)
    project_id: Mapped[str | None] = mapped_column(ForeignKey("projects.id"), nullable=True, index=True)
    task_id: Mapped[str | None] = mapped_column(String(36), nullable=True, index=True)
    customer_id: Mapped[str | None] = mapped_column(String(36), nullable=True, index=True)
    description: Mapped[str | None] = mapped_column(String(2000), nullable=True)
    start_time: Mapped[datetime] = mapped_column(DateTime, nullable=False)
    end_time: Mapped[datetime | None] = mapped_column(DateTime, nullable=True)
    duration_minutes: Mapped[int | None] = mapped_column(Integer, nullable=True)
    billable_hours: Mapped[Decimal | None] = mapped_column(Numeric(10, 2), nullable=True)
    hourly_rate: Mapped[Decimal | None] = mapped_column(Numeric(10, 2), nullable=True)
    total_amount: Mapped[Decimal | None] = mapped_column(Numeric(12, 2), nullable=True)
    is_billable: Mapped[bool] = mapped_column(Boolean, default=True, nullable=False)
    is_running: Mapped[bool] = mapped_column(Boolean, default=False, nullable=False)
    is_invoiced: Mapped[bool] = mapped_column(Boolean, default=False, nullable=False)
    invoice_id: Mapped[str | None] = mapped_column(String(36), nullable=True, index=True)
    status: Mapped[str] = mapped_column(String(20), default=TimesheetStatus.DRAFT.value, nullable=False)
    notes: Mapped[str | None] = mapped_column(String(2000), nullable=True)
    tags: Mapped[str | None] = mapped_column(String(500), nullable=True)
