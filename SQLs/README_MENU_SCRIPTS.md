# Menu Management Scripts

This directory contains utility scripts for managing menu items and permissions in the Workstation system.

## Scripts Overview

### 1. Check Missing Routes (Dry Run)
**File:** `check_missing_routes.php`

Check which routes are missing from the menu table without making any changes.

```bash
php SQLs/check_missing_routes.php
```

**Output:**
- Lists routes NOT in menu table
- Lists routes already in menu table
- Groups by category and priority
- Shows admin permission status
- Provides recommendations

### 2. Add Missing Routes
**File:** `add_missing_routes_to_menu.php`

Automatically adds missing routes to the menu table and updates admin permissions.

```bash
php SQLs/add_missing_routes_to_menu.php
```

**What it does:**
- Checks for existing routes to avoid duplicates
- Adds 7 new routes (accounting and reports)
- Assigns sequential sort orders
- Generates UUIDs automatically
- Updates admin@admin.com permissions
- Shows detailed progress output

**Routes added:**
1. Chart of Accounts (`/accounts`)
2. Journal Entries (`/journal-entries`)
3. Accounting Periods (`/accounting-periods`)
4. Balance Sheet (`/balance-sheet`)
5. Trial Balance (`/trial-balance`)
6. Profit & Loss (`/profit-loss`)
7. API Documentation (`/swagger`)

### 3. Grant All Permissions to Admin (SQL)
**File:** `grant_all_menu_permissions_to_admin.sql`

SQL script to grant all menu permissions to admin user.

```bash
docker exec workerra-ci-db mariadb -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev < SQLs/grant_all_menu_permissions_to_admin.sql
```

### 4. Grant All Permissions to Admin (PHP)
**File:** `grant_all_menu_permissions_to_admin.php`

PHP version with verbose output and verification.

```bash
php SQLs/grant_all_menu_permissions_to_admin.php
```

### 5. Add Accounting Routes (SQL Only)
**File:** `add_accounting_routes_to_menu.sql`

Pure SQL script to add accounting routes (no PHP required).

```bash
docker exec workerra-ci-db mariadb -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev < SQLs/add_accounting_routes_to_menu.sql
```

## Typical Workflow

### First Time Setup

```bash
# Step 1: Check what's missing (dry run)
php SQLs/check_missing_routes.php

# Step 2: Add missing routes
php SQLs/add_missing_routes_to_menu.php

# Step 3: Verify (optional)
php SQLs/check_missing_routes.php
```

### Maintenance: Adding New Routes

When you create a new controller/module:

1. **Option A: Add manually**
   ```sql
   INSERT INTO menu (name, link, icon, sort_order, language_code, uuid)
   VALUES ('New Module', '/new-module', 'fa fa-icon', 100, 'en', UUID());
   ```

2. **Option B: Update the PHP script**
   - Edit `add_missing_routes_to_menu.php`
   - Add your route to the `$routesToAdd` array
   - Run the script

3. **Update admin permissions**
   ```bash
   php SQLs/grant_all_menu_permissions_to_admin.php
   ```

### Troubleshooting: Permission Issues

If admin can't access a menu item:

```bash
# Check current status
php SQLs/check_missing_routes.php

# Grant all permissions
php SQLs/grant_all_menu_permissions_to_admin.php
```

## Database Schema

### Menu Table
```sql
CREATE TABLE menu (
    id INT(25) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    link VARCHAR(255),
    icon VARCHAR(45) DEFAULT 'fa fa-globe',
    uuid_business_id VARCHAR(150),
    sort_order INT(11),
    language_code VARCHAR(10) DEFAULT 'en',
    menu_fts VARCHAR(255),  -- Full-text search
    uuid CHAR(36)
);
```

### Users Permissions
```sql
-- Stored in users.permissions field as JSON
-- Example: ["1", "2", "3", "4", "5", ...]
```

## Configuration

All scripts use environment variables or these defaults:

