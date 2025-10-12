# Accounting Periods Permission Issue - Debug Guide

## Problem
User `admin@admin.com` cannot access `/accounting_periods/edit/uuid` even though menu permissions are granted.

## Root Cause Analysis

### Permission Check Logic (CommonController.php lines 53-67)
```php
$permissions = $this->session->get('permissions');
$user_permissions = array_map(function ($perm) {
    return strtolower(str_replace("/", "", $perm['link']));
}, $permissions);
if (!in_array($this->table, $user_permissions) && $currentPath !== "/dashboard") {
    echo view("errors/html/error_403");
    die;
}
```

### How It Works:
1. Gets `$this->table` from URI segment 1: `accounting_periods`
2. Strips slashes from permission links in session
3. Checks if `accounting_periods` is in the user_permissions array

### Potential Issues:

#### Issue 1: Menu Link Format Mismatch
The menu table might have:
- `/accounting-periods` (with hyphen)
- But URL is `/accounting_periods` (with underscore)

After stripping slashes:
- Permission: `accounting-periods`
- URL table: `accounting_periods`
- **MISMATCH!**

#### Issue 2: Missing Permission in Session
The permissions might not be loaded correctly in the session.

## Solution Steps

### Step 1: Check Menu Table
```sql
SELECT id, name, link, parent_id
FROM menu
WHERE link LIKE '%accounting%'
   OR name LIKE '%accounting%';
```

**Expected Result:**
```
id  | name                | link                  | parent_id
----+---------------------+-----------------------+----------
123 | Accounting Periods  | /accounting_periods   | 45
```

### Step 2: Check User Permissions
```sql
SELECT
    u.id,
    u.email,
    r.name as role_name,
    m.name as menu_name,
    m.link as menu_link
FROM users u
LEFT JOIN user_role ur ON u.id = ur.user_id
LEFT JOIN roles r ON ur.role_id = r.id
LEFT JOIN role_permission rp ON r.id = rp.role_id
LEFT JOIN menu m ON rp.menu_id = m.id
WHERE u.email = 'admin@admin.com'
  AND m.link LIKE '%accounting%';
```

**Expected Result:**
Should show accounting_periods in the menu_link column.

### Step 3: Check Session Permissions
Add this to AccountingPeriods controller temporarily:
```php
public function edit($uuid = null)
{
    // Debug permissions
    $permissions = session('permissions');
    echo "<pre>";
    print_r($permissions);
    echo "</pre>";
    die();

    // ... rest of code
}
```

Look for accounting_periods in the output.

### Step 4: Fix Menu Link Format

#### Option A: Update Menu Table (if using hyphens)
```sql
UPDATE menu
SET link = '/accounting_periods'
WHERE link = '/accounting-periods';
```

#### Option B: Update Routes (if menu uses underscores but routes use hyphens)
Check `ci4/app/Config/Routes.php`:
```php
// Should match menu link format
$routes->group('accounting_periods', function($routes) {
    // ... routes
});
```

### Step 5: Grant Permission Explicitly

```sql
-- Find the menu ID
SELECT id FROM menu WHERE link = '/accounting_periods';
-- Let's say it returns ID = 123

-- Find the admin role ID
SELECT id FROM roles WHERE name = 'Admin' OR name = 'Super Admin';
-- Let's say it returns ID = 1

-- Check if permission exists
SELECT * FROM role_permission
WHERE role_id = 1 AND menu_id = 123;

-- If not exists, insert it
INSERT INTO role_permission (role_id, menu_id, created_at)
VALUES (1, 123, NOW());
```

### Step 6: Clear Session
After fixing, the user needs to:
1. Logout
2. Clear browser cookies
3. Login again

This will refresh the permissions in the session.

## Quick Fix Script

Create this SQL script and run it:

```sql
-- accounting_periods_permission_fix.sql

-- 1. Check current menu entry
SELECT 'Current menu entry:' as info;
SELECT id, name, link FROM menu WHERE name LIKE '%Accounting Period%';

-- 2. Ensure link format is correct (with underscore)
UPDATE menu SET link = '/accounting_periods' WHERE name LIKE '%Accounting Period%';

-- 3. Get menu ID
SET @menu_id = (SELECT id FROM menu WHERE link = '/accounting_periods' LIMIT 1);

-- 4. Get admin role ID
SET @admin_role_id = (SELECT id FROM roles WHERE name = 'Admin' OR name = 'Super Admin' LIMIT 1);

-- 5. Check if permission exists
SELECT 'Checking existing permission:' as info;
SELECT * FROM role_permission WHERE role_id = @admin_role_id AND menu_id = @menu_id;

-- 6. Insert permission if not exists
INSERT IGNORE INTO role_permission (role_id, menu_id, created_at)
VALUES (@admin_role_id, @menu_id, NOW());

-- 7. Verify permission was added
SELECT 'Verification - permissions for admin role:' as info;
SELECT
    m.id,
    m.name,
    m.link,
    r.name as role_name
FROM role_permission rp
JOIN menu m ON rp.menu_id = m.id
JOIN roles r ON rp.role_id = r.id
WHERE r.id = @admin_role_id AND m.link = '/accounting_periods';
```

## Alternative: Bypass Permission Check for Accounting Periods

If you want accounting periods accessible to all logged-in users, modify AccountingPeriods controller:

```php
<?php
namespace App\Controllers;

use App\Controllers\BaseController; // NOT CommonController
use App\Models\AccountingPeriods_model;

class AccountingPeriods extends BaseController
{
    protected $periods_model;

    public function __construct()
    {
        parent::__construct();

        // Add basic auth check
        if(!session()->get('uuid')){
            return redirect()->to('/');
        }

        $this->periods_model = new AccountingPeriods_model();
    }

    // ... rest of methods
}
```

This extends BaseController instead of CommonController, bypassing the strict permission check.

## Testing After Fix

1. **Logout** from admin@admin.com
2. **Clear browser cookies**
3. **Login again**
4. Try accessing: `https://dev001.workstation.co.uk/accounting_periods`
5. Try accessing: `https://dev001.workstation.co.uk/accounting_periods/edit/d9ddd936-f6d1-48e5-824b-06d73762c43e`

Both should work now.

## Common Mistakes

### Mistake 1: URL vs Menu Link Format
- URL: `/accounting_periods` (underscore)
- Menu: `/accounting-periods` (hyphen)
- **Fix:** Make them match!

### Mistake 2: Forgot to Logout
Session caches permissions. Must logout/login after changing permissions.

### Mistake 3: Wrong Role
Permission granted to "User" role but admin has "Super Admin" role.
**Fix:** Grant to correct role.

### Mistake 4: Parent Menu Not Accessible
If accounting_periods is under a parent menu, parent must also be accessible.

## Debug Commands

```bash
# Check current route
curl -I https://dev001.workstation.co.uk/accounting_periods

# Check with authentication
curl -b cookies.txt https://dev001.workstation.co.uk/accounting_periods
```

## Prevention

To avoid this issue in future:

1. **Consistent Naming Convention**: Always use underscores in URLs and menu links
2. **Permission Seeder**: Create seeder to auto-grant all menu permissions to Admin role
3. **Better Error Messages**: Show which permission is missing
4. **Dev Mode**: Bypass permission checks in development

```php
// In CommonController
if (ENVIRONMENT === 'development') {
    // Skip permission check
    return;
}
```

## Contact Support

If issue persists after following all steps:
1. Share the output of Step 1-3 queries
2. Share session permissions dump
3. Share exact error or behavior
