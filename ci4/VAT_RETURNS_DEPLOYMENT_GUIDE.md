# VAT Returns Module - DTAP Deployment Guide

## Overview
This guide provides step-by-step instructions for deploying the VAT Returns module across all environments (Development, Testing, Acceptance, Production).

## Prerequisites

### Code Files Required
Ensure these files are deployed to your environment:

**Controllers:**
- `ci4/app/Controllers/Vat_returns.php`

**Models:**
- `ci4/app/Models/Vat_return_model.php`

**Views:**
- `ci4/app/Views/vat_returns/list.php`
- `ci4/app/Views/vat_returns/generate.php`
- `ci4/app/Views/vat_returns/preview.php`
- `ci4/app/Views/vat_returns/view.php`

**Migrations:**
- `ci4/app/Database/Migrations/2025-01-08-000000_CreateVatReturnsTable.php`

**SQL Deployment Script:**
- `ci4/vat_returns_deployment.sql`

## Deployment Steps

### Step 1: Deploy Application Code

Copy all the code files to your target environment:

```bash
# Example using rsync (adjust paths as needed)
rsync -avz ci4/app/Controllers/Vat_returns.php user@server:/path/to/app/Controllers/
rsync -avz ci4/app/Models/Vat_return_model.php user@server:/path/to/app/Models/
rsync -avz ci4/app/Views/vat_returns/ user@server:/path/to/app/Views/
rsync -avz ci4/app/Database/Migrations/2025-01-08-000000_CreateVatReturnsTable.php user@server:/path/to/app/Database/Migrations/
```

Or use your existing deployment pipeline (Git, CI/CD, etc.)

### Step 2: Run Database Migration

SSH into your server and run the migration:

```bash
# Navigate to your application directory
cd /path/to/your/app

# Run migrations
php spark migrate
```

**Expected Output:**
```
Running all new migrations...
	Running: (App) 2025-01-08-000000_App\Database\Migrations\CreateVatReturnsTable
Migrations complete.
```

**Verification:**
```bash
# Verify table was created
php spark db:table vat_returns
```

### Step 3: Run SQL Deployment Script

Execute the SQL script to add menu items and permissions:

#### Option A: Using MySQL/MariaDB CLI

```bash
# For MySQL
mysql -u your_username -p your_database < ci4/vat_returns_deployment.sql

# For MariaDB
mariadb -u your_username -p your_database < ci4/vat_returns_deployment.sql
```

#### Option B: Using Docker (if applicable)

```bash
# Development environment example
docker exec webimpetus-db mariadb -u wsl_dev -pCHANGE_ME myworkstation_dev < ci4/vat_returns_deployment.sql

# Or copy file into container first
docker cp ci4/vat_returns_deployment.sql webimpetus-db:/tmp/
docker exec webimpetus-db mariadb -u wsl_dev -pCHANGE_ME myworkstation_dev < /tmp/vat_returns_deployment.sql
```

#### Option C: Using phpMyAdmin or Database GUI

1. Open your database management tool
2. Select your database
3. Go to SQL tab
4. Copy and paste contents of `vat_returns_deployment.sql`
5. Click "Execute" or "Go"

### Step 4: Verify Deployment

The SQL script will output verification information. Check for:

✅ **Menu Created:**
```
Menu Information: VAT Returns Menu ID: [ID number]
```

✅ **Table Exists:**
```
Table Status: SUCCESS: vat_returns table exists
```

✅ **Admin User Has Access:**
```
Has VAT Access: YES
```

### Step 5: User Action Required

⚠️ **IMPORTANT:** All users must **log out and log back in** for the new menu to appear.

This is because:
- Menu permissions are cached in the user session
- Sessions only refresh on login
- No code changes are needed - it's automatic

## Environment-Specific Notes

### Development Environment
- Database: `myworkstation_dev`
- User: `wsl_dev`
- Container: `webimpetus-db`
- Already deployed ✅

### Testing Environment
- Update database credentials in the SQL command
- Test the migration first before running SQL script
- Verify with test users

### Acceptance Environment
- Ensure backup is taken before deployment
- Run during maintenance window if possible
- Test with actual user accounts

### Production Environment
- **CRITICAL:** Take full database backup first
- Schedule during low-traffic period
- Test rollback procedure beforehand
- Notify users they need to re-login

## Rollback Procedure

If you need to remove the VAT Returns module:

