from __future__ import annotations

from decimal import Decimal
from typing import Sequence

from sqlalchemy import select

from app.db.models.accounting.sales_invoice import SalesInvoice, SalesInvoiceLineItem

from .base import BaseRepository


class SalesInvoiceRepository(BaseRepository[SalesInvoice]):
    model = SalesInvoice

    def list_by_business(self, tenant_id: str, business_id: str) -> Sequence[SalesInvoice]:
        stmt = select(SalesInvoice).where(
            SalesInvoice.tenant_id == tenant_id,
            SalesInvoice.business_id == business_id,
            SalesInvoice.is_deleted == False,
        )
        return [row[0] for row in self.session.execute(stmt).all()]

    def upsert_line_items(self, invoice: SalesInvoice, items: list[SalesInvoiceLineItem]) -> None:
        invoice.line_items.clear()
        for item in items:
            invoice.line_items.append(item)

    def recompute_totals(self, invoice: SalesInvoice) -> None:
        subtotal = sum((item.quantity * item.unit_price for item in invoice.line_items), Decimal("0"))
        tax_total = sum(
            (item.quantity * item.unit_price * item.tax_rate / Decimal("100")) for item in invoice.line_items
        )
        invoice.subtotal = subtotal
        invoice.tax_total = tax_total
        invoice.total = subtotal + tax_total
        invoice.balance_due = invoice.total
        invoice.version += 1
