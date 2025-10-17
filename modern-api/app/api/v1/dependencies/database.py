"""Provide a SQLAlchemy session per request."""
from __future__ import annotations

from typing import Annotated

from fastapi import Depends

from app.db.session import SessionLocal


def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
