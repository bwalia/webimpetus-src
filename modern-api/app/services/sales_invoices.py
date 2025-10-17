from __future__ import annotations

from decimal import Decimal

from sqlalchemy.orm import Session

from app.core.security import Principal
from app.db.models.accounting.sales_invoice import SalesInvoice, SalesInvoiceLineItem
from app.repositories.sales_invoices import SalesInvoiceRepository
from app.schemas.common import PaginatedResponse
from app.schemas.sales_invoices import (
    SalesInvoice as SalesInvoiceSchema,
    SalesInvoiceCreate,
    SalesInvoiceLineItemCreate,
    SalesInvoiceUpdate,
)

from .base import BaseService, ServiceResult
from .utils.pagination import CursorPage


class SalesInvoiceService(BaseService):
    def __init__(self, session: Session, principal: Principal) -> None:
        super().__init__(session, principal)
        self.repo = SalesInvoiceRepository(session)

    def list_invoices(self, *, cursor: CursorPage, business_id: str | None = None) -> PaginatedResponse[SalesInvoiceSchema]:
        self._ensure_scope("finance.read")
        if business_id:
            items = self.repo.list_by_business(self.principal.tenant_id, business_id)
        else:
            items = self.repo.list(self.principal.tenant_id)
        data = [SalesInvoiceSchema.model_validate(item, from_attributes=True) for item in items]
        page = cursor.apply(data)
        return PaginatedResponse[SalesInvoiceSchema](data=page.items, meta=page.meta, links=page.links)

    def create_invoice(self, payload: SalesInvoiceCreate, *, idempotency_key: str | None) -> ServiceResult:
        self._ensure_scope("finance.post")
        fingerprint = payload.model_dump_json()
        self._assert_idempotency(idempotency_key, fingerprint)

        instance = SalesInvoice(
            **payload.model_dump(exclude={"line_items"}),
            tenant_id=self.principal.tenant_id,
        )
        instance.created_by = self.principal.sub
        self.repo.add(self.principal.tenant_id, instance)

        items = [self._to_line_model(item) for item in payload.line_items]
        self.repo.upsert_line_items(instance, items)
        self.repo.recompute_totals(instance)
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=SalesInvoiceSchema.model_validate(instance, from_attributes=True), etag=etag)

    def get_invoice(self, invoice_id: str) -> ServiceResult:
        self._ensure_scope("finance.read")
        instance = self.repo.get(self.principal.tenant_id, invoice_id)
        if not instance:
            raise KeyError("Invoice not found")
        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(
            data=SalesInvoiceSchema.model_validate(instance, from_attributes=True),
            etag=etag,
            version=instance.version,
            updated_at=instance.updated_at,
        )

    def update_invoice(
        self,
        invoice_id: str,
        payload: SalesInvoiceUpdate,
        *,
        if_match: str | None,
    ) -> ServiceResult:
        self._ensure_scope("finance.post")
        instance = self.repo.get(self.principal.tenant_id, invoice_id)
        if not instance:
            raise KeyError("Invoice not found")
        if if_match and if_match != self._compute_etag(instance.version, instance.updated_at):
            raise ValueError("ETag mismatch")

        for key, value in payload.model_dump(exclude_unset=True, exclude={"line_items"}).items():
            setattr(instance, key, value)
        if payload.line_items is not None:
            items = [self._to_line_model(item) for item in payload.line_items]
            self.repo.upsert_line_items(instance, items)
        self.repo.recompute_totals(instance)
        instance.updated_by = self.principal.sub
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=SalesInvoiceSchema.model_validate(instance, from_attributes=True), etag=etag)

    def delete_invoice(self, invoice_id: str, *, hard_delete: bool = False) -> None:
        self._ensure_scope("finance.post")
        instance = self.repo.get(self.principal.tenant_id, invoice_id)
        if not instance:
            raise KeyError("Invoice not found")
        if hard_delete:
            self.session.delete(instance)
        else:
            instance.is_deleted = True
            instance.version += 1
        self.session.flush()

    def _to_line_model(self, payload: SalesInvoiceLineItemCreate) -> SalesInvoiceLineItem:
        line_total = (payload.quantity * payload.unit_price) + (
            payload.quantity * payload.unit_price * payload.tax_rate / Decimal("100")
        )
        return SalesInvoiceLineItem(
            tenant_id=self.principal.tenant_id,
            product_id=str(payload.product_id) if payload.product_id else None,
            description=payload.description,
            quantity=payload.quantity,
            unit_price=payload.unit_price,
            tax_rate=payload.tax_rate,
            line_total=line_total,
        )
