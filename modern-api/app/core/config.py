"""Application configuration using Pydantic settings."""
from functools import lru_cache
from typing import List

from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    app_name: str = "Workerra CI4 Multi-tenant API"
    environment: str = "development"
    debug: bool = True
    database_url: str = "sqlite:///./modern_api.db"
    echo_sql: bool = False
    jwt_audience: str = "api://default"
    jwt_issuer: str = "https://auth.example.com/"
    jwt_public_key: str = ""  # PEM-encoded public key for RS256 verification
    default_scopes: List[str] = [
        "crm.read",
        "crm.write",
        "finance.read",
        "finance.post",
        "content.publish",
    ]
    rate_limit_per_minute: int = 120

    model_config = SettingsConfigDict(env_file=".env", env_file_encoding="utf-8", extra="ignore")


@lru_cache(maxsize=1)
def get_settings() -> Settings:
    return Settings()
