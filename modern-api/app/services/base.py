"""Base service layer primitives."""
from __future__ import annotations

from dataclasses import dataclass
from datetime import datetime
from typing import Callable, Optional

from sqlalchemy.orm import Session

from app.core.security import Principal, build_etag
from app.db.models.idempotency import IdempotencyKey


@dataclass(slots=True)
class ServiceResult:
    data: object
    etag: str | None = None
    version: int | None = None
    updated_at: datetime | None = None


class BaseService:
    def __init__(self, session: Session, principal: Principal) -> None:
        self.session = session
        self.principal = principal

    def _ensure_scope(self, *scopes: str) -> None:
        from app.core.security import require_scope

        require_scope(self.principal, scopes)

    def _compute_etag(self, version: int, updated_at: datetime) -> str:
        return build_etag(version, updated_at)

    def _assert_idempotency(self, key: Optional[str], fingerprint: str) -> None:
        if not key:
            return
        existing = (
            self.session.query(IdempotencyKey)
            .filter_by(tenant_id=self.principal.tenant_id, idempotency_key=key)
            .one_or_none()
        )
        if existing:
            if existing.fingerprint != fingerprint:
                raise ValueError("Idempotency key replay with different payload")
            raise ValueError("Duplicate request: already processed")
        record = IdempotencyKey(
            tenant_id=self.principal.tenant_id,
            idempotency_key=key,
            fingerprint=fingerprint,
        )
        self.session.add(record)
