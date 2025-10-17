from __future__ import annotations

from sqlalchemy.orm import Session

from app.core.security import Principal
from app.repositories.tasks import TaskRepository
from app.schemas.common import PaginatedResponse
from app.schemas.tasks import Task as TaskSchema
from app.schemas.tasks import TaskCreate, TaskUpdate

from .base import BaseService, ServiceResult
from .utils.pagination import CursorPage


class TaskService(BaseService):
    def __init__(self, session: Session, principal: Principal) -> None:
        super().__init__(session, principal)
        self.repo = TaskRepository(session)

    def list_tasks(self, *, cursor: CursorPage, project_id: str | None = None) -> PaginatedResponse[TaskSchema]:
        self._ensure_scope("crm.read")
        if project_id:
            items = self.repo.list_by_project(self.principal.tenant_id, project_id)
        else:
            items = self.repo.list(self.principal.tenant_id)
        data = [TaskSchema.model_validate(item, from_attributes=True) for item in items]
        page = cursor.apply(data)
        return PaginatedResponse[TaskSchema](data=page.items, meta=page.meta, links=page.links)

    def create_task(self, payload: TaskCreate, *, idempotency_key: str | None) -> ServiceResult:
        self._ensure_scope("crm.write")
        fingerprint = payload.model_dump_json()
        self._assert_idempotency(idempotency_key, fingerprint)

        from app.db.models.task import Task

        instance = Task(**payload.model_dump(), tenant_id=self.principal.tenant_id)
        instance.created_by = self.principal.sub
        self.repo.add(self.principal.tenant_id, instance)
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=TaskSchema.model_validate(instance, from_attributes=True), etag=etag)

    def get_task(self, task_id: str) -> ServiceResult:
        self._ensure_scope("crm.read")
        instance = self.repo.get(self.principal.tenant_id, task_id)
        if not instance:
            raise KeyError("Task not found")
        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(
            data=TaskSchema.model_validate(instance, from_attributes=True),
            etag=etag,
            version=instance.version,
            updated_at=instance.updated_at,
        )

    def update_task(self, task_id: str, payload: TaskUpdate, *, if_match: str | None) -> ServiceResult:
        self._ensure_scope("crm.write")
        instance = self.repo.get(self.principal.tenant_id, task_id)
        if not instance:
            raise KeyError("Task not found")
        if if_match and if_match != self._compute_etag(instance.version, instance.updated_at):
            raise ValueError("ETag mismatch")

        for key, value in payload.model_dump(exclude_unset=True).items():
            setattr(instance, key, value)
        instance.updated_by = self.principal.sub
        instance.version += 1
        self.session.flush()

        etag = self._compute_etag(instance.version, instance.updated_at)
        return ServiceResult(data=TaskSchema.model_validate(instance, from_attributes=True), etag=etag)

    def delete_task(self, task_id: str, *, hard_delete: bool = False) -> None:
        self._ensure_scope("crm.write")
        instance = self.repo.get(self.principal.tenant_id, task_id)
        if not instance:
            raise KeyError("Task not found")
        if hard_delete:
            self.session.delete(instance)
        else:
            instance.is_deleted = True
            instance.version += 1
        self.session.flush()
