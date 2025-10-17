from __future__ import annotations

from datetime import datetime, timedelta
from uuid import uuid4

import pytest
from fastapi.testclient import TestClient
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from sqlalchemy.pool import StaticPool

from app.api.v1.dependencies.auth import get_current_principal
from app.api.v1.dependencies.database import get_db
from app.core.security import Principal
from app.db.models import Base, Business
from app.main import app


@pytest.fixture(scope="module")
def test_context():
    engine = create_engine(
        "sqlite+pysqlite:///:memory:",
        future=True,
        connect_args={"check_same_thread": False},
        poolclass=StaticPool,
    )
    TestingSessionLocal = sessionmaker(bind=engine, autoflush=False, autocommit=False, expire_on_commit=False)
    Base.metadata.create_all(bind=engine)
    from sqlalchemy import inspect

    inspector = inspect(engine)
    assert "timesheets" in inspector.get_table_names()

    tenant_id = str(uuid4())
    user_id = str(uuid4())

    def override_get_db():
        db = TestingSessionLocal()
        try:
            yield db
            db.commit()
        except Exception:
            db.rollback()
            raise
        finally:
            db.close()

    def override_get_current_principal():
        return Principal(
            sub=user_id,
            tenant_id=tenant_id,
            roles=["owner"],
            scopes=["crm.read", "crm.write"],
            expires_at=datetime.utcnow() + timedelta(hours=1),
        )

    app.dependency_overrides[get_db] = override_get_db
    app.dependency_overrides[get_current_principal] = override_get_current_principal

    with TestClient(app) as client:
        yield {
            "client": client,
            "session_factory": TestingSessionLocal,
            "engine": engine,
            "tenant_id": tenant_id,
            "user_id": user_id,
        }

    app.dependency_overrides.clear()
    Base.metadata.drop_all(bind=engine)


@pytest.fixture(scope="module")
def client(test_context):
    return test_context["client"]


@pytest.fixture(scope="module")
def session_factory(test_context):
    return test_context["session_factory"]


@pytest.fixture(scope="module")
def business_id(session_factory, test_context):
    tenant_id = test_context["tenant_id"]
    session = session_factory()
    try:
        business = Business(
            tenant_id=tenant_id,
            name="Acme Ltd",
            code="ACME",
            status="active",
        )
        session.add(business)
        session.commit()
        session.refresh(business)
        return business.id
    finally:
        session.close()


def test_create_and_list_timesheets(client: TestClient, business_id: str):
    employee_id = str(uuid4())
    start_time = datetime.utcnow().replace(microsecond=0)
    end_time = start_time + timedelta(hours=1)

    payload = {
        "uuid_business_id": business_id,
        "employee_id": employee_id,
        "start_time": start_time.isoformat(),
        "end_time": end_time.isoformat(),
        "hourly_rate": "100.00",
        "is_billable": True,
        "status": "draft",
        "description": "Initial consultation",
    }

    create_response = client.post("/api/v1/timesheets", json=payload)
    assert create_response.status_code == 201, create_response.json()
    assert "ETag" in create_response.headers

    body = create_response.json()
    assert body["uuid_business_id"] == business_id
    assert body["employee_id"] == employee_id
    assert body["duration_minutes"] == 60
    assert body["billable_hours"] == "1.00"
    assert body["total_amount"] == "100.00"

    list_response = client.get("/api/v1/timesheets")
    assert list_response.status_code == 200
    payload = list_response.json()
    assert payload["meta"]["page_size"] == 50
    assert any(item["uuid"] == body["uuid"] for item in payload["data"])


def test_update_timesheet_uses_etag(client: TestClient, business_id: str):
    start_time = datetime.utcnow().replace(microsecond=0)
    end_time = start_time + timedelta(minutes=30)
    create_payload = {
        "uuid_business_id": business_id,
        "employee_id": str(uuid4()),
        "start_time": start_time.isoformat(),
        "end_time": end_time.isoformat(),
        "hourly_rate": "80.00",
        "status": "draft",
    }
    create_resp = client.post("/api/v1/timesheets", json=create_payload)
    assert create_resp.status_code == 201, create_resp.json()
    timesheet_id = create_resp.json()["uuid"]
    etag = create_resp.headers["ETag"]

    update_resp = client.patch(
        f"/api/v1/timesheets/{timesheet_id}",
        json={"description": "Updated description"},
        headers={"If-Match": etag},
    )
    assert update_resp.status_code == 200
    assert update_resp.json()["description"] == "Updated description"
    assert "ETag" in update_resp.headers
    assert update_resp.headers["ETag"] != etag


def test_start_and_stop_timer(client: TestClient, business_id: str):
    start_payload = {
        "uuid_business_id": business_id,
        "employee_id": str(uuid4()),
        "description": "Billable timer",
        "hourly_rate": "50.00",
        "status": "running",
    }

    start_resp = client.post("/api/v1/timesheets/start", json=start_payload)
    assert start_resp.status_code == 201, start_resp.json()
    start_body = start_resp.json()
    assert start_body["is_running"] is True

    stop_resp = client.post(f"/api/v1/timesheets/{start_body['uuid']}/stop")
    assert stop_resp.status_code == 200
    stop_body = stop_resp.json()
    assert stop_body["is_running"] is False
    assert stop_body["status"] == "stopped"
