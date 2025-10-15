# Project Jobs API Documentation

This document describes the REST API endpoints for the Project Jobs Management system.

## Base URL
```
https://dev001.workstation.co.uk/api/v2
```

## Authentication
All endpoints require JWT authentication. Include the bearer token in the Authorization header:
```
Authorization: Bearer YOUR_JWT_TOKEN
```

## API Endpoints

### 1. Project Jobs API (`/api/v2/project_jobs`)

#### GET /api/v2/project_jobs
Get all project jobs for a business.

**Required Parameters:**
- `uuid_business_id` (query) - Business UUID

**Optional Parameters:**
- `params` (query) - JSON string with pagination, sorting, and filtering options
  ```json
  {
    "pagination": {"page": 1, "perPage": 10},
    "sort": {"field": "created_at", "order": "DESC"},
    "filter": {
      "uuid_business_id": "329e0405-b544-5051-8d37-d0143e9c8829",
      "uuid_project_id": "project-uuid",
      "status": "In Progress"
    }
  }
  ```

**Example Request:**
```bash
curl -X GET "https://dev001.workstation.co.uk/api/v2/project_jobs?uuid_business_id=329e0405-b544-5051-8d37-d0143e9c8829" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json"
```

**Example Response:**
```json
{
  "data": [
    {
      "uuid": "962a6b54-8edc-53fa-af3c-d6ba810888b8",
      "uuid_business_id": "329e0405-b544-5051-8d37-d0143e9c8829",
      "uuid_project_id": "project-uuid",
      "job_number": "JOB-001",
      "job_name": "Job checkout feature 001",
      "job_description": "Implement checkout feature",
      "job_type": "Development",
      "priority": "High",
      "status": "In Progress",
      "assigned_to_user_id": 1,
      "assigned_to_employee_id": 1,
      "planned_start_date": "2025-01-15",
      "planned_end_date": "2025-02-15",
      "estimated_hours": 120.00,
      "estimated_cost": 12000.00,
      "billable": 1,
      "hourly_rate": 100.00,
      "completion_percentage": 35,
      "created_at": "2025-10-15 10:30:00"
    }
  ],
  "total": 1,
  "message": 200
}
```

#### GET /api/v2/project_jobs/{uuid}
Get a single project job by UUID.

**Example Request:**
```bash
curl -X GET "https://dev001.workstation.co.uk/api/v2/project_jobs/962a6b54-8edc-53fa-af3c-d6ba810888b8" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json"
```

#### POST /api/v2/project_jobs
Create a new project job.

**Required Fields:**
- `uuid_business_id` - Business UUID
- `uuid_project_id` - Project UUID
- `job_name` - Job name/title

**Optional Fields:**
- `job_description` - Detailed description
- `job_type` - Type: Development, Design, Testing, Deployment, Support, Research, Other (default: Development)
- `priority` - Priority: Low, Normal, High, Urgent (default: Normal)
- `status` - Status: Planning, In Progress, On Hold, Completed, Cancelled (default: Planning)
- `assigned_to_user_id` - User ID
- `assigned_to_employee_id` - Employee ID
- `planned_start_date` - Start date (YYYY-MM-DD)
- `planned_end_date` - End date (YYYY-MM-DD)
- `estimated_hours` - Estimated hours
- `estimated_cost` - Estimated cost
- `billable` - Is billable (1/0, default: 1)
- `hourly_rate` - Hourly rate
- `completion_percentage` - Completion % (default: 0)
- `notes` - Additional notes
- `created_by` - Creator UUID

**Example Request:**
```bash
curl -X POST "https://dev001.workstation.co.uk/api/v2/project_jobs" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "uuid_business_id": "329e0405-b544-5051-8d37-d0143e9c8829",
    "uuid_project_id": "project-uuid",
    "job_name": "Implement payment gateway",
    "job_description": "Integrate Stripe payment gateway",
    "job_type": "Development",
    "priority": "High",
    "status": "Planning",
    "estimated_hours": 80,
    "estimated_cost": 8000,
    "billable": 1,
    "hourly_rate": 100
  }'
```

#### PUT /api/v2/project_jobs/{uuid}
Update an existing project job.

