from __future__ import annotations

from datetime import datetime
from typing import Optional
from uuid import UUID

from pydantic import BaseModel, EmailStr, Field
from pydantic.config import ConfigDict

from .common import AuditFields


class ContactBase(BaseModel):
    business_id: UUID
    first_name: str = Field(max_length=120)
    last_name: str = Field(max_length=120)
    email: EmailStr
    phone: Optional[str] = Field(default=None, max_length=50)
    gdpr_consent: bool = False


class ContactCreate(ContactBase):
    pass


class ContactUpdate(BaseModel):
    first_name: Optional[str] = None
    last_name: Optional[str] = None
    email: Optional[EmailStr] = None
    phone: Optional[str] = Field(default=None, max_length=50)
    gdpr_consent: Optional[bool] = None


class Contact(ContactBase, AuditFields):
    model_config = ConfigDict(from_attributes=True)
