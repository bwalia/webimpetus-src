"""Security helpers for OAuth2/OIDC bearer tokens and RBAC."""
from __future__ import annotations

import base64
import hashlib
import hmac
import json
from dataclasses import dataclass
from datetime import datetime, timezone
from typing import Iterable, List, Optional, Sequence

from jose import JWTError, jwt

from .config import get_settings


@dataclass(slots=True)
class Principal:
    sub: str
    tenant_id: str
    roles: Sequence[str]
    scopes: Sequence[str]
    expires_at: datetime

    def has_role(self, *candidates: str) -> bool:
        return any(role in self.roles for role in candidates)

    def has_scope(self, scope: str) -> bool:
        return scope in self.scopes


class TokenVerifier:
    """Verify incoming JWTs using configured issuer/audience/public key."""

    def __init__(self) -> None:
        settings = get_settings()
        self._issuer = settings.jwt_issuer
        self._audience = settings.jwt_audience
        self._public_key = settings.jwt_public_key or None

    def decode(self, token: str) -> Principal:
        settings = get_settings()

        options = {
            "verify_aud": bool(self._audience),
            "verify_signature": bool(self._public_key),
        }

        try:
            payload = jwt.decode(
                token,
                key=self._public_key,
                algorithms=["RS256", "HS256"],
                audience=self._audience if self._audience else None,
                issuer=self._issuer if self._issuer else None,
                options=options,
            )
        except JWTError as exc:
            raise PermissionError("Invalid bearer token") from exc

        exp = datetime.fromtimestamp(payload.get("exp", 0), tz=timezone.utc)
        if exp < datetime.now(tz=timezone.utc):
            raise PermissionError("Token expired")

        roles = payload.get("roles", []) or []
        scopes = payload.get("scope", payload.get("scopes", []))
        if isinstance(scopes, str):
            scopes = scopes.split()

        if not payload.get("tenant_id"):
            raise PermissionError("tenant_id missing from token")

        return Principal(
            sub=str(payload.get("sub")),
            tenant_id=str(payload.get("tenant_id")),
            roles=list(roles),
            scopes=list(scopes),
            expires_at=exp,
        )


def require_scope(principal: Principal, required: Iterable[str]) -> None:
    missing = [scope for scope in required if not principal.has_scope(scope)]
    if missing and not principal.has_role("owner", "admin"):
        raise PermissionError(f"Missing scopes: {', '.join(missing)}")


def hash_secret(value: str, *, secret: Optional[str] = None) -> str:
    secret = secret or get_settings().jwt_public_key or "default-secret"
    digest = hmac.new(secret.encode(), value.encode(), hashlib.sha256).digest()
    return base64.urlsafe_b64encode(digest).decode().rstrip("=")


def build_etag(version: int, updated_at: datetime) -> str:
    payload = json.dumps({"v": version, "ts": updated_at.astimezone(timezone.utc).isoformat()}).encode()
    return hashlib.sha256(payload).hexdigest()
