from __future__ import annotations

from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, Response, status
from sqlalchemy.orm import Session

from app.api.v1.dependencies.auth import get_current_principal
from app.api.v1.dependencies.common import get_cursor, get_idempotency_key, get_if_match
from app.api.v1.dependencies.database import get_db
from app.core.security import Principal
from app.schemas.common import PaginatedResponse
from app.schemas.sales_invoices import SalesInvoice, SalesInvoiceCreate, SalesInvoiceUpdate
from app.services.sales_invoices import SalesInvoiceService
from app.services.utils.pagination import CursorPage

router = APIRouter(prefix="/sales-invoices", tags=["Sales Invoices"])


@router.get("", response_model=PaginatedResponse[SalesInvoice])
def list_invoices(
    cursor: Annotated[CursorPage, Depends(get_cursor)],
    business_id: str | None = None,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = SalesInvoiceService(db, principal)
    return service.list_invoices(cursor=cursor, business_id=business_id)


@router.post("", response_model=SalesInvoice, status_code=status.HTTP_201_CREATED)
def create_invoice(
    payload: SalesInvoiceCreate,
    response: Response,
    idempotency_key: Annotated[str | None, Depends(get_idempotency_key)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = SalesInvoiceService(db, principal)
    try:
        result = service.create_invoice(payload, idempotency_key=idempotency_key)
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_409_CONFLICT, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.get("/{invoice_id}", response_model=SalesInvoice)
def get_invoice(
    invoice_id: str,
    response: Response,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = SalesInvoiceService(db, principal)
    try:
        result = service.get_invoice(invoice_id)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Invoice not found")
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.patch("/{invoice_id}", response_model=SalesInvoice)
def update_invoice(
    invoice_id: str,
    payload: SalesInvoiceUpdate,
    response: Response,
    if_match: Annotated[str | None, Depends(get_if_match)],
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = SalesInvoiceService(db, principal)
    try:
        result = service.update_invoice(invoice_id, payload, if_match=if_match)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Invoice not found")
    except ValueError as exc:
        raise HTTPException(status_code=status.HTTP_412_PRECONDITION_FAILED, detail=str(exc))
    if result.etag:
        response.headers["ETag"] = result.etag
    return result.data


@router.delete("/{invoice_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_invoice(
    invoice_id: str,
    hard_delete: bool = False,
    db: Session = Depends(get_db),
    principal: Principal = Depends(get_current_principal),
):
    service = SalesInvoiceService(db, principal)
    try:
        service.delete_invoice(invoice_id, hard_delete=hard_delete)
    except KeyError:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Invoice not found")
