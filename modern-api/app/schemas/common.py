"""Common Pydantic schemas and utilities."""
from __future__ import annotations

from datetime import datetime
from typing import Generic, List, Optional, Sequence, TypeVar
from uuid import UUID

from pydantic import BaseModel, Field
from pydantic.config import ConfigDict


class TraceableModel(BaseModel):
    trace_id: Optional[str] = Field(default=None, description="Request correlation ID")


class MetaPagination(BaseModel):
    next_cursor: Optional[str] = None
    prev_cursor: Optional[str] = None
    page_size: int


class ResponseEnvelope(BaseModel):
    data: object
    meta: Optional[dict] = None
    links: Optional[dict] = None


class ErrorDetail(BaseModel):
    code: str
    message: str
    details: Optional[dict] = None
    trace_id: Optional[str] = None


ModelT = TypeVar("ModelT", bound=BaseModel)


class PaginatedResponse(BaseModel, Generic[ModelT]):
    data: Sequence[ModelT]
    meta: MetaPagination
    links: Optional[dict] = None


class AuditFields(BaseModel):
    id: UUID
    tenant_id: UUID
    created_at: datetime
    updated_at: datetime
    created_by: Optional[UUID]
    updated_by: Optional[UUID]
    version: int
    is_deleted: bool

    model_config = ConfigDict(from_attributes=True)


class MutationResult(BaseModel, Generic[ModelT]):
    data: ModelT
    etag: str
