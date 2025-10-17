"""Common dependencies for pagination, idempotency and concurrency."""
from __future__ import annotations

from typing import Annotated, Optional

from fastapi import Depends, Header, HTTPException, Query, status

from app.services.utils.pagination import CursorPage


def get_cursor(page_cursor: Annotated[str | None, Query(alias="page[cursor]")]=None, page_size: Annotated[int | None, Query(alias="page[size]")]=None) -> CursorPage:
    size = page_size or 50
    if size > 200:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="page[size] too large")
    return CursorPage(cursor=page_cursor, page_size=size)


def get_idempotency_key(idempotency_key: Annotated[str | None, Header(alias="Idempotency-Key", convert_underscores=False)] = None) -> str | None:
    return idempotency_key


def get_if_match(if_match: Annotated[str | None, Header(alias="If-Match", convert_underscores=False)] = None) -> str | None:
    return if_match