```php
$config = [
    'hostname' => 'workerra-ci-db',
    'username' => 'workerra-ci-dev',
    'password' => 'CHANGE_ME',
    'database' => 'myworkstation_dev',
];
```

## Script Features

### Safety Features
- ✅ Checks for duplicates before inserting
- ✅ Uses prepared statements (SQL injection safe)
- ✅ Validates database connection
- ✅ Provides detailed error messages
- ✅ Shows what will be added before running

### Automation Features
- ✅ Auto-generates UUIDs
- ✅ Auto-calculates sort orders
- ✅ Auto-updates admin permissions
- ✅ Detects existing routes
- ✅ Groups routes by category

### Output Features
- ✅ Color-coded status (✓, ✗, ⊘)
- ✅ Progress indicators
- ✅ Summary statistics
- ✅ Detailed verification
- ✅ Next steps recommendations

## Examples

### Example: Check Status
```bash
$ php SQLs/check_missing_routes.php

================================================================================
Check Missing Routes (Dry Run)
This script will NOT modify the database
================================================================================

✓ Connected to database: myworkstation_dev

Current Menu Status:
--------------------------------------------------------------------------------
  Total menu items: 45
  Existing routes: 45

================================================================================
ANALYSIS RESULTS
================================================================================

Routes NOT in Menu Table (7):
--------------------------------------------------------------------------------
Name                           Link                           Category        Priority
--------------------------------------------------------------------------------
Chart of Accounts              /accounts                      Accounting      HIGH
Journal Entries                /journal-entries               Accounting      HIGH
...
```

### Example: Add Routes
```bash
$ php SQLs/add_missing_routes_to_menu.php

================================================================================
Add Missing Routes to Menu Table
================================================================================

✓ Connected to database: myworkstation_dev

Step 1: Checking current menu items...
  Current menu items: 45

Step 2: Checking for existing routes...
  Found 45 existing menu links

Step 3: Calculating next sort order...
  Next sort order: 460

Step 4: Adding missing routes...
--------------------------------------------------------------------------------
  ✓ ADDED: Chart of Accounts (ID: 46, Link: /accounts)
  ✓ ADDED: Journal Entries (ID: 47, Link: /journal-entries)
  ...
--------------------------------------------------------------------------------

Step 5: Verifying results...
  New total menu items: 52
  Items added: 7
  Items skipped: 0

✓ SUCCESS
================================================================================
Added 7 new menu item(s) to the system.
Admin user permissions have been updated.
```

## Error Handling

### Common Errors

**Error: Can't connect to database**
```
Solution: Check database container is running
docker ps | grep workerra-ci-db
```

**Error: Access denied**
```
Solution: Check database credentials in .env file
cat .env | grep database.default
```

**Error: Duplicate entry**
```
Solution: Route already exists. Script will skip automatically.
```

**Error: Admin user not found**
```
Solution: Check if admin@admin.com exists in users table
```

## Testing

### Before Running in Production

1. Run dry-run check:
   ```bash
   php SQLs/check_missing_routes.php
   ```

2. Backup database:
   ```bash
   docker exec workerra-ci-db mysqldump -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev > backup.sql
   ```

3. Run on test environment first

4. Verify results:
   ```bash
   # Check menu count
   docker exec workerra-ci-db mariadb -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev -e "SELECT COUNT(*) FROM menu;"

   # Check admin permissions
   docker exec workerra-ci-db mariadb -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev -e "SELECT JSON_LENGTH(permissions) FROM users WHERE email = 'admin@admin.com';"
   ```

## Related Documentation

- `ADMIN_MENU_PERMISSIONS_UPDATE.md` - Admin permissions update log
- `MISSING_MENU_ROUTES_ANALYSIS.md` - Comprehensive route analysis
- `MENU_ROUTES_SUMMARY.md` - Session summary and overview

## Support

If you encounter issues:

1. Check script output for error messages
2. Verify database connection
3. Check database credentials
4. Review `/debug-permissions` page
5. Check user permissions in database

## License

Internal use only - Workstation System
