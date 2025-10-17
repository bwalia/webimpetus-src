"""Simple cursor pagination utilities."""
from __future__ import annotations

import base64
import json
from dataclasses import dataclass
from typing import Generic, List, Sequence, TypeVar

from pydantic import BaseModel

from app.schemas.common import MetaPagination

ModelT = TypeVar("ModelT")


@dataclass
class CursorPage(Generic[ModelT]):
    cursor: str | None
    page_size: int = 50

    def apply(self, items: Sequence[ModelT]) -> "PaginatedSlice[ModelT]":
        start_index = 0
        if self.cursor:
            decoded = json.loads(base64.urlsafe_b64decode(self.cursor + "==").decode())
            start_index = decoded.get("offset", 0)
        sliced = list(items)[start_index : start_index + self.page_size]
        next_cursor = None
        if start_index + self.page_size < len(items):
            payload = json.dumps({"offset": start_index + self.page_size}).encode()
            next_cursor = base64.urlsafe_b64encode(payload).decode().rstrip("=")
        meta = MetaPagination(next_cursor=next_cursor, prev_cursor=None, page_size=self.page_size)
        links = {"next": next_cursor}
        return PaginatedSlice(items=sliced, meta=meta, links=links)


@dataclass
class PaginatedSlice(Generic[ModelT]):
    items: Sequence[ModelT]
    meta: MetaPagination
    links: dict | None
