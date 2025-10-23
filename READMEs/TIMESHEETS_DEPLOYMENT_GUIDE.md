# Timesheets Module - Production Deployment Guide

## Overview
This guide provides step-by-step instructions for deploying the new Timesheets module to your production environment.

## What is Being Deployed
A complete, modern timesheet tracking system with:
- Real-time timer functionality
- Bulk invoice generation
- Advanced filtering and reporting
- Summary dashboard cards
- Full permission system integration

## Pre-Deployment Checklist

### 1. Backup Database
```bash
# Create a backup of your production database
mysqldump -u [username] -p [database_name] > backup_before_timesheets_$(date +%Y%m%d).sql
```

### 2. Verify Required Tables Exist
The module integrates with existing tables:
- ✅ `menu` (for navigation)
- ✅ `employees` (for time tracking)
- ✅ `projects` (for project assignment)
- ✅ `tasks` (for task assignment)
- ✅ `customers` (for customer assignment)
- ✅ `sales_invoices` (for invoice generation)
- ✅ `user_permissions` (for access control)

### 3. Check PHP Version
- Minimum: PHP 7.4
- Recommended: PHP 8.0+

## Deployment Steps

### Step 1: Database Changes

**Execute the SQL file:**
```bash
# From your production server
mysql -u [username] -p [database_name] < SQLs/timesheets_module_production_deployment.sql
```

**Or manually execute:**
1. Log into your production database
2. Run the SQL from: `SQLs/timesheets_module_production_deployment.sql`

**What this does:**
- Creates the `timesheets` table with 25 columns
- Adds "Timesheets" menu entry (ID will auto-generate)
- Creates necessary indexes for performance

### Step 2: Deploy Application Files

**Upload these files to production:**

```bash
# From your local development environment
rsync -avz ci4/app/Controllers/Timesheets.php production:/var/www/html/app/Controllers/
rsync -avz ci4/app/Models/Timesheets_model.php production:/var/www/html/app/Models/
rsync -avz ci4/app/Views/timesheets/ production:/var/www/html/app/Views/timesheets/
rsync -avz ci4/app/Traits/PermissionTrait.php production:/var/www/html/app/Traits/
```

**Update existing file:**
```bash
# Deploy updated Routes.php
rsync -avz ci4/app/Config/Routes.php production:/var/www/html/app/Config/
```

**Files to deploy:**
1. **Controllers** (1 file):
   - `ci4/app/Controllers/Timesheets.php` - Main controller (353 lines)

2. **Models** (1 file):
   - `ci4/app/Models/Timesheets_model.php` - Business logic (229 lines)

3. **Views** (2 files):
   - `ci4/app/Views/timesheets/list.php` - List view (498 lines)
   - `ci4/app/Views/timesheets/edit.php` - Entry form (449 lines)

4. **Traits** (1 file - if not already deployed):
   - `ci4/app/Traits/PermissionTrait.php` - Permission helpers

5. **Configuration** (1 file - updated):
   - `ci4/app/Config/Routes.php` - Added routes at lines 54 and 214-225

### Step 3: Clear Caches

```bash
# SSH into production server
cd /var/www/html

# Clear CodeIgniter cache
php spark cache:clear

# Clear OPcache (if using)
sudo service php-fpm reload
# OR
sudo service php8.0-fpm reload

# Restart web server
sudo service nginx restart
# OR
sudo service apache2 restart
```

**For Docker environments:**
```bash
docker restart [php-container-name]
docker restart [nginx-container-name]
```

### Step 4: Regenerate Autoloader

```bash
# SSH into production server
cd /var/www/html

# Regenerate composer autoload
composer dump-autoload -o
```

### Step 5: Set File Permissions

```bash
# Ensure proper permissions
chmod 755 /var/www/html/app/Controllers/Timesheets.php
chmod 755 /var/www/html/app/Models/Timesheets_model.php
chmod 755 /var/www/html/app/Views/timesheets/
chmod 644 /var/www/html/app/Views/timesheets/*.php
chmod 755 /var/www/html/app/Traits/
chmod 644 /var/www/html/app/Traits/PermissionTrait.php

# If using www-data user
chown -R www-data:www-data /var/www/html/app/
```

## Post-Deployment Verification

### 1. Database Verification

```sql
-- Check table was created
DESCRIBE timesheets;

-- Check menu entry was added
SELECT id, name, link, icon, sort_order
FROM menu
WHERE name = 'Timesheets';

-- Expected result: One row showing Timesheets menu with sort_order 9822
```

### 2. Application Verification

**Test as Admin User (ID = 1):**

1. **Log out and log back in** (required to refresh permissions)

2. **Check menu visibility:**
   - Look for "Timesheets" menu item
   - Should appear with clock icon
   - Should be near "Timeslips" in the menu

3. **Access list page:**
   - Navigate to `/timesheets`
   - Should see 4 summary cards (all showing 0 initially)
   - Should see empty DataTable
   - Should see "Start New Timer" button

4. **Create first timesheet:**
   - Click "Start New Timer"
   - Select an employee
   - Optionally select project/task
   - Click "Start Timer"
   - Should redirect to list with running timer

5. **Stop timer:**
   - Click edit on the running timesheet
   - Click "Stop Timer"
   - Should auto-calculate duration and amount

6. **Create invoice:**
   - Create multiple completed timesheets
   - Check the boxes next to them
   - Click "Create Invoice from Selected"
   - Should create and redirect to new invoice

### 3. Permission Verification

**Test as Regular User:**

1. Log in as a non-admin user
2. Navigate to User Management
3. Edit the test user's permissions
4. Grant/revoke timesheets permissions (read/create/update/delete)
5. Log out and back in as that user
6. Verify buttons enable/disable based on permissions

