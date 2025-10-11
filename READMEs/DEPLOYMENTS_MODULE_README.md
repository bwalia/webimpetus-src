# Deployments Module

## Overview
The Deployments module is a comprehensive deployment tracking system for managing service deployments across DTAP (Development, Testing, Acceptance, Production) environments. It provides complete visibility into deployment activities, links to related tasks and incidents, and maintains detailed deployment history.

## Features

### Core Functionality
- **DTAP Environment Support**: Track deployments across all four environments
- **Service Integration**: Link deployments to specific services
- **Task & Incident Tracking**: Connect deployments to related tasks and incidents
- **Version Control Integration**: Git branch, commit hash, and repository tracking
- **Deployment Types**: Initial, Update, Hotfix, Rollback, Configuration
- **Status Tracking**: Planned, In Progress, Completed, Failed, Rolled Back
- **Priority Levels**: Low, Medium, High, Critical

### Advanced Features
- **Approval Workflow**: Optional approval requirements with approver tracking
- **Downtime Management**: Track scheduled downtime windows
- **Health Monitoring**: Health check URL and status tracking
- **Rollback Planning**: Document rollback procedures
- **Deployment Notes**: Technical documentation and execution details
- **Affected Components**: Track all impacted systems

### UI Components
- **List View** with:
  - Summary cards showing deployment statistics
  - Color-coded environment badges
  - Status indicators
  - Priority flags
  - Filterable data table

- **Edit/Add Form** with 4 organized tabs:
  1. General Information
  2. Technical Details
  3. Links & Approval
  4. Downtime & Health

### API Endpoints
Full RESTful API with Swagger documentation:
- `GET /api/v2/deployments` - List all deployments
- `GET /api/v2/deployments/{uuid}` - Get single deployment
- `POST /api/v2/deployments` - Create deployment
- `PUT /api/v2/deployments/{uuid}` - Update deployment
- `DELETE /api/v2/deployments/{uuid}` - Delete deployment
- `GET /api/v2/deployments/stats` - Get deployment statistics

## Database Schema

### Table: `deployments`

**Primary Fields:**
- `id` (int, auto-increment)
- `uuid` (varchar 36, unique identifier)
- `uuid_business_id` (varchar 36, multi-tenant support)

**Deployment Information:**
- `deployment_name` (varchar 255, required)
- `uuid_service_id` (varchar 36, links to services table)
- `environment` (enum: Development, Testing, Acceptance, Production)
- `version` (varchar 50)
- `deployment_type` (enum: Initial, Update, Hotfix, Rollback, Configuration)
- `deployment_status` (enum: Planned, In Progress, Completed, Failed, Rolled Back)
- `deployment_date` (datetime)
- `completed_date` (datetime)
- `deployed_by` (varchar 36, user UUID)

**Linking Fields:**
- `uuid_task_id` (varchar 36, links to tasks table)
- `uuid_incident_id` (varchar 36, links to incidents table)

**Technical Details:**
- `description` (text)
- `deployment_notes` (text)
- `rollback_plan` (text)
- `affected_components` (text)
- `git_commit_hash` (varchar 40)
- `git_branch` (varchar 100)
- `repository_url` (varchar 500)
- `deployment_url` (varchar 500)

**Downtime & Health:**
- `downtime_required` (tinyint)
- `downtime_start` (datetime)
- `downtime_end` (datetime)
- `health_check_url` (varchar 500)
- `health_check_status` (enum: Healthy, Degraded, Unhealthy, Unknown)

**Approval:**
- `approval_required` (tinyint)
- `approved_by` (varchar 36, user UUID)
- `approval_date` (datetime)

**Metadata:**
- `priority` (enum: Low, Medium, High, Critical)
- `status` (tinyint, active/inactive)
- `created` (datetime)
- `modified` (datetime)

## File Structure

```
ci4/
├── app/
│   ├── Controllers/
│   │   ├── Deployments.php                      # Main controller
│   │   └── Api/V2/Deployments.php              # API controller with Swagger
│   ├── Models/
│   │   └── Deployments_model.php               # Model with relations
│   ├── Views/
│   │   └── deployments/
│   │       ├── list.php                        # List view with summary cards
│   │       └── edit.php                        # Add/Edit form (4 tabs)
│   ├── Database/
│   │   └── Migrations/
│   │       └── 2025-10-10-000000_CreateDeploymentsTable.php
│   └── Config/
│       └── Routes.php                          # API routes
```

