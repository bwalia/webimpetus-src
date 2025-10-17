from __future__ import annotations

from sqlalchemy.orm import Session

from app.core.security import Principal
from app.db.models.contact import Contact
from app.repositories.contacts import ContactRepository
from app.schemas.common import PaginatedResponse
from app.schemas.contacts import Contact as ContactSchema
from app.schemas.contacts import ContactCreate, ContactUpdate

from .base import BaseService, ServiceResult
from .utils.pagination import CursorPage


class ContactService(BaseService):
    def __init__(self, session: Session, principal: Principal) -> None:
        super().__init__(session, principal)
        self.repo = ContactRepository(session)

    def list_contacts(self, *, cursor: CursorPage, business_id: str | None = None) -> PaginatedResponse[ContactSchema]:
        self._ensure_scope("crm.read")
        if business_id:
            items = self.repo.list_by_business(self.principal.tenant_id, business_id)
        else:
            items = self.repo.list(self.principal.tenant_id)
        data = [ContactSchema.model_validate(item, from_attributes=True) for item in items]
        page = cursor.apply(data)
        return PaginatedResponse[ContactSchema](data=page.items, meta=page.meta, links=page.links)

    def create_contact(self, payload: ContactCreate, *, idempotency_key: str | None) -> ServiceResult:
        self._ensure_scope("crm.write")
        fingerprint = payload.model_dump_json()
        self._assert_idempotency(idempotency_key, fingerprint)

        instance = Contact(**payload.model_dump(), tenant_id=self.principal.tenant_id)
        instance.created_by = self.principal.sub
        self.repo.add(self.principal.tenant_id, instance)
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=ContactSchema.model_validate(instance, from_attributes=True), etag=etag)

    def get_contact(self, contact_id: str) -> ServiceResult:
        self._ensure_scope("crm.read")
        instance = self.repo.get(self.principal.tenant_id, contact_id)
        if not instance:
            raise KeyError("Contact not found")
        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(
            data=ContactSchema.model_validate(instance, from_attributes=True),
            etag=etag,
            version=instance.version,
            updated_at=instance.updated_at,
        )

    def update_contact(
        self,
        contact_id: str,
        payload: ContactUpdate,
        *,
        if_match: str | None,
    ) -> ServiceResult:
        self._ensure_scope("crm.write")
        instance = self.repo.get(self.principal.tenant_id, contact_id)
        if not instance:
            raise KeyError("Contact not found")
        if if_match and if_match != self._compute_etag(instance.version, instance.updated_at):
            raise ValueError("ETag mismatch")

        for key, value in payload.model_dump(exclude_unset=True).items():
            setattr(instance, key, value)
        instance.updated_by = self.principal.sub
        instance.version += 1
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=ContactSchema.model_validate(instance, from_attributes=True), etag=etag)

    def delete_contact(self, contact_id: str, *, hard_delete: bool = False) -> None:
        self._ensure_scope("crm.write")
        instance = self.repo.get(self.principal.tenant_id, contact_id)
        if not instance:
            raise KeyError("Contact not found")
        if hard_delete:
            self.session.delete(instance)
        else:
            instance.is_deleted = True
            instance.version += 1
        self.session.flush()
