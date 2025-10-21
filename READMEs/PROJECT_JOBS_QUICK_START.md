# Project Jobs System - Quick Start Guide

## Overview
The Project Jobs system is a comprehensive job and task management system built on top of the existing Projects module. It allows you to break down projects into jobs, jobs into phases, and track everything with a calendar scheduler.

## Accessing the System

### Navigation Menu
The system can be accessed via three menu items:

1. **Project Jobs** (`/project_jobs`) - Manage jobs within projects
2. **Project Job Phases** (`/project_job_phases`) - Manage phases within jobs
3. **Project Job Scheduler** (`/project_job_scheduler/calendar`) - Calendar view for scheduling

## System Hierarchy

```
Projects (existing)
  └── Project Jobs
        ├── Project Job Phases
        │     ├── Tasks (linked)
        │     │     └── Timesheets (linked)
        │     └── Schedule Events
        └── Schedule Events
```

## Key Features

### 1. Project Jobs
- **Auto-generated job numbers** (JOB-001, JOB-002, etc.)
- **Job types**: Development, Design, Testing, Deployment, Support, Research, Other
- **Priority levels**: Low, Normal, High, Urgent
- **Status tracking**: Planning, In Progress, On Hold, Completed, Cancelled
- **Assignment**: Assign to users or employees
- **Financial tracking**: Estimated/actual hours, estimated/actual cost, hourly rate, billable flag
- **Progress tracking**: Completion percentage (0-100%)
- **Date tracking**: Planned vs actual start/end dates

### 2. Project Job Phases
- **Auto-generated phase numbers** (PHASE-001, PHASE-002, etc.)
- **Phase ordering**: Drag-to-reorder phases
- **Dependencies**: Link phases so one must complete before another starts
- **Status tracking**: Not Started, In Progress, Completed, Blocked
- **Deliverables**: Define what should be delivered
- **Acceptance criteria**: Define completion criteria
- **Hours tracking**: Estimated vs actual (actual calculated from timesheets)

### 3. Project Job Scheduler
- **Calendar views**: Month, Week, Day, List
- **Drag-and-drop**: Move events by dragging
- **Event types**: All-day or timed events
- **Filtering**: Filter by Project, Job, User, or Employee
- **Color coding**: Assign colors to events
- **Status tracking**: Scheduled, In Progress, Completed, Cancelled

## Getting Started

### Step 1: Create a Job
1. Navigate to **Project Jobs** from the menu
2. Click **New Job**
3. Select a **Project** (required)
4. Enter **Job Name** (required)
5. Set job type, priority, and status
6. Optionally assign to user or employee
7. Set planned dates, estimated hours/cost
8. Click **Submit**

### Step 2: Create Phases
1. From the job edit page, click **Manage Phases**
   - OR navigate directly to job phases via the Phases button in the list
2. Click **New Phase**
3. Enter **Phase Name** (required)
4. Set phase order (determines sequence)
5. Optionally set dependencies (phase must wait for another to complete)
6. Define deliverables and acceptance criteria
7. Click **Submit**

### Step 3: Schedule Work
1. Navigate to **Project Job Scheduler**
2. Click on a date or **Schedule New Event**
3. Select the **Job** (and optionally a Phase)
4. Enter event title
5. Set date and time (or mark as all-day)
6. Assign to user/employee
7. Choose a color for easy identification
8. Click **Save**

### Step 4: Track Progress
- Update **completion percentage** on jobs and phases as work progresses
- Link **tasks** to jobs/phases for granular tracking
- Log **timesheets** against jobs/phases/tasks
- Actual hours are automatically calculated from timesheets

## Integration with Existing Modules

### Tasks
When creating/editing tasks, you can now link them to:
- A project job (`uuid_project_job_id`)
- A specific phase (`uuid_job_phase_id`)

### Timesheets
When logging time, timesheets can track:
- Project job (`uuid_project_job_id`)
- Job phase (`uuid_job_phase_id`)
- Specific task (`uuid_task_id`)