### 4. Performance Check

```sql
-- Check indexes were created
SHOW INDEXES FROM timesheets;

-- Expected: 8 indexes (PRIMARY + 7 additional)
```

## Troubleshooting

### Issue: Menu item not appearing

**Solution:**
```bash
# Clear session cache
# Users must log out and log back in
# Or clear sessions table:
TRUNCATE sessions;
```

### Issue: "Trait not found" error

**Solution:**
```bash
# Regenerate autoloader
composer dump-autoload -o

# Restart PHP processes
sudo service php-fpm restart
```

### Issue: "Table doesn't exist" error

**Solution:**
```sql
-- Verify table exists
SHOW TABLES LIKE 'timesheets';

-- If not, run the SQL again
source SQLs/timesheets_module_production_deployment.sql;
```

### Issue: Permission denied errors

**Solution:**
```bash
# Check file ownership
ls -la /var/www/html/app/Controllers/Timesheets.php
ls -la /var/www/html/app/Traits/

# Fix ownership
chown www-data:www-data /var/www/html/app/Controllers/Timesheets.php
chown -R www-data:www-data /var/www/html/app/Traits/

# Fix permissions
chmod 755 /var/www/html/app/Traits/
chmod 644 /var/www/html/app/Traits/PermissionTrait.php
```

### Issue: Routes not working

**Solution:**
```bash
# Verify Routes.php was updated
grep -n "timesheets" /var/www/html/app/Config/Routes.php

# Should show lines 54 and 214-225
# If not, redeploy Routes.php

# Clear route cache
php spark cache:clear
```

### Issue: Summary cards showing wrong data

**Solution:**
```sql
-- Verify data is being inserted correctly
SELECT * FROM timesheets LIMIT 5;

-- Check uuid_business_id matches session
SELECT DISTINCT uuid_business_id FROM timesheets;
```

## Rollback Procedure

If you need to rollback the deployment:

### 1. Remove Database Changes

```sql
-- Drop the timesheets table
DROP TABLE IF EXISTS timesheets;

-- Remove menu entry (get ID first)
SELECT id FROM menu WHERE name = 'Timesheets';
DELETE FROM menu WHERE name = 'Timesheets';
```

### 2. Remove Application Files

```bash
rm /var/www/html/app/Controllers/Timesheets.php
rm /var/www/html/app/Models/Timesheets_model.php
rm -rf /var/www/html/app/Views/timesheets/
```

### 3. Restore Original Routes.php

```bash
# Restore from backup
cp /var/www/html/app/Config/Routes.php.backup /var/www/html/app/Config/Routes.php
```

### 4. Clear Caches

```bash
php spark cache:clear
service php-fpm restart
service nginx restart
```

## Security Considerations

1. **Permissions**: The module respects the existing permission system
   - Admin users (ID=1) have full access automatically
   - Regular users inherit permissions from roles
   - User-specific permissions can be configured

2. **Data Isolation**: All queries filter by `uuid_business_id`
   - Multi-tenant safe
   - Business data remains isolated

3. **SQL Injection**: All queries use parameter binding
   - CodeIgniter query builder used throughout
   - No raw SQL concatenation

4. **Access Control**: All controller methods check permissions
   - Uses PermissionTrait
   - Returns 403 error for unauthorized access

## Performance Optimization

### 1. Recommended Indexes (already included)
```sql
-- These are created automatically, but verify:
SHOW INDEXES FROM timesheets;
```

### 2. For High Volume Usage

If you expect thousands of timesheets per business:

```sql
-- Add composite indexes for common queries
CREATE INDEX idx_business_employee_created
ON timesheets(uuid_business_id, employee_id, created_at);

CREATE INDEX idx_business_status_created
ON timesheets(uuid_business_id, status, created_at);

CREATE INDEX idx_business_invoiced
ON timesheets(uuid_business_id, is_invoiced, is_billable);
```

## Support & Documentation

- **Implementation Files**: All code is in `/home/bwalia/workerra-ci/ci4/app/`
- **SQL Files**: Database scripts in `/home/bwalia/workerra-ci/SQLs/`
- **Permission System**: See `PERMISSION_TRAIT_GUIDE.md` for details
- **Submit Button Component**: See `SUBMIT_BUTTON_GUIDE.md` for UI standards

## Feature Documentation

### Timer Functionality
- Real-time timer display with HH:MM:SS format
- Start/stop controls with visual indicators
- Auto-calculation of duration and billable hours
- Pulsing animation for active timers

### Invoice Generation
- Select multiple completed timesheets
- Bulk creates invoice with line items
- Auto-populates customer, amounts, descriptions
- Marks timesheets as invoiced to prevent duplicate billing

### Reporting
- Hours tracked this week
- Hours tracked this month
- Uninvoiced amount (ready to bill)
- Active timer count

### Filtering
- By status (draft, running, stopped, completed, invoiced)
- By employee
- By project
- By customer
- By date range

## Success Criteria

Deployment is successful when:
- ✅ Database table created with all indexes
- ✅ Menu entry visible in navigation
- ✅ List page loads with summary cards
- ✅ Can create and edit timesheets
- ✅ Timer starts and stops correctly
- ✅ Invoice generation works from selected timesheets
- ✅ Permissions control access appropriately
- ✅ No PHP errors in logs
- ✅ Page load times under 2 seconds

## Production Deployment Date

**Deployed by:** [Your Name]
**Date:** [Deployment Date]
**Environment:** Production
**Database:** [Database Name]
**Server:** [Server Name/IP]

---

**Legacy Note:** The existing `/timeslips` module remains unchanged and fully functional. Both systems can coexist, with Timeslips serving as the legacy system and Timesheets as the modern replacement.