```sql
-- Get the menu ID
SET @vat_menu_id = (SELECT id FROM menu WHERE name = 'VAT Returns' LIMIT 1);

-- Remove from menu
DELETE FROM menu WHERE name = 'VAT Returns' AND link = '/vat_returns';

-- Remove from user permissions
UPDATE users
SET permissions = JSON_REMOVE(
    permissions,
    JSON_UNQUOTE(JSON_SEARCH(permissions, 'one', CAST(@vat_menu_id AS CHAR)))
)
WHERE JSON_SEARCH(permissions, 'one', CAST(@vat_menu_id AS CHAR)) IS NOT NULL;

-- Drop table
DROP TABLE IF EXISTS vat_returns;
```

## Granting Access to Additional Users

### Method 1: Individual Users

```sql
-- Get the VAT menu ID
SET @vat_menu_id = (SELECT id FROM menu WHERE name = 'VAT Returns' LIMIT 1);

-- Grant to specific user by email
UPDATE users
SET permissions = JSON_ARRAY_APPEND(permissions, '$', CAST(@vat_menu_id AS CHAR))
WHERE email = 'user@example.com'
AND JSON_SEARCH(permissions, 'one', CAST(@vat_menu_id AS CHAR)) IS NULL;
```

### Method 2: Multiple Users

```sql
SET @vat_menu_id = (SELECT id FROM menu WHERE name = 'VAT Returns' LIMIT 1);

UPDATE users
SET permissions = JSON_ARRAY_APPEND(permissions, '$', CAST(@vat_menu_id AS CHAR))
WHERE email IN ('user1@example.com', 'user2@example.com', 'user3@example.com')
AND JSON_SEARCH(permissions, 'one', CAST(@vat_menu_id AS CHAR)) IS NULL;
```

### Method 3: Role-Based (if using UUID roles)

```sql
-- Get the VAT menu UUID
SET @vat_menu_uuid = (SELECT uuid FROM menu WHERE name = 'VAT Returns' LIMIT 1);

-- Grant to a role
INSERT INTO roles__permissions (uuid, role_id, permission_id)
SELECT UUID(), 'your-role-uuid-here', @vat_menu_uuid
WHERE NOT EXISTS (
    SELECT 1 FROM roles__permissions
    WHERE role_id = 'your-role-uuid-here'
    AND permission_id = @vat_menu_uuid
);
```

## Troubleshooting

### Issue: "Table 'vat_returns' doesn't exist"
**Solution:** Run the migration first
```bash
php spark migrate
```

### Issue: "403 Forbidden" when accessing /vat_returns
**Solution:**
1. Check user has permission (run verification query)
2. User must log out and log back in
3. Clear browser cache if needed

### Issue: Menu item doesn't appear
**Solution:**
1. Verify menu was inserted: `SELECT * FROM menu WHERE name = 'VAT Returns';`
2. User must log out and log back in
3. Check user permissions include the menu ID

### Issue: Migration already ran
**Solution:** This is safe to ignore. The migration will be skipped automatically.

### Issue: Menu already exists error
**Solution:** The SQL script has duplicate prevention. If you see an error, the menu already exists and you can proceed.

## Testing Checklist

After deployment, verify:

- [ ] Migration completed successfully
- [ ] SQL script executed without errors
- [ ] `vat_returns` table exists in database
- [ ] Menu item appears in `menu` table
- [ ] Admin user has menu ID in permissions
- [ ] Admin can access `/vat_returns` after re-login
- [ ] Can generate a VAT return for a past quarter
- [ ] Preview shows correct calculations
- [ ] Can save a VAT return
- [ ] Can view saved VAT return
- [ ] Can export to CSV
- [ ] Can submit a VAT return
- [ ] Cannot delete submitted return

## Support and Documentation

- **Full Documentation:** `ci4/VAT_RETURNS_README.md`
- **API Endpoints:** Documented in README
- **Database Schema:** See migration file or run `DESCRIBE vat_returns;`

## Deployment Checklist

Use this checklist for each environment:

### Pre-Deployment
- [ ] Backup database
- [ ] Code files copied to server
- [ ] Review deployment plan with team
- [ ] Schedule maintenance window (production only)

### Deployment
- [ ] Run `php spark migrate`
- [ ] Execute `vat_returns_deployment.sql`
- [ ] Verify table creation
- [ ] Verify menu creation
- [ ] Verify admin permissions

### Post-Deployment
- [ ] Test access as admin user (after re-login)
- [ ] Test generating a VAT return
- [ ] Test export functionality
- [ ] Notify users to re-login
- [ ] Document any issues

### Production Only
- [ ] Monitor error logs for 24 hours
- [ ] Verify no performance impact
- [ ] Collect user feedback
- [ ] Update runbook/documentation

## Contact

For issues during deployment, contact your system administrator or development team.

## Version History

- **v1.0** - 2025-01-08 - Initial release
  - UK VAT Returns quarterly reporting
  - UK vs Non-UK VAT separation
  - CSV export functionality
  - Submit/draft workflow