**Example Request:**
```bash
curl -X PUT "https://dev001.workstation.co.uk/api/v2/project_jobs/962a6b54-8edc-53fa-af3c-d6ba810888b8" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "In Progress",
    "completion_percentage": 50,
    "actual_start_date": "2025-01-20"
  }'
```

#### DELETE /api/v2/project_jobs/{uuid}
Delete a project job.

**Example Request:**
```bash
curl -X DELETE "https://dev001.workstation.co.uk/api/v2/project_jobs/962a6b54-8edc-53fa-af3c-d6ba810888b8" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json"
```

---

### 2. Project Job Phases API (`/api/v2/project_job_phases`)

#### GET /api/v2/project_job_phases
Get all phases for a business.

**Required Parameters:**
- `uuid_business_id` (query) - Business UUID

**Optional Parameters:**
- `uuid_project_job_id` (query) - Filter by specific job
- `status` (query) - Filter by status
- `params` (query) - JSON string with pagination/sorting/filtering

**Example Request:**
```bash
curl -X GET "https://dev001.workstation.co.uk/api/v2/project_job_phases?uuid_business_id=329e0405-b544-5051-8d37-d0143e9c8829&uuid_project_job_id=962a6b54-8edc-53fa-af3c-d6ba810888b8" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json"
```

#### GET /api/v2/project_job_phases/{uuid}
Get a single phase by UUID.

#### POST /api/v2/project_job_phases
Create a new phase.

**Required Fields:**
- `uuid_business_id` - Business UUID
- `uuid_project_job_id` - Job UUID
- `phase_name` - Phase name

**Optional Fields:**
- `phase_description` - Description
- `phase_order` - Order number (default: 1)
- `status` - Status: Not Started, In Progress, Completed, Blocked (default: Not Started)
- `assigned_to_user_id` - User ID
- `assigned_to_employee_id` - Employee ID
- `planned_start_date` - Start date (YYYY-MM-DD)
- `planned_end_date` - End date (YYYY-MM-DD)
- `estimated_hours` - Estimated hours
- `depends_on_phase_uuid` - UUID of dependency phase
- `completion_percentage` - Completion %
- `deliverables` - Deliverables description
- `acceptance_criteria` - Acceptance criteria
- `notes` - Notes

**Example Request:**
```bash
curl -X POST "https://dev001.workstation.co.uk/api/v2/project_job_phases" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "uuid_business_id": "329e0405-b544-5051-8d37-d0143e9c8829",
    "uuid_project_job_id": "962a6b54-8edc-53fa-af3c-d6ba810888b8",
    "phase_name": "Requirements Gathering",
    "phase_description": "Collect and document requirements",
    "phase_order": 1,
    "estimated_hours": 20
  }'
```

#### PUT /api/v2/project_job_phases/{uuid}
Update a phase.

#### DELETE /api/v2/project_job_phases/{uuid}
Delete a phase.

---

### 3. Project Job Scheduler API (`/api/v2/project_job_scheduler`)

#### GET /api/v2/project_job_scheduler
Get all scheduled events for a business.

**Required Parameters:**
- `uuid_business_id` (query) - Business UUID

**Optional Parameters:**
- `uuid_project_job_id` (query) - Filter by job
- `start_date` (query) - Filter by start date (YYYY-MM-DD)
- `end_date` (query) - Filter by end date (YYYY-MM-DD)
- `params` (query) - JSON string with pagination/sorting/filtering

**Example Request:**
```bash
curl -X GET "https://dev001.workstation.co.uk/api/v2/project_job_scheduler?uuid_business_id=329e0405-b544-5051-8d37-d0143e9c8829&start_date=2025-01-01&end_date=2025-12-31" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json"
```

#### GET /api/v2/project_job_scheduler/{uuid}
Get a single scheduled event by UUID.

#### POST /api/v2/project_job_scheduler
Create a new scheduled event.

**Required Fields:**
- `uuid_business_id` - Business UUID
- `uuid_project_job_id` - Job UUID
- `title` - Event title
- `schedule_date` - Schedule date (YYYY-MM-DD)

