from __future__ import annotations

from typing import Optional
from uuid import UUID

from pydantic import BaseModel, Field
from pydantic.config import ConfigDict

from .common import AuditFields


class TaskBase(BaseModel):
    project_id: UUID
    name: str = Field(max_length=200)
    description: Optional[str] = Field(default=None)
    status: str = Field(default="todo")
    assignee_id: Optional[UUID] = None
    labels: Optional[list[str]] = None
    due_date: Optional[str] = Field(default=None, pattern=r"^\\d{4}-\\d{2}-\\d{2}$")


class TaskCreate(TaskBase):
    pass


class TaskUpdate(BaseModel):
    name: Optional[str] = None
    description: Optional[str] = None
    status: Optional[str] = None
    assignee_id: Optional[UUID] = None
    labels: Optional[list[str]] = None
    due_date: Optional[str] = None


class Task(TaskBase, AuditFields):
    model_config = ConfigDict(from_attributes=True)
