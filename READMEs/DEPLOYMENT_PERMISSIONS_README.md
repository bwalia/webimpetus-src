# Deployment Permissions

This document explains the deployment permissions system for managing user access to deployments across DTAP environments.

## Overview

The `deployment_permissions` table controls which users can deploy, approve, and rollback deployments in different environments (Development, Testing, Acceptance, Production).

## Table Structure

### deployment_permissions

| Column | Type | Description |
|--------|------|-------------|
| id | int(11) | Primary key |
| uuid | varchar(36) | Unique identifier |
| uuid_business_id | varchar(36) | Business/Tenant identifier |
| uuid_user_id | varchar(36) | User UUID who has permission |
| environment | enum | DTAP environment (Development, Testing, Acceptance, Production) |
| can_deploy | tinyint(1) | Can create/execute deployments (default: 1) |
| can_approve | tinyint(1) | Can approve deployments (default: 0) |
| can_rollback | tinyint(1) | Can rollback deployments (default: 0) |
| granted_by | varchar(36) | UUID of user who granted permission |
| granted_date | datetime | When permission was granted |
| expires_date | datetime | Optional expiration date |
| notes | text | Additional notes about this permission |
| status | tinyint(1) | 1 = active, 0 = inactive |
| created | datetime | Record creation timestamp |
| modified | datetime | Last modification timestamp |

## SQL Files

### Location
- **SQL File**: [/SQLs/create_deployment_permissions.sql](SQLs/create_deployment_permissions.sql)
- **Migration**: [/ci4/app/Database/Migrations/2025-10-11-000000_CreateDeploymentPermissionsTable.php](ci4/app/Database/Migrations/2025-10-11-000000_CreateDeploymentPermissionsTable.php)

### Installation

#### Option 1: Using SQL File (Quick)
```bash
# Run the SQL file directly
docker exec -i workerra-ci-db mariadb -uworkerra-ci-dev -pCHANGE_ME myworkstation_dev < SQLs/create_deployment_permissions.sql
```

#### Option 2: Using CodeIgniter Migration
```bash
# Run the migration
php spark migrate
```

## Default Permissions

The installation automatically grants the admin user (id=1) full permissions across all environments:

- **Development**: Deploy, Approve, Rollback ✓
- **Testing**: Deploy, Approve, Rollback ✓
- **Acceptance**: Deploy, Approve, Rollback ✓
- **Production**: Deploy, Approve, Rollback ✓

## Usage in Code

The permissions are checked in the [Deployments controller](ci4/app/Controllers/Deployments.php):

```php
// Check if user has deployment permission
$hasPermission = $this->db->table('deployment_permissions')
    ->where('uuid_user_id', $userUuid)
    ->where('environment', $environment)
    ->where('uuid_business_id', $this->businessUuid)
    ->where('status', 1)
    ->countAllResults() > 0;
```

## Granting Permissions

To grant deployment permissions to a user:

```sql
INSERT INTO deployment_permissions
  (uuid, uuid_business_id, uuid_user_id, environment, can_deploy, can_approve, can_rollback, granted_by, notes, status, created)
VALUES
  (UUID(), 'business-uuid', 'user-uuid', 'Production', 1, 1, 0, 'admin-uuid', 'Limited production access', 1, NOW());
```

## Permission Levels

1. **can_deploy**: User can create and execute deployments
2. **can_approve**: User can approve pending deployments (for approval workflows)
3. **can_rollback**: User can rollback deployments to previous versions

## Environment Types (DTAP)

- **Development**: Development environment for active development
- **Testing**: Testing/QA environment
- **Acceptance**: UAT/Staging environment
- **Production**: Live production environment

## Security Considerations

- Use the unique constraint `idx_unique_user_env` to prevent duplicate permissions
- Set `expires_date` for temporary access grants
- Use `status=0` to revoke permissions without deleting records
- Track who granted permissions using `granted_by` for audit trails

## Verification

Check current permissions:

```sql
SELECT
  dp.id,
  u.name AS user_name,
  dp.environment,
  dp.can_deploy,
  dp.can_approve,
  dp.can_rollback,
  dp.status,
  dp.granted_date,
  dp.expires_date
FROM deployment_permissions dp
LEFT JOIN users u ON u.uuid = dp.uuid_user_id
WHERE dp.status = 1
ORDER BY dp.environment, u.name;
```

## Related Tables

- **deployments**: Main deployments table
- **users**: User information
- **businesses**: Business/tenant information
- **services**: Services being deployed

## See Also

- [Deployments Controller](ci4/app/Controllers/Deployments.php)
- [Deployments Migration](ci4/app/Database/Migrations/2025-10-10-000000_CreateDeploymentsTable.php)
