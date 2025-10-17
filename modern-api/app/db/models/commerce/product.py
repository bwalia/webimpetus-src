from __future__ import annotations

from sqlalchemy import Numeric, String
from sqlalchemy.orm import Mapped, mapped_column

from app.db.models.base import Base, BaseUUIDMixin


class Product(Base, BaseUUIDMixin):
    __tablename__ = "products"

    name: Mapped[str] = mapped_column(String(200), nullable=False)
    sku: Mapped[str] = mapped_column(String(100), nullable=False, unique=True)
    unit_price: Mapped[float] = mapped_column(Numeric(12, 2), nullable=False, default=0)
    currency: Mapped[str] = mapped_column(String(3), nullable=False, default="GBP")