**Optional Fields:**
- `uuid_phase_id` - Phase UUID
- `assigned_to_user_id` - User ID
- `assigned_to_employee_id` - Employee ID
- `start_time` - Start time (HH:MM:SS)
- `end_time` - End time (HH:MM:SS)
- `all_day` - Is all-day event (1/0, default: 0)
- `duration_hours` - Duration in hours
- `color` - Color code (default: #667eea)
- `notes` - Notes
- `status` - Status: Scheduled, In Progress, Completed, Cancelled (default: Scheduled)
- `created_by` - Creator UUID

**Example Request:**
```bash
curl -X POST "https://dev001.workstation.co.uk/api/v2/project_job_scheduler" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "uuid_business_id": "329e0405-b544-5051-8d37-d0143e9c8829",
    "uuid_project_job_id": "962a6b54-8edc-53fa-af3c-d6ba810888b8",
    "title": "Sprint Planning Meeting",
    "schedule_date": "2025-01-20",
    "start_time": "10:00:00",
    "end_time": "11:30:00",
    "duration_hours": 1.5,
    "color": "#4CAF50"
  }'
```

#### PUT /api/v2/project_job_scheduler/{uuid}
Update a scheduled event.

#### DELETE /api/v2/project_job_scheduler/{uuid}
Delete a scheduled event.

---

## Error Responses

### 400 Bad Request
Missing required field or invalid data:
```json
{
  "error": "Business UUID is required"
}
```

### 403 Forbidden
Missing business UUID:
```json
{
  "data": "You must specify the Business UUID",
  "message": 403
}
```

### 404 Not Found
Resource not found:
```json
{
  "error": "Job not found"
}
```

### 500 Internal Server Error
Server error:
```json
{
  "error": "Failed to create job"
}
```

---

## OpenAPI/Swagger Documentation

Full OpenAPI documentation is available at:
- **Swagger UI**: https://dev001.workstation.co.uk/api-docs
- **JSON Spec**: https://dev001.workstation.co.uk/swagger/json
- **YAML Spec**: https://dev001.workstation.co.uk/swagger/yaml

---

## Data Models

### Project Job Fields
- `id` (int) - Auto-increment ID
- `uuid` (string) - Unique identifier
- `uuid_business_id` (string) - Business UUID
- `uuid_project_id` (string) - Project UUID
- `job_number` (string) - Auto-generated job number (JOB-001, JOB-002, etc.)
- `job_name` (string) - Job name/title
- `job_description` (text) - Detailed description
- `job_type` (enum) - Development, Design, Testing, Deployment, Support, Research, Other
- `priority` (enum) - Low, Normal, High, Urgent
- `status` (enum) - Planning, In Progress, On Hold, Completed, Cancelled
- `assigned_to_user_id` (int) - Assigned user ID
- `assigned_to_employee_id` (int) - Assigned employee ID
- `planned_start_date` (date) - Planned start date
- `planned_end_date` (date) - Planned end date
- `actual_start_date` (date) - Actual start date
- `actual_end_date` (date) - Actual end date
- `estimated_hours` (decimal) - Estimated hours
- `actual_hours` (decimal) - Actual hours worked
- `estimated_cost` (decimal) - Estimated cost
- `actual_cost` (decimal) - Actual cost
- `billable` (tinyint) - Is billable (1/0)
- `hourly_rate` (decimal) - Hourly rate
- `completion_percentage` (int) - Completion percentage (0-100)
- `notes` (text) - Additional notes
- `created_by` (string) - Creator UUID
- `created_at` (datetime) - Creation timestamp
- `modified_at` (datetime) - Last modification timestamp

### Project Job Phase Fields
Similar structure with phase-specific fields like `phase_number`, `phase_name`, `phase_order`, `depends_on_phase_uuid`, `deliverables`, `acceptance_criteria`

### Project Job Scheduler Fields
Event-specific fields like `schedule_date`, `start_time`, `end_time`, `all_day`, `duration_hours`, `title`, `color`

---

## Testing the APIs

### Example Business UUIDs (for testing):
- Odin Capital Management Ltd: `d15c707f-bd87-574a-9237-a40286bfaaa9`
- Workstation SRL BE: `329e0405-b544-5051-8d37-d0143e9c8829`
- Workstation Solutions Ltd UK: `0f6c4e64-9b50-5e11-a7d1-1923b7aef282`

### Example Job UUID (for testing):
- Job: `962a6b54-8edc-53fa-af3c-d6ba810888b8` (JOB-001 - Job checkout feature 001)

**Note**: You must obtain a valid JWT token by authenticating through the application's login endpoint before using these APIs.
