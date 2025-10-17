from __future__ import annotations

from sqlalchemy.orm import Session

from app.core.security import Principal
from app.db.models.business import Business as BusinessModel
from app.repositories.businesses import BusinessRepository
from app.schemas.businesses import Business as BusinessSchema
from app.schemas.businesses import BusinessCreate, BusinessUpdate
from app.schemas.common import PaginatedResponse

from .base import BaseService, ServiceResult
from .utils.pagination import CursorPage


class BusinessService(BaseService):
    def __init__(self, session: Session, principal: Principal) -> None:
        super().__init__(session, principal)
        self.repo = BusinessRepository(session)

    def list_businesses(self, *, cursor: CursorPage, status: str | None = None) -> PaginatedResponse[BusinessSchema]:
        self._ensure_scope("crm.read")
        items = self.repo.list_filtered(self.principal.tenant_id, status=status)
        data = [BusinessSchema.model_validate(item, from_attributes=True) for item in items]
        page = cursor.apply(data)
        return PaginatedResponse[BusinessSchema](data=page.items, meta=page.meta, links=page.links)

    def create_business(self, payload: BusinessCreate, *, idempotency_key: str | None) -> ServiceResult:
        self._ensure_scope("crm.write")
        fingerprint = payload.model_dump_json()
        self._assert_idempotency(idempotency_key, fingerprint)

        instance = BusinessModel(**payload.model_dump(), tenant_id=self.principal.tenant_id)
        instance.created_by = self.principal.sub
        self.repo.add(self.principal.tenant_id, instance)
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=BusinessSchema.model_validate(instance, from_attributes=True), etag=etag)

    def get_business(self, business_id: str) -> ServiceResult:
        self._ensure_scope("crm.read")
        instance = self.repo.get(self.principal.tenant_id, business_id)
        if not instance:
            raise KeyError("Business not found")
        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(
            data=BusinessSchema.model_validate(instance, from_attributes=True),
            etag=etag,
            version=instance.version,
            updated_at=instance.updated_at,
        )

    def update_business(
        self,
        business_id: str,
        payload: BusinessUpdate,
        *,
        if_match: str | None,
    ) -> ServiceResult:
        self._ensure_scope("crm.write")
        instance = self.repo.get(self.principal.tenant_id, business_id)
        if not instance:
            raise KeyError("Business not found")
        if if_match and if_match != self._compute_etag(instance.version, instance.updated_at):
            raise ValueError("ETag mismatch")

        for key, value in payload.model_dump(exclude_unset=True).items():
            setattr(instance, key, value)
        instance.updated_by = self.principal.sub
        instance.version += 1
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=BusinessSchema.model_validate(instance, from_attributes=True), etag=etag)

    def delete_business(self, business_id: str, *, hard_delete: bool = False) -> None:
        self._ensure_scope("crm.write")
        instance = self.repo.get(self.principal.tenant_id, business_id)
        if not instance:
            raise KeyError("Business not found")
        if hard_delete:
            self.session.delete(instance)
        else:
            instance.is_deleted = True
            instance.version += 1
        self.session.flush()
