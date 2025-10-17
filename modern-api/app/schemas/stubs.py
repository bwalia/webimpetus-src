from __future__ import annotations

from datetime import datetime
from uuid import UUID, uuid4

from pydantic import BaseModel, Field


class StubBase(BaseModel):
    id: UUID = Field(default_factory=uuid4)
    tenant_id: UUID
    name: str | None = None
    status: str | None = None
    created_at: datetime | None = None
    updated_at: datetime | None = None


class PurchaseInvoiceStub(StubBase):
    supplier_id: UUID | None = None
    currency: str | None = None


class PurchaseOrderStub(StubBase):
    supplier_id: UUID | None = None


class ReceiptStub(StubBase):
    amount: float | None = None


class PaymentStub(StubBase):
    amount: float | None = None


class ProductStub(StubBase):
    sku: str | None = None


class CategoryStub(StubBase):
    slug: str | None = None


class TaxStub(StubBase):
    rate: float | None = None


class TenantStub(StubBase):
    domain: str | None = None


class RoleStub(StubBase):
    permissions: list[str] | None = None


class EmployeeStub(StubBase):
    email: str | None = None


class DomainStub(StubBase):
    fqdn: str | None = None


class SecretStub(StubBase):
    key_id: str | None = None


class UserBusinessStub(StubBase):
    user_id: UUID | None = None


class LaunchpadStub(StubBase):
    url: str | None = None


class DocumentStub(StubBase):
    file_name: str | None = None


class BookmarkStub(StubBase):
    target_url: str | None = None


class JobStub(StubBase):
    state: str | None = None


class ServiceStub(StubBase):
    endpoint: str | None = None


class EnquiryStub(StubBase):
    contact_id: UUID | None = None


class AccountStub(StubBase):
    account_code: str | None = None


class WebpageStub(StubBase):
    slug: str | None = None


class BlockStub(StubBase):
    content_type: str | None = None


class TemplateStub(StubBase):
    version: str | None = None


class KnowledgeBaseStub(StubBase):
    category: str | None = None


class BlogStub(StubBase):
    slug: str | None = None


class GalleryStub(StubBase):
    asset_count: int | None = None


class SprintStub(StubBase):
    start_date: datetime | None = None


class EmailCampaignStub(StubBase):
    scheduled_at: datetime | None = None


class TagStub(StubBase):
    color: str | None = None


class VatReturnStub(StubBase):
    period: str | None = None


class TrialBalanceStub(StubBase):
    as_of: datetime | None = None


class ProfitLossStub(StubBase):
    period_start: datetime | None = None


class BalanceSheetStub(StubBase):
    as_of: datetime | None = None


class WebhookStub(StubBase):
    callback_url: str | None = None
