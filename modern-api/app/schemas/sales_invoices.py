from __future__ import annotations

from decimal import Decimal
from typing import List, Optional
from uuid import UUID

from pydantic import BaseModel, Field
from pydantic.config import ConfigDict

from .common import AuditFields


class SalesInvoiceLineItemBase(BaseModel):
    product_id: Optional[UUID] = None
    description: str
    quantity: Decimal = Field(gt=0)
    unit_price: Decimal = Field(ge=0)
    tax_rate: Decimal = Field(ge=0)


class SalesInvoiceLineItemCreate(SalesInvoiceLineItemBase):
    pass


class SalesInvoiceLineItem(SalesInvoiceLineItemBase, AuditFields):
    line_total: Decimal
    model_config = ConfigDict(from_attributes=True)


class SalesInvoiceBase(BaseModel):
    business_id: UUID
    customer_id: UUID
    number: str
    currency: str = Field(default="GBP", min_length=3, max_length=3)
    exchange_rate: Decimal = Field(default=1, gt=0)
    issue_date: str = Field(pattern=r"^\\d{4}-\\d{2}-\\d{2}$")
    due_date: str = Field(pattern=r"^\\d{4}-\\d{2}-\\d{2}$")
    status: str = Field(default="draft")
    line_items: List[SalesInvoiceLineItemCreate]


class SalesInvoiceCreate(SalesInvoiceBase):
    pass


class SalesInvoiceUpdate(BaseModel):
    number: Optional[str] = None
    currency: Optional[str] = None
    exchange_rate: Optional[Decimal] = None
    issue_date: Optional[str] = None
    due_date: Optional[str] = None
    status: Optional[str] = None
    line_items: Optional[List[SalesInvoiceLineItemCreate]] = None


class SalesInvoice(SalesInvoiceBase, AuditFields):
    subtotal: Decimal
    tax_total: Decimal
    total: Decimal
    balance_due: Decimal
    line_items: List[SalesInvoiceLineItem]
    model_config = ConfigDict(from_attributes=True)
