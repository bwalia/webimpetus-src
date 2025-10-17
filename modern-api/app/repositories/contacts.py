from __future__ import annotations

from typing import Sequence

from sqlalchemy import select

from app.db.models.contact import Contact

from .base import BaseRepository


class ContactRepository(BaseRepository[Contact]):
    model = Contact

    def list_by_business(self, tenant_id: str, business_id: str) -> Sequence[Contact]:
        stmt = select(Contact).where(
            Contact.tenant_id == tenant_id,
            Contact.business_id == business_id,
            Contact.is_deleted == False,
        )
        return [row[0] for row in self.session.execute(stmt).all()]

    def find_by_email(self, tenant_id: str, email: str) -> Contact | None:
        stmt = select(Contact).where(
            Contact.tenant_id == tenant_id,
            Contact.email == email,
            Contact.is_deleted == False,
        )
        return self.session.execute(stmt).scalar_one_or_none()
