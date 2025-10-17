"""Application entrypoint for the FastAPI service."""
from __future__ import annotations

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from app.api.v1.routers import router as api_v1_router
from app.core.config import get_settings
from app.core.middleware.tracing import TraceMiddleware


def create_app() -> FastAPI:
    settings = get_settings()

    app = FastAPI(
        title=settings.app_name,
        version="0.1.0",
        debug=settings.debug,
        openapi_url="/openapi.json",
        docs_url="/docs",
        redoc_url="/redoc",
    )

    app.add_middleware(TraceMiddleware)
    app.add_middleware(
        CORSMiddleware,
        allow_origins=["*"],
        allow_credentials=True,
        allow_methods=["*"],
        allow_headers=["*"],
    )

    app.include_router(api_v1_router)

    @app.get("/_health", tags=["Health"], summary="Health check")
    def healthcheck() -> dict[str, str]:
        return {"status": "ok"}

    return app


app = create_app()
