# Go sample client for Modern API Timesheets

This example CLI exercises the Timesheets endpoints exposed by the FastAPI-based `modern-api` service. It creates a draft entry, updates it with an `If-Match` header, lists timesheets, starts a new running timer, and finally stops it.

## Prerequisites

1. A running `modern-api` instance accessible from your machine (for local work, `docker compose up modern-api` in the repository root).
2. A bearer token that the API will accept (set up via your usual auth flow).
3. A valid Business UUID in the target tenant—the sample reuses it when creating each timesheet.
4. Go 1.21 or later.

## Configuration

Set the following environment variables before running the client:

- `MODERN_API_BASE_URL` (optional): Defaults to `http://localhost:8000/api/v1`.
- `MODERN_API_BEARER_TOKEN`: JWT or opaque token accepted by the API (omit only if auth is disabled locally).
- `MODERN_API_BUSINESS_ID`: UUID of an existing business record.

## Run it

From this directory:

```bash
export MODERN_API_BASE_URL=http://localhost:8000/api/v1
export MODERN_API_BEARER_TOKEN="<your token>"
export MODERN_API_BUSINESS_ID="<business uuid>"
go run .
```

The program logs each step and surfaces the server responses (including ETags) so you can confirm the API behaviour end-to-end.

## What it covers

- `POST /timesheets` with a complete payload
- `PATCH /timesheets/{id}` guarded by `If-Match`
- `GET /timesheets` pagination metadata
- `POST /timesheets/start` and `/timesheets/{id}/stop`

Use it as a starting point for deeper integration tests or tooling.