## Usage Examples

### Creating a Deployment

**Via UI:**
1. Navigate to `/deployments`
2. Click "Add New Deployment"
3. Fill in required fields:
   - Deployment Name
   - Environment
4. Optionally add:
   - Linked Service
   - Related Task or Incident
   - Git details
   - Approval requirements
   - Downtime windows
5. Save

**Via API:**
```bash
POST /api/v2/deployments
{
  "deployment_name": "Deploy User Service v2.1",
  "uuid_business_id": "xxx-xxx-xxx",
  "uuid_service_id": "yyy-yyy-yyy",
  "environment": "Production",
  "version": "2.1.0",
  "deployment_type": "Update",
  "deployment_status": "Planned",
  "deployment_date": "2025-10-15 02:00:00",
  "git_branch": "main",
  "git_commit_hash": "a1b2c3d4e5f6",
  "priority": "High",
  "approval_required": 1
}
```

### Tracking a Hotfix Deployment

```bash
POST /api/v2/deployments
{
  "deployment_name": "Hotfix: Fix login bug",
  "uuid_business_id": "xxx-xxx-xxx",
  "uuid_service_id": "yyy-yyy-yyy",
  "uuid_incident_id": "zzz-zzz-zzz",  # Link to incident
  "environment": "Production",
  "deployment_type": "Hotfix",
  "deployment_status": "In Progress",
  "priority": "Critical",
  "downtime_required": 1,
  "downtime_start": "2025-10-10 22:00:00",
  "downtime_end": "2025-10-10 22:30:00",
  "rollback_plan": "Revert to commit abc123 and restart services"
}
```

### Querying Deployments

**Get all Production deployments:**
```bash
GET /api/v2/deployments?uuid_business_id=xxx&environment=Production
```

**Get failed deployments:**
```bash
GET /api/v2/deployments?uuid_business_id=xxx&deployment_status=Failed
```

**Get deployment statistics:**
```bash
GET /api/v2/deployments/stats?uuid_business_id=xxx
```

## Integration Points

### 1. Services Module
- Link deployments to specific services
- Track deployment history per service
- Service-level deployment statistics

### 2. Tasks Module
- Connect deployments to deployment tasks
- Track task-driven deployments
- Deployment checklist integration

### 3. Incidents Module
- Link emergency deployments to incidents
- Track incident remediation deployments
- Incident resolution timeline

### 4. Users Module
- Track who performed deployments
- Approval workflow
- Deployment permissions

## Best Practices

### Deployment Planning
1. Always create deployment records in "Planned" status
2. Link to related tasks for scheduled deployments
3. Link to incidents for emergency deployments
4. Document rollback plans for Production deployments

### Production Deployments
1. Set `approval_required = 1`
2. Fill in `downtime_start` and `downtime_end` if applicable
3. Document `affected_components`
4. Add comprehensive `deployment_notes`
5. Test `health_check_url` after deployment

### Version Control
1. Always include `git_commit_hash`
2. Specify `git_branch`
3. Link to `repository_url`
4. Use semantic versioning in `version` field

### Post-Deployment
1. Update status to "Completed" or "Failed"
2. Set `completed_date`
3. Update `health_check_status`
4. Add any lessons learned to `deployment_notes`

## Navigation

The Deployments module is accessible from the left-hand navigation menu under "Deployments" (with rocket icon 🚀).

## Permissions

Users need appropriate permissions in the `menu` table to access the Deployments module. The menu entry is:
- Name: Deployments
- Link: /deployments
- Icon: fa fa-rocket

## Summary Cards

The list view displays 4 summary cards:
1. **Total Deployments** - All deployments count
2. **Completed** - Successfully completed deployments
3. **In Progress** - Currently deploying
4. **This Month** - Deployments in current month

## Environment Badges

Deployments are color-coded by environment:
- **Development** - Blue
- **Testing** - Yellow
- **Acceptance** - Pink
- **Production** - Green

## Future Enhancements

Potential additions:
- Deployment pipeline visualization
- Automated deployment triggers
- Integration with CI/CD tools
- Deployment metrics and KPIs
- Deployment calendar view
- Email notifications
- Deployment approval workflow
- Rollback automation

## Support

For issues or questions about the Deployments module:
1. Check the Swagger API documentation at `/api-docs`
2. Review this README
3. Check the database schema
4. Contact the development team
