from __future__ import annotations

from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Response, status
from sqlalchemy.orm import Session

from app.api.v1.dependencies.auth import get_current_principal
from app.api.v1.dependencies.common import get_cursor, get_idempotency_key, get_if_match
from app.api.v1.dependencies.database import get_db
from app.core.security import Principal
from app.schemas.businesses import Business, BusinessCreate, BusinessUpdate
from app.schemas.common import PaginatedResponse
from app.services.businesses import BusinessService
from app.services.utils.pagination import CursorPage

router = APIRouter(prefix="/businesses", tags=["Businesses"])


@router.get("", response_model=PaginatedResponse[Business])
def list_businesses(
    cursor: Annotated[CursorPage, Depends(get_cursor)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = BusinessService(db, principal)
    return service.list_businesses(cursor=cursor)


@router.post("", response_model=Business, status_code=status.HTTP_201_CREATED)
def create_business(
    payload: BusinessCreate,
    response: Response,
    idempotency_key: Annotated[str | None, Depends(get_idempotency_key)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = BusinessService(db, principal)
    try:
        result = service.create_business(payload, idempotency_key=idempotency_key)
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_409_CONFLICT, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.get("/{business_id}", response_model=Business)
def get_business(
    business_id: str,
    response: Response,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = BusinessService(db, principal)
    try:
        result = service.get_business(business_id)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Business not found")
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.patch("/{business_id}", response_model=Business)
def update_business(
    business_id: str,
    payload: BusinessUpdate,
    response: Response,
    if_match: Annotated[str | None, Depends(get_if_match)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = BusinessService(db, principal)
    try:
        result = service.update_business(business_id, payload, if_match=if_match)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Business not found")
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_412_PRECONDITION_FAILED, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.delete("/{business_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_business(
    business_id: str,
    hard_delete: bool = False,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = BusinessService(db, principal)
    try:
        service.delete_business(business_id, hard_delete=hard_delete)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Business not found")
