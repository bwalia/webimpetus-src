"""Placeholder routers for resources awaiting full implementation."""

from __future__ import annotations

from app.schemas import stubs as stub_schemas

from .stubs import build_stub_router

purchase_invoices_router = build_stub_router(
    prefix="/purchase-invoices",
    tag="Purchase Invoices",
    model=stub_schemas.PurchaseInvoiceStub,
)

purchase_orders_router = build_stub_router(
    prefix="/purchase-orders",
    tag="Purchase Orders",
    model=stub_schemas.PurchaseOrderStub,
)

receipts_router = build_stub_router(
    prefix="/receipts",
    tag="Receipts",
    model=stub_schemas.ReceiptStub,
)

payments_router = build_stub_router(
    prefix="/payments",
    tag="Payments",
    model=stub_schemas.PaymentStub,
)

products_router = build_stub_router(
    prefix="/products",
    tag="Products",
    model=stub_schemas.ProductStub,
)

categories_router = build_stub_router(
    prefix="/categories",
    tag="Categories",
    model=stub_schemas.CategoryStub,
)

taxes_router = build_stub_router(
    prefix="/taxes",
    tag="Taxes",
    model=stub_schemas.TaxStub,
)

tenants_router = build_stub_router(
    prefix="/tenants",
    tag="Tenants",
    model=stub_schemas.TenantStub,
)

roles_router = build_stub_router(
    prefix="/roles",
    tag="Roles",
    model=stub_schemas.RoleStub,
)

employees_router = build_stub_router(
    prefix="/employees",
    tag="Employees",
    model=stub_schemas.EmployeeStub,
)

domains_router = build_stub_router(
    prefix="/domains",
    tag="Domains",
    model=stub_schemas.DomainStub,
)

secrets_router = build_stub_router(
    prefix="/secrets",
    tag="Secrets",
    model=stub_schemas.SecretStub,
)

user_businesses_router = build_stub_router(
    prefix="/user-businesses",
    tag="User Businesses",
    model=stub_schemas.UserBusinessStub,
)

launchpad_router = build_stub_router(
    prefix="/launchpad",
    tag="Launchpad",
    model=stub_schemas.LaunchpadStub,
)

documents_router = build_stub_router(
    prefix="/documents",
    tag="Documents",
    model=stub_schemas.DocumentStub,
)

bookmarks_router = build_stub_router(
    prefix="/bookmarks",
    tag="Bookmarks",
    model=stub_schemas.BookmarkStub,
)

jobs_router = build_stub_router(
    prefix="/jobs",
    tag="Jobs",
    model=stub_schemas.JobStub,
)

services_router = build_stub_router(
    prefix="/services",
    tag="Services",
    model=stub_schemas.ServiceStub,
)

enquiries_router = build_stub_router(
    prefix="/enquiries",
    tag="Enquiries",
    model=stub_schemas.EnquiryStub,
)

accounts_router = build_stub_router(
    prefix="/accounts",
    tag="Accounts",
    model=stub_schemas.AccountStub,
)

webpages_router = build_stub_router(
    prefix="/webpages",
    tag="Webpages",
    model=stub_schemas.WebpageStub,
)

blocks_router = build_stub_router(
    prefix="/blocks",
    tag="Blocks",
    model=stub_schemas.BlockStub,
)

templates_router = build_stub_router(
    prefix="/templates",
    tag="Templates",
    model=stub_schemas.TemplateStub,
)

knowledge_base_router = build_stub_router(
    prefix="/knowledge-base",
    tag="Knowledge Base",
    model=stub_schemas.KnowledgeBaseStub,
)

blogs_router = build_stub_router(
    prefix="/blogs",
    tag="Blogs",
    model=stub_schemas.BlogStub,
)

galleries_router = build_stub_router(
    prefix="/galleries",
    tag="Galleries",
    model=stub_schemas.GalleryStub,
)

sprints_router = build_stub_router(
    prefix="/sprints",
    tag="Sprints",
    model=stub_schemas.SprintStub,
)

email_campaigns_router = build_stub_router(
    prefix="/email-campaigns",
    tag="Email Campaigns",
    model=stub_schemas.EmailCampaignStub,
)

tags_router = build_stub_router(
    prefix="/tags",
    tag="Tags",
    model=stub_schemas.TagStub,
)

vat_returns_router = build_stub_router(
    prefix="/vat-returns",
    tag="VAT Returns",
    model=stub_schemas.VatReturnStub,
)

trial_balance_router = build_stub_router(
    prefix="/reports/trial-balance",
    tag="Trial Balance",
    model=stub_schemas.TrialBalanceStub,
)

profit_loss_router = build_stub_router(
    prefix="/reports/profit-loss",
    tag="Profit & Loss",
    model=stub_schemas.ProfitLossStub,
)

balance_sheet_router = build_stub_router(
    prefix="/reports/balance-sheet",
    tag="Balance Sheet",
    model=stub_schemas.BalanceSheetStub,
)

webhooks_router = build_stub_router(
    prefix="/webhooks",
    tag="Webhooks",
    model=stub_schemas.WebhookStub,
)

__all__ = [
    "purchase_invoices_router",
    "purchase_orders_router",
    "receipts_router",
    "payments_router",
    "products_router",
    "categories_router",
    "taxes_router",
    "tenants_router",
    "roles_router",
    "employees_router",
    "domains_router",
    "secrets_router",
    "user_businesses_router",
    "launchpad_router",
    "documents_router",
    "bookmarks_router",
    "jobs_router",
    "services_router",
    "enquiries_router",
    "accounts_router",
    "webpages_router",
    "blocks_router",
    "templates_router",
    "knowledge_base_router",
    "blogs_router",
    "galleries_router",
    "sprints_router",
    "email_campaigns_router",
    "tags_router",
    "vat_returns_router",
    "trial_balance_router",
    "profit_loss_router",
    "balance_sheet_router",
    "webhooks_router",
]
