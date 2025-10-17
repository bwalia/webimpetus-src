"""FastAPI dependencies for authentication and authorization."""
from __future__ import annotations

from typing import Annotated

from fastapi import Depends, Header, HTTPException, status

from app.core.security import Principal, TokenVerifier


def get_current_principal(authorization: Annotated[str | None, Header()] = None) -> Principal:
    if not authorization or not authorization.lower().startswith("bearer "):
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Missing bearer token")
    token = authorization.split(" ", 1)[1]
    verifier = TokenVerifier()
    try:
        return verifier.decode(token)
    except PermissionError as exc:
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail=str(exc)) from exc
