from __future__ import annotations

from typing import Sequence

from sqlalchemy import select

from app.db.models.task import Task

from .base import BaseRepository


class TaskRepository(BaseRepository[Task]):
    model = Task

    def list_by_project(self, tenant_id: str, project_id: str) -> Sequence[Task]:
        stmt = select(Task).where(
            Task.tenant_id == tenant_id,
            Task.project_id == project_id,
            Task.is_deleted == False,
        )
        return [row[0] for row in self.session.execute(stmt).all()]
