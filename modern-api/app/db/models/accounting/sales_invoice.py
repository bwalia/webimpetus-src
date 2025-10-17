from __future__ import annotations

from enum import Enum

from sqlalchemy import ForeignKey, Numeric, String
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.db.models.base import Base, BaseUUIDMixin


class InvoiceStatus(str, Enum):
    DRAFT = "draft"
    SENT = "sent"
    PARTIAL = "partial"
    PAID = "paid"
    VOID = "void"


class SalesInvoice(Base, BaseUUIDMixin):
    __tablename__ = "sales_invoices"

    business_id: Mapped[str] = mapped_column(ForeignKey("businesses.id"), nullable=False, index=True)
    customer_id: Mapped[str] = mapped_column(ForeignKey("contacts.id"), nullable=False, index=True)
    number: Mapped[str] = mapped_column(String(40), nullable=False, index=True)
    currency: Mapped[str] = mapped_column(String(3), nullable=False, default="GBP")
    exchange_rate: Mapped[float] = mapped_column(Numeric(12, 6), nullable=False, default=1)
    issue_date: Mapped[str] = mapped_column(String(10), nullable=False)
    due_date: Mapped[str] = mapped_column(String(10), nullable=False)
    status: Mapped[str] = mapped_column(String(20), default=InvoiceStatus.DRAFT.value)
    subtotal: Mapped[float] = mapped_column(Numeric(12, 2), nullable=False, default=0)
    tax_total: Mapped[float] = mapped_column(Numeric(12, 2), nullable=False, default=0)
    total: Mapped[float] = mapped_column(Numeric(12, 2), nullable=False, default=0)
    balance_due: Mapped[float] = mapped_column(Numeric(12, 2), nullable=False, default=0)

    business = relationship("Business", back_populates="sales_invoices")
    customer = relationship("Contact", back_populates="invoices")
    line_items = relationship("SalesInvoiceLineItem", back_populates="invoice", cascade="all, delete-orphan")


class SalesInvoiceLineItem(Base, BaseUUIDMixin):
    __tablename__ = "sales_invoice_line_items"

    invoice_id: Mapped[str] = mapped_column(ForeignKey("sales_invoices.id"), nullable=False, index=True)
    product_id: Mapped[str | None] = mapped_column(ForeignKey("products.id"), nullable=True)
    description: Mapped[str] = mapped_column(String(255), nullable=False)
    quantity: Mapped[float] = mapped_column(Numeric(12, 2), nullable=False)
    unit_price: Mapped[float] = mapped_column(Numeric(12, 2), nullable=False)
    tax_rate: Mapped[float] = mapped_column(Numeric(5, 2), nullable=False, default=0)
    line_total: Mapped[float] = mapped_column(Numeric(12, 2), nullable=False)

    invoice = relationship("SalesInvoice", back_populates="line_items")
