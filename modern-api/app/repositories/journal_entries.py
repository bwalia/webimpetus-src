from __future__ import annotations

from decimal import Decimal

from sqlalchemy import func, select

from app.db.models.accounting.journal_entry import JournalEntry, JournalLine

from .base import BaseRepository


class JournalEntryRepository(BaseRepository[JournalEntry]):
    model = JournalEntry

    def is_period_locked(self, tenant_id: str, period: str) -> bool:
        stmt = select(func.count()).select_from(JournalEntry).where(
            JournalEntry.tenant_id == tenant_id,
            JournalEntry.period == period,
            JournalEntry.is_locked == True,
        )
        return self.session.execute(stmt).scalar_one() > 0

    def validate_balanced(self, entry: JournalEntry) -> None:
        total_debit = sum(Decimal(line.debit) for line in entry.lines)
        total_credit = sum(Decimal(line.credit) for line in entry.lines)
        if total_debit != total_credit:
            raise ValueError("Journal entry must balance")

    def replace_lines(self, entry: JournalEntry, lines: list[JournalLine]) -> None:
        entry.lines.clear()
        entry.lines.extend(lines)
