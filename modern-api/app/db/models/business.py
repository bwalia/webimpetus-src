from __future__ import annotations

from enum import Enum

from sqlalchemy import String
from sqlalchemy.orm import Mapped, mapped_column, relationship

from .base import Base, BaseUUIDMixin


class BusinessStatus(str, Enum):
    ACTIVE = "active"
    INACTIVE = "inactive"
    PROSPECT = "prospect"


class Business(Base, BaseUUIDMixin):
    __tablename__ = "businesses"

    name: Mapped[str] = mapped_column(String(200), nullable=False)
    code: Mapped[str] = mapped_column(String(50), nullable=False, index=True)
    status: Mapped[str] = mapped_column(String(20), default=BusinessStatus.ACTIVE.value)
    industry: Mapped[str | None] = mapped_column(String(100), nullable=True)
    website: Mapped[str | None] = mapped_column(String(255), nullable=True)

    contacts = relationship("Contact", back_populates="business")
    projects = relationship("Project", back_populates="business")
    sales_invoices = relationship("SalesInvoice", back_populates="business")
