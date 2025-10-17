"""Authentication router for token generation."""
from __future__ import annotations

from datetime import datetime, timedelta, timezone
from typing import Annotated

from fastapi import APIRouter, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordBearer, OAuth2PasswordRequestForm
from jose import jwt
from pydantic import BaseModel
from sqlalchemy.ext.asyncio import AsyncSession

from app.api.v1.dependencies.database import get_db
from app.core.config import get_settings


class TokenResponse(BaseModel):
    """OAuth2 token response."""
    access_token: str
    token_type: str = "bearer"
    expires_in: int


router = APIRouter(prefix="/auth", tags=["Authentication"])

oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/v1/auth/token")


@router.post("/token", response_model=TokenResponse, summary="Get access token")
async def login(
    form_data: Annotated[OAuth2PasswordRequestForm, Depends()],
    db: AsyncSession = Depends(get_db),
) -> TokenResponse:
    """
    OAuth2 compatible token endpoint.

    Request body (form-encoded):
    - username: email or username
    - password: user password
    - grant_type: must be "password"

    Returns a JWT access token that can be used for API authentication.
    """
    settings = get_settings()

    # For now, this is a stub implementation
    # TODO: Implement actual user authentication against database
    # This should verify credentials, check user exists, and get user roles/tenant

    # Stub: Accept any credentials for development
    # In production, you would:
    # 1. Query user from database by email/username
    # 2. Verify password hash
    # 3. Get user's tenant_id and roles
    # 4. Generate token with proper claims

    if not form_data.username or not form_data.password:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid credentials",
            headers={"WWW-Authenticate": "Bearer"},
        )

    # Generate JWT token
    expires_at = datetime.now(tz=timezone.utc) + timedelta(hours=24)
    expires_in = int((expires_at - datetime.now(tz=timezone.utc)).total_seconds())

    # Token payload
    payload = {
        "sub": form_data.username,
        "tenant_id": "default",  # TODO: Get from database
        "roles": ["user"],  # TODO: Get from database
        "scopes": ["read", "write"],  # TODO: Get from database
        "exp": int(expires_at.timestamp()),
        "iat": int(datetime.now(tz=timezone.utc).timestamp()),
        "iss": settings.jwt_issuer or "webimpetus-api",
        "aud": settings.jwt_audience or "webimpetus-api",
    }

    # Sign token
    secret_key = settings.jwt_public_key or "your-secret-key-change-in-production"
    token = jwt.encode(payload, secret_key, algorithm="HS256")

    return TokenResponse(
        access_token=token,
        token_type="bearer",
        expires_in=expires_in,
    )


@router.post("/token/json", response_model=TokenResponse, summary="Get access token (JSON)")
async def login_json(
    credentials: dict,
    db: AsyncSession = Depends(get_db),
) -> TokenResponse:
    """
    JSON-based token endpoint (alternative to form-encoded).

    Request body (JSON):
    {
        "email": "user@example.com",
        "password": "password123",
        "type": "users"  // optional: users, contacts, employees, etc.
    }

    Returns a JWT access token.
    """
    settings = get_settings()

    email = credentials.get("email") or credentials.get("username")
    password = credentials.get("password")
    user_type = credentials.get("type", "users")

    if not email or not password:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Email and password are required",
        )

    # Generate JWT token (stub implementation)
    expires_at = datetime.now(tz=timezone.utc) + timedelta(hours=24)
    expires_in = int((expires_at - datetime.now(tz=timezone.utc)).total_seconds())

    payload = {
        "sub": email,
        "tenant_id": "default",
        "roles": ["user"],
        "scopes": ["read", "write"],
        "exp": int(expires_at.timestamp()),
        "iat": int(datetime.now(tz=timezone.utc).timestamp()),
        "iss": settings.jwt_issuer or "webimpetus-api",
        "aud": settings.jwt_audience or "webimpetus-api",
        "type": user_type,
    }

    secret_key = settings.jwt_public_key or "your-secret-key-change-in-production"
    token = jwt.encode(payload, secret_key, algorithm="HS256")

    return TokenResponse(
        access_token=token,
        token_type="bearer",
        expires_in=expires_in,
    )
