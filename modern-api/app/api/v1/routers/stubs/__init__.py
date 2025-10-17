"""Utility for registering stub routers."""
from fastapi import APIRouter, HTTPException, status
from pydantic import BaseModel


def build_stub_router(prefix: str, tag: str, model: type[BaseModel]) -> APIRouter:
    router = APIRouter(prefix=prefix, tags=[tag])

    @router.get("", response_model=list[model])
    def list_resources():  # pragma: no cover - stub
        raise HTTPException(status_code=status.HTTP_501_NOT_IMPLEMENTED, detail="TODO: implement list")

    @router.post("", response_model=model, status_code=status.HTTP_202_ACCEPTED)
    def create_resource(_: model):  # type: ignore
        raise HTTPException(status_code=status.HTTP_501_NOT_IMPLEMENTED, detail="TODO: implement create")

    @router.get("/{resource_id}", response_model=model)
    def get_resource(resource_id: str):  # pragma: no cover - stub
        raise HTTPException(status_code=status.HTTP_501_NOT_IMPLEMENTED, detail=f"TODO: fetch {resource_id}")

    @router.patch("/{resource_id}", response_model=model)
    def update_resource(resource_id: str, _: model):  # type: ignore
        raise HTTPException(status_code=status.HTTP_501_NOT_IMPLEMENTED, detail=f"TODO: patch {resource_id}")

    @router.delete("/{resource_id}", status_code=status.HTTP_202_ACCEPTED)
    def delete_resource(resource_id: str):  # pragma: no cover - stub
        raise HTTPException(status_code=status.HTTP_501_NOT_IMPLEMENTED, detail=f"TODO: delete {resource_id}")

    return router
