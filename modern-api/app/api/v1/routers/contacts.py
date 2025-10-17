from __future__ import annotations

from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Response, status
from sqlalchemy.orm import Session

from app.api.v1.dependencies.auth import get_current_principal
from app.api.v1.dependencies.common import get_cursor, get_idempotency_key, get_if_match
from app.api.v1.dependencies.database import get_db
from app.core.security import Principal
from app.schemas.common import PaginatedResponse
from app.schemas.contacts import Contact, ContactCreate, ContactUpdate
from app.services.contacts import ContactService
from app.services.utils.pagination import CursorPage

router = APIRouter(prefix="/contacts", tags=["Contacts"])


@router.get("", response_model=PaginatedResponse[Contact])
def list_contacts(
    cursor: Annotated[CursorPage, Depends(get_cursor)],
    business_id: str | None = None,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = ContactService(db, principal)
    result = service.list_contacts(cursor=cursor, business_id=business_id)
    return result


@router.post("", response_model=Contact, status_code=status.HTTP_201_CREATED)
def create_contact(
    payload: ContactCreate,
    response: Response,
    idempotency_key: Annotated[str | None, Depends(get_idempotency_key)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = ContactService(db, principal)
    try:
        result = service.create_contact(payload, idempotency_key=idempotency_key)
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_409_CONFLICT, detail=str(exc)) from exc
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.get("/{contact_id}", response_model=Contact)
def get_contact(
    contact_id: str,
    response: Response,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = ContactService(db, principal)
    try:
        result = service.get_contact(contact_id)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Contact not found")
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.patch("/{contact_id}", response_model=Contact)
def update_contact(
    contact_id: str,
    payload: ContactUpdate,
    response: Response,
    if_match: Annotated[str | None, Depends(get_if_match)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = ContactService(db, principal)
    try:
        result = service.update_contact(contact_id, payload, if_match=if_match)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Contact not found")
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_412_PRECONDITION_FAILED, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.delete("/{contact_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_contact(
    contact_id: str,
    hard_delete: bool = False,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = ContactService(db, principal)
    try:
        service.delete_contact(contact_id, hard_delete=hard_delete)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Contact not found")
