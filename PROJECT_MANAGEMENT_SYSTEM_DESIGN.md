# Project Management System Design

## Overview
Comprehensive project management system extending existing `/projects` module with jobs, job phases, task linking, and time tracking integration.

## Database Tables

### 1. project_jobs
Links jobs to projects with scheduling and assignment capabilities.

```sql
CREATE TABLE `project_jobs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `uuid` VARCHAR(64) NOT NULL UNIQUE,
    `uuid_business_id` VARCHAR(64) NOT NULL,
    `uuid_project_id` VARCHAR(64) NOT NULL COMMENT 'FK to projects.uuid',
    `job_number` VARCHAR(50) NOT NULL COMMENT 'e.g., JOB-001',
    `job_name` VARCHAR(255) NOT NULL,
    `job_description` TEXT NULL,
    `job_type` ENUM('Development', 'Design', 'Testing', 'Deployment', 'Support', 'Research', 'Other') DEFAULT 'Development',
    `priority` ENUM('Low', 'Normal', 'High', 'Urgent') DEFAULT 'Normal',
    `status` ENUM('Planning', 'In Progress', 'On Hold', 'Completed', 'Cancelled') DEFAULT 'Planning',

    -- Assignment
    `assigned_to_user_id` INT(11) NULL COMMENT 'FK to users.id',
    `assigned_to_employee_id` INT(11) NULL COMMENT 'FK to employees.id',
    `assigned_by` VARCHAR(64) NULL,
    `assigned_at` DATETIME NULL,

    -- Scheduling
    `planned_start_date` DATE NULL,
    `planned_end_date` DATE NULL,
    `actual_start_date` DATE NULL,
    `actual_end_date` DATE NULL,
    `estimated_hours` DECIMAL(10,2) NULL,
    `actual_hours` DECIMAL(10,2) NULL DEFAULT 0,

    -- Financial
    `estimated_cost` DECIMAL(15,2) NULL,
    `actual_cost` DECIMAL(15,2) NULL DEFAULT 0,
    `billable` TINYINT(1) DEFAULT 1,
    `hourly_rate` DECIMAL(10,2) NULL,

    -- Progress
    `completion_percentage` INT(3) DEFAULT 0,
    `notes` TEXT NULL,

    -- Audit
    `created_by` VARCHAR(64) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    INDEX `idx_uuid` (`uuid`),
    INDEX `idx_project` (`uuid_project_id`),
    INDEX `idx_business` (`uuid_business_id`),
    INDEX `idx_assigned_user` (`assigned_to_user_id`),
    INDEX `idx_assigned_employee` (`assigned_to_employee_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_dates` (`planned_start_date`, `planned_end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 2. project_job_phases
Break jobs into phases for detailed tracking.

```sql
CREATE TABLE `project_job_phases` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `uuid` VARCHAR(64) NOT NULL UNIQUE,
    `uuid_business_id` VARCHAR(64) NOT NULL,
    `uuid_project_job_id` VARCHAR(64) NOT NULL COMMENT 'FK to project_jobs.uuid',
    `phase_number` VARCHAR(50) NOT NULL COMMENT 'e.g., PHASE-001',
    `phase_name` VARCHAR(255) NOT NULL,
    `phase_description` TEXT NULL,
    `phase_order` INT(3) DEFAULT 1 COMMENT 'Display order',
    `status` ENUM('Not Started', 'In Progress', 'Completed', 'Blocked') DEFAULT 'Not Started',

    -- Assignment
    `assigned_to_user_id` INT(11) NULL COMMENT 'FK to users.id',
    `assigned_to_employee_id` INT(11) NULL COMMENT 'FK to employees.id',

    -- Scheduling
    `planned_start_date` DATE NULL,
    `planned_end_date` DATE NULL,
    `actual_start_date` DATE NULL,
    `actual_end_date` DATE NULL,
    `estimated_hours` DECIMAL(10,2) NULL,
    `actual_hours` DECIMAL(10,2) NULL DEFAULT 0,

    -- Dependencies
    `depends_on_phase_uuid` VARCHAR(64) NULL COMMENT 'Phase that must complete before this one',

    -- Progress
    `completion_percentage` INT(3) DEFAULT 0,
    `notes` TEXT NULL,

    -- Deliverables
    `deliverables` TEXT NULL COMMENT 'Expected deliverables for this phase',
    `acceptance_criteria` TEXT NULL,

    -- Audit
    `created_by` VARCHAR(64) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    INDEX `idx_uuid` (`uuid`),
    INDEX `idx_job` (`uuid_project_job_id`),
    INDEX `idx_business` (`uuid_business_id`),
    INDEX `idx_order` (`phase_order`),
    INDEX `idx_assigned_user` (`assigned_to_user_id`),
    INDEX `idx_assigned_employee` (`assigned_to_employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 3. project_job_scheduler
Calendar view for drag-and-drop job assignment.

```sql
CREATE TABLE `project_job_scheduler` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `uuid` VARCHAR(64) NOT NULL UNIQUE,
    `uuid_business_id` VARCHAR(64) NOT NULL,
    `uuid_project_job_id` VARCHAR(64) NOT NULL COMMENT 'FK to project_jobs.uuid',
    `uuid_phase_id` VARCHAR(64) NULL COMMENT 'FK to project_job_phases.uuid (optional)',

    -- Assignment
    `assigned_to_user_id` INT(11) NULL COMMENT 'FK to users.id',
    `assigned_to_employee_id` INT(11) NULL COMMENT 'FK to employees.id',

    -- Schedule
    `schedule_date` DATE NOT NULL,
    `start_time` TIME NULL,
    `end_time` TIME NULL,
    `all_day` TINYINT(1) DEFAULT 0,
    `duration_hours` DECIMAL(5,2) NULL,

    -- Display
    `title` VARCHAR(255) NOT NULL,
    `color` VARCHAR(7) DEFAULT '#667eea' COMMENT 'Hex color for calendar display',
    `notes` TEXT NULL,

    -- Status
    `status` ENUM('Scheduled', 'In Progress', 'Completed', 'Cancelled') DEFAULT 'Scheduled',

    -- Audit
    `created_by` VARCHAR(64) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `modified_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    INDEX `idx_uuid` (`uuid`),
    INDEX `idx_job` (`uuid_project_job_id`),
    INDEX `idx_phase` (`uuid_phase_id`),
    INDEX `idx_business` (`uuid_business_id`),
    INDEX `idx_date` (`schedule_date`),
    INDEX `idx_assigned_user` (`assigned_to_user_id`),
    INDEX `idx_assigned_employee` (`assigned_to_employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 4. Extend tasks table
Add fields to link tasks with jobs and phases.

```sql
ALTER TABLE `tasks`
ADD COLUMN `uuid_project_job_id` VARCHAR(64) NULL COMMENT 'FK to project_jobs.uuid' AFTER `project_uuid`,
ADD COLUMN `uuid_job_phase_id` VARCHAR(64) NULL COMMENT 'FK to project_job_phases.uuid' AFTER `uuid_project_job_id`,
ADD INDEX `idx_project_job` (`uuid_project_job_id`),
ADD INDEX `idx_job_phase` (`uuid_job_phase_id`);
```

### 5. Extend timeslips table
Add fields to link timeslips with jobs and phases for accurate tracking.

```sql
ALTER TABLE `timeslips`
ADD COLUMN `uuid_project_job_id` VARCHAR(64) NULL COMMENT 'FK to project_jobs.uuid' AFTER `uuid_project_id`,
ADD COLUMN `uuid_job_phase_id` VARCHAR(64) NULL COMMENT 'FK to project_job_phases.uuid' AFTER `uuid_project_job_id`,
ADD COLUMN `uuid_task_id` VARCHAR(64) NULL COMMENT 'FK to tasks.uuid' AFTER `uuid_job_phase_id`,
ADD INDEX `idx_project_job` (`uuid_project_job_id`),
ADD INDEX `idx_job_phase` (`uuid_job_phase_id`),
ADD INDEX `idx_task` (`uuid_task_id`);
```

## Data Relationships

```
projects (existing)
  └── project_jobs
        ├── project_job_phases
        │     ├── tasks (extended)
        │     │     └── timeslips (extended)
        │     └── project_job_scheduler
        └── project_job_scheduler
              └── timeslips (extended)
```

## Models to Create

### 1. ProjectJobs_model.php
```php
- getNextJobNumber($businessUuid, $projectUuid)
- getJobsWithDetails($businessUuid, $filters)
- getJobsByProject($projectUuid)
- getJobsByUser($userId)
- getJobsByEmployee($employeeId)
- updateActualHours($jobUuid, $hours)
- updateCompletionPercentage($jobUuid, $percentage)
- getJobTimelineSummary($jobUuid)
- getOverdueJobs($businessUuid)
```

### 2. ProjectJobPhases_model.php
```php
- getNextPhaseNumber($businessUuid, $jobUuid)
- getPhasesByJob($jobUuid)
- getPhasesWithDependencies($jobUuid)
- updatePhaseProgress($phaseUuid, $percentage)
- checkDependenciesCompleted($phaseUuid)
- getBlockedPhases($jobUuid)
```

### 3. ProjectJobScheduler_model.php
```php
- getScheduleByDateRange($businessUuid, $startDate, $endDate)
- getScheduleByUser($userId, $dateRange)
- getScheduleByEmployee($employeeId, $dateRange)
- createScheduleEntry($data)
- updateScheduleEntry($uuid, $data)
- dragDropUpdate($uuid, $newDate, $newAssignment)
- getCalendarEvents($businessUuid, $filters)
```

## Controllers to Create

### 1. ProjectJobs.php
- index() - List jobs
- edit($uuid) - Create/edit job
- update() - Save job
- delete($uuid) - Delete job
- jobsList() - DataTables endpoint
- byProject($projectUuid) - Jobs for specific project
- assignToUser($jobUuid, $userId) - Assign job
- updateProgress($jobUuid) - Update completion

### 2. ProjectJobPhases.php
- index() - List phases
- edit($uuid) - Create/edit phase
- update() - Save phase
- delete($uuid) - Delete phase
- phasesByJob($jobUuid) - Phases for specific job
- updateStatus($phaseUuid) - Change phase status
- checkDependencies($phaseUuid) - Validate dependencies

### 3. ProjectJobScheduler.php
- calendar() - Calendar view
- scheduleList() - Get schedule data
- create() - Create schedule entry
- update($uuid) - Update entry
- delete($uuid) - Delete entry
- dragDrop() - Handle drag-and-drop
- assignJob() - Assign job to user/employee

## Views to Create

### 1. project_jobs/list.php
- DataTable with jobs
- Filter by project, status, assigned user
- Progress bars
- Quick actions (edit, assign, view timeline)

### 2. project_jobs/edit.php
- Job details form
- Assignment section
- Phase management
- Task linking
- Time tracking summary

### 3. project_job_phases/list.php
- Phases for a job
- Drag-to-reorder
- Dependency visualization
- Progress indicators

### 4. project_job_scheduler/calendar.php
- Full calendar view (FullCalendar.js)
- Drag-and-drop job assignment
- Color-coded by job type/priority
- Filter by user/employee
- Month/week/day views

### 5. Extended tasks/edit.php
- Add job and phase dropdowns
- Auto-populate from project selection
- Show related timeslips

### 6. Extended timeslips/edit.php
- Add job and phase dropdowns
- Link to tasks
- Show job/phase context

## API Endpoints

### ProjectJobs API
```
GET    /api/v2/project_jobs                  - List jobs
GET    /api/v2/project_jobs/{uuid}           - Get job details
POST   /api/v2/project_jobs                  - Create job
PUT    /api/v2/project_jobs/{uuid}           - Update job
DELETE /api/v2/project_jobs/{uuid}           - Delete job
GET    /api/v2/project_jobs/by-project/{uuid} - Jobs by project
POST   /api/v2/project_jobs/{uuid}/assign    - Assign job
```

### ProjectJobPhases API
```
GET    /api/v2/project_job_phases            - List phases
GET    /api/v2/project_job_phases/{uuid}     - Get phase details
POST   /api/v2/project_job_phases            - Create phase
PUT    /api/v2/project_job_phases/{uuid}     - Update phase
DELETE /api/v2/project_job_phases/{uuid}     - Delete phase
GET    /api/v2/project_job_phases/by-job/{uuid} - Phases by job
```

### ProjectJobScheduler API
```
GET    /api/v2/scheduler/calendar           - Get calendar events
POST   /api/v2/scheduler/schedule           - Create schedule
PUT    /api/v2/scheduler/schedule/{uuid}    - Update schedule
DELETE /api/v2/scheduler/schedule/{uuid}    - Delete schedule
POST   /api/v2/scheduler/drag-drop          - Handle drag-drop
GET    /api/v2/scheduler/by-user/{id}       - User's schedule
GET    /api/v2/scheduler/by-employee/{id}   - Employee's schedule
```

## Frontend Libraries

### Calendar Component
**FullCalendar.js** - For drag-and-drop calendar
```html
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
```

Features:
- Drag-and-drop events
- Resize events
- Multiple views (month, week, day)
- Event color coding
- Resource views (by user/employee)

### Gantt Chart (Optional)
**Frappe Gantt** - For timeline visualization
```html
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
```

## Implementation Benefits

1. **Complete Project Tracking**: Projects → Jobs → Phases → Tasks → Timeslips
2. **Resource Management**: Assign jobs to users or employees with calendar view
3. **Time Accuracy**: Direct linking between work units and time tracking
4. **Progress Visibility**: Track completion at every level
5. **Dependency Management**: Ensure phases complete in correct order
6. **Cost Tracking**: Estimate vs actual hours and costs
7. **Visual Scheduling**: Drag-and-drop calendar for easy assignment
8. **Reporting**: Comprehensive data for project reports

## Next Steps

1. Create database migrations for all tables
2. Create models with all methods
3. Create controllers with CRUD operations
4. Create views with calendar integration
5. Create API endpoints
6. Add routes
7. Add menu items
8. Test complete workflow
9. Create documentation
