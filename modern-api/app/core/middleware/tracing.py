"""Middleware that ensures every request has a trace ID."""
from __future__ import annotations

import uuid
from typing import Callable

from fastapi import Request
from starlette.middleware.base import BaseHTTPMiddleware

TRACE_HEADER = "X-Trace-Id"


class TraceMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next: Callable):
        trace_id = request.headers.get(TRACE_HEADER, str(uuid.uuid4()))
        request.state.trace_id = trace_id

        response = await call_next(request)
        response.headers.setdefault(TRACE_HEADER, trace_id)
        return response
