from __future__ import annotations

from sqlalchemy import ForeignKey, String
from sqlalchemy.orm import Mapped, mapped_column, relationship

from .base import Base, BaseUUIDMixin


class Contact(Base, BaseUUIDMixin):
    __tablename__ = "contacts"

    business_id: Mapped[str] = mapped_column(ForeignKey("businesses.id"), nullable=False, index=True)
    first_name: Mapped[str] = mapped_column(String(120), nullable=False)
    last_name: Mapped[str] = mapped_column(String(120), nullable=False)
    email: Mapped[str] = mapped_column(String(255), nullable=False, index=True)
    phone: Mapped[str | None] = mapped_column(String(50), nullable=True)
    gdpr_consent: Mapped[bool] = mapped_column(default=False, nullable=False)

    business = relationship("Business", back_populates="contacts")
    tasks = relationship("Task", back_populates="assignee")
    invoices = relationship("SalesInvoice", back_populates="customer")
