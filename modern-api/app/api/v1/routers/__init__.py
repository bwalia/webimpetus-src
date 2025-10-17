"""API v1 routers package."""

from fastapi import APIRouter

from . import (
    businesses,
    contacts,
    journal_entries,
    projects,
    sales_invoices,
    stubbed_resources,
    tasks,
    timesheets,
)

router = APIRouter(prefix="/api/v1")

router.include_router(businesses.router)
router.include_router(contacts.router)
router.include_router(projects.router)
router.include_router(tasks.router)
router.include_router(timesheets.router)
router.include_router(sales_invoices.router)
router.include_router(journal_entries.router)

router.include_router(stubbed_resources.purchase_invoices_router)
router.include_router(stubbed_resources.purchase_orders_router)
router.include_router(stubbed_resources.receipts_router)
router.include_router(stubbed_resources.payments_router)
router.include_router(stubbed_resources.products_router)
router.include_router(stubbed_resources.categories_router)
router.include_router(stubbed_resources.taxes_router)
router.include_router(stubbed_resources.tenants_router)
router.include_router(stubbed_resources.roles_router)
router.include_router(stubbed_resources.employees_router)
router.include_router(stubbed_resources.domains_router)
router.include_router(stubbed_resources.secrets_router)
router.include_router(stubbed_resources.user_businesses_router)
router.include_router(stubbed_resources.launchpad_router)
router.include_router(stubbed_resources.documents_router)
router.include_router(stubbed_resources.bookmarks_router)
router.include_router(stubbed_resources.jobs_router)
router.include_router(stubbed_resources.services_router)
router.include_router(stubbed_resources.enquiries_router)
router.include_router(stubbed_resources.accounts_router)
router.include_router(stubbed_resources.webpages_router)
router.include_router(stubbed_resources.blocks_router)
router.include_router(stubbed_resources.templates_router)
router.include_router(stubbed_resources.knowledge_base_router)
router.include_router(stubbed_resources.blogs_router)
router.include_router(stubbed_resources.galleries_router)
router.include_router(stubbed_resources.sprints_router)
router.include_router(stubbed_resources.email_campaigns_router)
router.include_router(stubbed_resources.tags_router)
router.include_router(stubbed_resources.vat_returns_router)
router.include_router(stubbed_resources.trial_balance_router)
router.include_router(stubbed_resources.profit_loss_router)
router.include_router(stubbed_resources.balance_sheet_router)
router.include_router(stubbed_resources.webhooks_router)
