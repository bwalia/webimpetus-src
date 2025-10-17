from __future__ import annotations

from decimal import Decimal
from typing import List, Optional
from uuid import UUID

from pydantic import BaseModel, Field, model_validator
from pydantic.config import ConfigDict

from .common import AuditFields


class JournalLineBase(BaseModel):
    account_code: str = Field(max_length=20)
    description: Optional[str] = Field(default=None, max_length=255)
    debit: Decimal = Field(ge=0)
    credit: Decimal = Field(ge=0)


class JournalLineCreate(JournalLineBase):
    pass


class JournalLine(JournalLineBase, AuditFields):
    model_config = ConfigDict(from_attributes=True)


class JournalEntryBase(BaseModel):
    business_id: UUID
    entry_date: str = Field(pattern=r"^\\d{4}-\\d{2}-\\d{2}$")
    period: str = Field(pattern=r"^\\d{4}-\\d{2}$")
    reference: Optional[str] = None
    memo: Optional[str] = None
    status: str = Field(default="draft")
    lines: List[JournalLineCreate]

    @model_validator(mode="after")
    def validate_balance(cls, values: "JournalEntryBase") -> "JournalEntryBase":
        total_debits = sum(line.debit for line in values.lines)
        total_credits = sum(line.credit for line in values.lines)
        if total_debits != total_credits:
            raise ValueError("Journal entry must balance (debits == credits)")
        return values


class JournalEntryCreate(JournalEntryBase):
    pass


class JournalEntryUpdate(BaseModel):
    entry_date: Optional[str] = None
    period: Optional[str] = None
    reference: Optional[str] = None
    memo: Optional[str] = None
    status: Optional[str] = None
    lines: Optional[List[JournalLineCreate]] = None


class JournalEntry(JournalEntryBase, AuditFields):
    is_locked: bool
    lines: List[JournalLine]
    model_config = ConfigDict(from_attributes=True)