This provides complete time tracking from project → job → phase → task level.

## AJAX Search Endpoints

For use in custom forms or integrations:

- **Search Jobs**: `GET /common/searchProjectJobs?q={query}&project_uuid={optional}`
- **Search Phases**: `GET /common/searchProjectJobPhases?q={query}&job_uuid={optional}`

Returns JSON array with:
```json
[
  {
    "id": 123,
    "uuid": "abc-def-123",
    "job_number": "JOB-001",
    "job_name": "Database Migration",
    "project_name": "System Upgrade"
  }
]
```

## Key Routes

### Project Jobs
- List: `GET /project_jobs`
- Edit: `GET /project_jobs/edit/{uuid}`
- Save: `POST /project_jobs/update`
- Delete: `GET /project_jobs/delete/{uuid}`
- Jobs for project: `GET /project_jobs/byProject/{projectUuid}`
- Assign: `POST /project_jobs/assign/{uuid}`
- Update progress: `POST /project_jobs/updateProgress/{uuid}`

### Project Job Phases
- List for job: `GET /project_job_phases/index/{jobUuid}`
- Edit: `GET /project_job_phases/edit/{uuid}/{jobUuid}`
- Save: `POST /project_job_phases/update`
- Delete: `GET /project_job_phases/delete/{uuid}`
- Check dependencies: `GET /project_job_phases/checkDependencies/{uuid}`
- Reorder: `POST /project_job_phases/reorder`

### Project Job Scheduler
- Calendar: `GET /project_job_scheduler/calendar`
- Get events: `GET /project_job_scheduler/getEvents`
- Create event: `POST /project_job_scheduler/createEvent`
- Update event: `POST /project_job_scheduler/updateEvent/{uuid}`
- Delete event: `POST /project_job_scheduler/deleteEvent/{uuid}`
- Drag-drop: `POST /project_job_scheduler/dragDrop`

## Tips & Best Practices

### Job Organization
- Use clear, descriptive job names (e.g., "Frontend Redesign", "API Migration")
- Set realistic estimated hours and costs
- Regularly update completion percentages
- Mark jobs as "On Hold" if blocked rather than deleting them

### Phase Management
- Break jobs into logical phases (e.g., Design → Development → Testing → Deployment)
- Use phase dependencies to enforce workflow order
- Define clear deliverables for each phase
- Set acceptance criteria to define "done"

### Calendar Scheduling
- Use color coding for different types of work (e.g., blue for development, green for testing)
- Filter the calendar by project or assignee to reduce clutter
- Drag events to reschedule quickly
- Use all-day events for milestones or deadlines

### Progress Tracking
- Link tasks to phases for detailed tracking
- Log all work time in timesheets
- Review actual vs estimated hours regularly
- Update phase completion as work progresses

## Troubleshooting

### Menu Items Not Showing
If the menu items don't appear:
1. Check user permissions
2. Verify menu items exist in the `menu` table (IDs: 57, 58, 59)
3. Clear browser cache

### Search Not Working
If AJAX search fails:
1. Check browser console for errors
2. Verify routes are accessible
3. Clear CodeIgniter cache: `php spark cache:clear`

### Calendar Not Loading
If FullCalendar doesn't display:
1. Check browser console for JavaScript errors
2. Verify FullCalendar CDN is accessible
3. Check that `/project_job_scheduler/getEvents` returns valid JSON

## Support & Documentation

- Full implementation status: `/home/bwalia/workstation-ci4/READMEs/PROJECT_JOBS_IMPLEMENTATION_STATUS.md`
- Database migrations: `/home/bwalia/workstation-ci4/ci4/app/Database/Migrations/2025-10-14-152649_CreateProjectJobsTables.php`
- SQL files: `/home/bwalia/workstation-ci4/ci4/SQLs/create_project_jobs_table.sql` (and related)

---

**System Version**: 1.0
**Last Updated**: 2025-10-15
**Status**: Production Ready ✅
