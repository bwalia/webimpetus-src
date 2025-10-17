from __future__ import annotations

from enum import Enum

from sqlalchemy import ForeignKey, String
from sqlalchemy.orm import Mapped, mapped_column, relationship

from .base import Base, BaseUUIDMixin


class TaskStatus(str, Enum):
    TODO = "todo"
    IN_PROGRESS = "in_progress"
    REVIEW = "review"
    DONE = "done"


class Task(Base, BaseUUIDMixin):
    __tablename__ = "tasks"

    project_id: Mapped[str] = mapped_column(ForeignKey("projects.id"), nullable=False, index=True)
    name: Mapped[str] = mapped_column(String(200), nullable=False)
    description: Mapped[str | None] = mapped_column(String(2000), nullable=True)
    status: Mapped[str] = mapped_column(String(20), default=TaskStatus.TODO.value)
    assignee_id: Mapped[str | None] = mapped_column(ForeignKey("contacts.id"), nullable=True, index=True)
    labels: Mapped[str | None] = mapped_column(String(500), nullable=True)
    due_date: Mapped[str | None] = mapped_column(String(10), nullable=True)

    project = relationship("Project", back_populates="tasks")
    assignee = relationship("Contact", back_populates="tasks")
