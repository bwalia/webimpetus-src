from __future__ import annotations

from sqlalchemy.orm import Session

from app.core.security import Principal
from app.db.models.accounting.journal_entry import JournalEntry, JournalLine
from app.repositories.journal_entries import JournalEntryRepository
from app.schemas.common import PaginatedResponse
from app.schemas.journal_entries import (
    JournalEntry as JournalEntrySchema,
    JournalEntryCreate,
    JournalEntryUpdate,
    JournalLineCreate,
)

from .base import BaseService, ServiceResult
from .utils.pagination import CursorPage


class JournalEntryService(BaseService):
    def __init__(self, session: Session, principal: Principal) -> None:
        super().__init__(session, principal)
        self.repo = JournalEntryRepository(session)

    def list_entries(self, *, cursor: CursorPage, business_id: str | None = None) -> PaginatedResponse[JournalEntrySchema]:
        self._ensure_scope("finance.read")
        if business_id:
            items = [
                entry
                for entry in self.repo.list(self.principal.tenant_id)
                if entry.business_id == business_id
            ]
        else:
            items = self.repo.list(self.principal.tenant_id)
        data = [JournalEntrySchema.model_validate(item, from_attributes=True) for item in items]
        page = cursor.apply(data)
        return PaginatedResponse[JournalEntrySchema](data=page.items, meta=page.meta, links=page.links)

    def create_entry(self, payload: JournalEntryCreate, *, idempotency_key: str | None) -> ServiceResult:
        self._ensure_scope("finance.post")
        fingerprint = payload.model_dump_json()
        self._assert_idempotency(idempotency_key, fingerprint)

        if self.repo.is_period_locked(self.principal.tenant_id, payload.period):
            raise ValueError("Accounting period is locked")

        entry = JournalEntry(
            **payload.model_dump(exclude={"lines"}),
            tenant_id=self.principal.tenant_id,
        )
        entry.created_by = self.principal.sub
        entry.lines = [self._to_line_model(line) for line in payload.lines]
        self.repo.validate_balanced(entry)
        self.repo.add(self.principal.tenant_id, entry)
        self.session.flush()

        etag = self._compute_etag(entry.version, entry.updated_at)
        return ServiceResult(data=JournalEntrySchema.model_validate(entry, from_attributes=True), etag=etag)

    def get_entry(self, entry_id: str) -> ServiceResult:
        self._ensure_scope("finance.read")
        entry = self.repo.get(self.principal.tenant_id, entry_id)
        if not entry:
            raise KeyError("Journal entry not found")
        etag = self._compute_etag(entry.version, entry.updated_at)
        return ServiceResult(
            data=JournalEntrySchema.model_validate(entry, from_attributes=True),
            etag=etag,
            version=entry.version,
            updated_at=entry.updated_at,
        )

    def update_entry(self, entry_id: str, payload: JournalEntryUpdate, *, if_match: str | None) -> ServiceResult:
        self._ensure_scope("finance.post")
        entry = self.repo.get(self.principal.tenant_id, entry_id)
        if not entry:
            raise KeyError("Journal entry not found")
        if if_match and if_match != self._compute_etag(entry.version, entry.updated_at):
            raise ValueError("ETag mismatch")
        if entry.is_locked:
            raise ValueError("Entry is locked")
        if payload.period and self.repo.is_period_locked(self.principal.tenant_id, payload.period):
            raise ValueError("Accounting period is locked")

        for key, value in payload.model_dump(exclude_unset=True, exclude={"lines"}).items():
            setattr(entry, key, value)
        if payload.lines is not None:
            new_lines = [self._to_line_model(line) for line in payload.lines]
            entry.lines = new_lines
        self.repo.validate_balanced(entry)
        entry.version += 1
        self.session.flush()

        etag = self._compute_etag(entry.version, entry.updated_at)
        return ServiceResult(data=JournalEntrySchema.model_validate(entry, from_attributes=True), etag=etag)

    def delete_entry(self, entry_id: str, *, hard_delete: bool = False) -> None:
        self._ensure_scope("finance.post")
        entry = self.repo.get(self.principal.tenant_id, entry_id)
        if not entry:
            raise KeyError("Journal entry not found")
        if entry.is_locked:
            raise ValueError("Entry is locked")
        if hard_delete:
            self.session.delete(entry)
        else:
            entry.is_deleted = True
            entry.version += 1
        self.session.flush()

    def _to_line_model(self, payload: JournalLineCreate) -> JournalLine:
        return JournalLine(
            tenant_id=self.principal.tenant_id,
            account_code=payload.account_code,
            description=payload.description,
            debit=payload.debit,
            credit=payload.credit,
        )
