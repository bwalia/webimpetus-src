from __future__ import annotations

from typing import Optional
from uuid import UUID

from pydantic import BaseModel, Field
from pydantic.config import ConfigDict

from .common import AuditFields


class ProjectBase(BaseModel):
    business_id: UUID
    name: str = Field(max_length=200)
    description: Optional[str] = Field(default=None, max_length=1000)
    status: str = Field(default="planning")
    start_date: Optional[str] = Field(default=None, pattern=r"^\\d{4}-\\d{2}-\\d{2}$")
    end_date: Optional[str] = Field(default=None, pattern=r"^\\d{4}-\\d{2}-\\d{2}$")


class ProjectCreate(ProjectBase):
    pass


class ProjectUpdate(BaseModel):
    name: Optional[str] = None
    description: Optional[str] = None
    status: Optional[str] = None
    start_date: Optional[str] = None
    end_date: Optional[str] = None


class Project(ProjectBase, AuditFields):
    model_config = ConfigDict(from_attributes=True)
