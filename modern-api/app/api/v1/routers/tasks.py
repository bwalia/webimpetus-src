from __future__ import annotations

from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Response, status
from sqlalchemy.orm import Session

from app.api.v1.dependencies.auth import get_current_principal
from app.api.v1.dependencies.common import get_cursor, get_idempotency_key, get_if_match
from app.api.v1.dependencies.database import get_db
from app.core.security import Principal
from app.schemas.common import PaginatedResponse
from app.schemas.tasks import Task, TaskCreate, TaskUpdate
from app.services.tasks import TaskService
from app.services.utils.pagination import CursorPage

router = APIRouter(prefix="/tasks", tags=["Tasks"])


@router.get("", response_model=PaginatedResponse[Task])
def list_tasks(
    cursor: Annotated[CursorPage, Depends(get_cursor)],
    project_id: str | None = None,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TaskService(db, principal)
    return service.list_tasks(cursor=cursor, project_id=project_id)


@router.post("", response_model=Task, status_code=status.HTTP_201_CREATED)
def create_task(
    payload: TaskCreate,
    response: Response,
    idempotency_key: Annotated[str | None, Depends(get_idempotency_key)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TaskService(db, principal)
    try:
        result = service.create_task(payload, idempotency_key=idempotency_key)
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_409_CONFLICT, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.get("/{task_id}", response_model=Task)
def get_task(
    task_id: str,
    response: Response,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TaskService(db, principal)
    try:
        result = service.get_task(task_id)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Task not found")
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.patch("/{task_id}", response_model=Task)
def update_task(
    task_id: str,
    payload: TaskUpdate,
    response: Response,
    if_match: Annotated[str | None, Depends(get_if_match)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TaskService(db, principal)
    try:
        result = service.update_task(task_id, payload, if_match=if_match)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Task not found")
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_412_PRECONDITION_FAILED, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.delete("/{task_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_task(
    task_id: str,
    hard_delete: bool = False,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = TaskService(db, principal)
    try:
        service.delete_task(task_id, hard_delete=hard_delete)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Task not found")
