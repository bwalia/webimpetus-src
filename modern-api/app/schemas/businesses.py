from __future__ import annotations

from typing import Optional
from uuid import UUID

from pydantic import BaseModel, Field
from pydantic.config import ConfigDict

from .common import AuditFields


class BusinessBase(BaseModel):
    name: str = Field(max_length=200)
    code: str = Field(max_length=50)
    status: str = Field(default="active")
    industry: Optional[str] = Field(default=None, max_length=100)
    website: Optional[str] = Field(default=None, max_length=255)


class BusinessCreate(BusinessBase):
    pass


class BusinessUpdate(BaseModel):
    name: Optional[str] = None
    status: Optional[str] = None
    industry: Optional[str] = None
    website: Optional[str] = None


class Business(BusinessBase, AuditFields):
    model_config = ConfigDict(from_attributes=True)
