from __future__ import annotations

from typing import Sequence

from sqlalchemy import select

from app.db.models.business import Business

from .base import BaseRepository


class BusinessRepository(BaseRepository[Business]):
    model = Business

    def find_by_code(self, tenant_id: str, code: str) -> Business | None:
        stmt = select(Business).where(
            Business.tenant_id == tenant_id,
            Business.code == code,
            Business.is_deleted == False,
        )
        result = self.session.execute(stmt).scalar_one_or_none()
        return result

    def list_filtered(self, tenant_id: str, *, status: str | None = None) -> Sequence[Business]:
        stmt = select(Business).where(Business.tenant_id == tenant_id, Business.is_deleted == False)
        if status:
            stmt = stmt.where(Business.status == status)
        return [row[0] for row in self.session.execute(stmt).all()]
