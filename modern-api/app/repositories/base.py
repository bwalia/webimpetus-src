"""Base repository with multi-tenant safeguards."""
from __future__ import annotations

from typing import Generic, Iterable, Optional, Sequence, TypeVar

from sqlalchemy import select
from sqlalchemy.orm import Session

from app.db.models.base import BaseUUIDMixin

ModelT = TypeVar("ModelT", bound=BaseUUIDMixin)


class TenantEnforcementError(RuntimeError):
    pass


class BaseRepository(Generic[ModelT]):
    model: type[ModelT]

    def __init__(self, session: Session) -> None:
        self.session = session

    def _query(self, tenant_id: str):
        return self.session.execute(select(self.model).where(self.model.tenant_id == tenant_id, self.model.is_deleted == False))

    def list(self, tenant_id: str) -> Sequence[ModelT]:
        result = self._query(tenant_id)
        return [row[0] for row in result.all()]

    def get(self, tenant_id: str, obj_id: str) -> Optional[ModelT]:
        result = self.session.get(self.model, obj_id)
        if result is None or result.tenant_id != tenant_id or result.is_deleted:
            return None
        return result

    def add(self, tenant_id: str, obj: ModelT) -> ModelT:
        if obj.tenant_id != tenant_id:
            raise TenantEnforcementError("tenant_id mismatch")
        self.session.add(obj)
        return obj

    def soft_delete(self, tenant_id: str, obj_id: str) -> bool:
        obj = self.get(tenant_id, obj_id)
        if not obj:
            return False
        obj.is_deleted = True
        obj.version += 1
        return True

    def apply_patch(self, obj: ModelT, data: dict) -> ModelT:
        for key, value in data.items():
            if hasattr(obj, key) and key not in {"id", "tenant_id"}:
                setattr(obj, key, value)
        obj.version += 1
        return obj
