from __future__ import annotations

from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Response, status
from sqlalchemy.orm import Session

from app.api.v1.dependencies.auth import get_current_principal
from app.api.v1.dependencies.common import get_cursor, get_idempotency_key, get_if_match
from app.api.v1.dependencies.database import get_db
from app.core.security import Principal
from app.schemas.common import PaginatedResponse
from app.schemas.journal_entries import JournalEntry, JournalEntryCreate, JournalEntryUpdate
from app.services.journal_entries import JournalEntryService
from app.services.utils.pagination import CursorPage

router = APIRouter(prefix="/journal-entries", tags=["Journal Entries"])


@router.get("", response_model=PaginatedResponse[JournalEntry])
def list_entries(
    cursor: Annotated[CursorPage, Depends(get_cursor)],
    business_id: str | None = None,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = JournalEntryService(db, principal)
    return service.list_entries(cursor=cursor, business_id=business_id)


@router.post("", response_model=JournalEntry, status_code=status.HTTP_201_CREATED)
def create_entry(
    payload: JournalEntryCreate,
    response: Response,
    idempotency_key: Annotated[str | None, Depends(get_idempotency_key)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = JournalEntryService(db, principal)
    try:
        result = service.create_entry(payload, idempotency_key=idempotency_key)
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_409_CONFLICT, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.get("/{entry_id}", response_model=JournalEntry)
def get_entry(
    entry_id: str,
    response: Response,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = JournalEntryService(db, principal)
    try:
        result = service.get_entry(entry_id)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Journal entry not found")
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.patch("/{entry_id}", response_model=JournalEntry)
def update_entry(
    entry_id: str,
    payload: JournalEntryUpdate,
    response: Response,
    if_match: Annotated[str | None, Depends(get_if_match)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = JournalEntryService(db, principal)
    try:
        result = service.update_entry(entry_id, payload, if_match=if_match)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Journal entry not found")
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_412_PRECONDITION_FAILED, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.delete("/{entry_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_entry(
    entry_id: str,
    hard_delete: bool = False,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = JournalEntryService(db, principal)
    try:
        service.delete_entry(entry_id, hard_delete=hard_delete)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Journal entry not found")
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_409_CONFLICT, detail=str(exc))
