"""Idempotency key storage."""
from __future__ import annotations

from sqlalchemy import String, UniqueConstraint
from sqlalchemy.orm import Mapped, mapped_column

from .base import Base, BaseUUIDMixin


class IdempotencyKey(Base, BaseUUIDMixin):
    __tablename__ = "idempotency_keys"
    __table_args__ = (UniqueConstraint("tenant_id", "idempotency_key", name="uq_idempotency_tenant_key"),)

    idempotency_key: Mapped[str] = mapped_column(String(128), nullable=False)
    fingerprint: Mapped[str] = mapped_column(String(128), nullable=False)
