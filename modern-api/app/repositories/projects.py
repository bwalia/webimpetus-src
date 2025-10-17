from __future__ import annotations

from typing import Sequence

from sqlalchemy import select

from app.db.models.project import Project

from .base import BaseRepository


class ProjectRepository(BaseRepository[Project]):
    model = Project

    def list_by_business(self, tenant_id: str, business_id: str) -> Sequence[Project]:
        stmt = select(Project).where(
            Project.tenant_id == tenant_id,
            Project.business_id == business_id,
            Project.is_deleted == False,
        )
        return [row[0] for row in self.session.execute(stmt).all()]
