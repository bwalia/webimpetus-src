from __future__ import annotations

from enum import Enum

from sqlalchemy import CheckConstraint, ForeignKey, Numeric, String
from sqlalchemy.orm import Mapped, mapped_column, relationship

from app.db.models.base import Base, BaseUUIDMixin


class JournalStatus(str, Enum):
    DRAFT = "draft"
    POSTED = "posted"
    REVERSED = "reversed"


class JournalEntry(Base, BaseUUIDMixin):
    __tablename__ = "journal_entries"

    business_id: Mapped[str] = mapped_column(ForeignKey("businesses.id"), nullable=False, index=True)
    entry_date: Mapped[str] = mapped_column(String(10), nullable=False)
    period: Mapped[str] = mapped_column(String(7), nullable=False)  # e.g. 2025-01
    reference: Mapped[str | None] = mapped_column(String(50), nullable=True)
    memo: Mapped[str | None] = mapped_column(String(500), nullable=True)
    status: Mapped[str] = mapped_column(String(20), default=JournalStatus.DRAFT.value)
    is_locked: Mapped[bool] = mapped_column(default=False, nullable=False)

    lines = relationship("JournalLine", back_populates="entry", cascade="all, delete-orphan")


class JournalLine(Base, BaseUUIDMixin):
    __tablename__ = "journal_lines"
    __table_args__ = (
        CheckConstraint("debit >= 0 AND credit >= 0", name="ck_journal_line_non_negative"),
    )

    entry_id: Mapped[str] = mapped_column(ForeignKey("journal_entries.id"), nullable=False, index=True)
    account_code: Mapped[str] = mapped_column(String(20), nullable=False)
    description: Mapped[str | None] = mapped_column(String(255), nullable=True)
    debit: Mapped[float] = mapped_column(Numeric(12, 2), nullable=False, default=0)
    credit: Mapped[float] = mapped_column(Numeric(12, 2), nullable=False, default=0)

    entry = relationship("JournalEntry", back_populates="lines")
