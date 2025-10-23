# Accounting Periods Permission Issue - Solution

## Problem
User `admin@admin.com` cannot access:
```
https://dev001.workstation.co.uk/accounting_periods/edit/d9ddd936-f6d1-48e5-824b-06d73762c43e
```

Even though menu permissions appear to be granted.

## Root Cause

The CommonController permission check (lines 53-67) compares:
- **URL table name**: `accounting_periods` (from URI segment 1)
- **Menu link**: Could be `/accounting-periods` (hyphen) or `/accounting_periods` (underscore)

After processing (removing slash, lowercasing):
- If menu has `/accounting-periods` → becomes `accounting-periods` ❌ **MISMATCH**
- If menu has `/accounting_periods` → becomes `accounting_periods` ✓ **MATCH**

## Quick Diagnostic Tool

### Step 1: Run Debug Tool
Visit this URL while logged in as admin@admin.com:
```
https://dev001.workstation.co.uk/debug-permissions
```

Or:
```
https://dev001.workstation.co.uk/debug_permissions
```

This will show you:
1. Current user info
2. Menu entries for accounting
3. User's roles
4. Role permissions
5. Session permissions
6. URL format analysis
7. **Specific issues and fixes**
8. **Ready-to-run SQL script**

### Step 2: Review Output
The debug tool will tell you exactly what's wrong:
- ❌ Menu entry missing
- ❌ Permission not granted
- ❌ URL format mismatch
- ❌ Session not refreshed

## Common Solutions

### Solution 1: URL Format Mismatch (Most Common)

**If menu link is `/accounting-periods` (hyphen) but URL is `/accounting_periods` (underscore):**

```sql
-- Fix the menu link to use underscore
UPDATE menu
SET link = '/accounting_periods'
WHERE link = '/accounting-periods';
```

**Then user must:**
1. Logout
2. Login again

---

### Solution 2: Missing Menu Entry

**If menu entry doesn't exist at all:**

```sql
-- Create the menu entry
INSERT INTO menu (name, link, icon, parent_id, sort_order, created_at)
VALUES ('Accounting Periods', '/accounting_periods', 'fa-calendar-alt', NULL, 100, NOW());
```

---

### Solution 3: Permission Not Granted

**If menu exists but permission not granted to admin role:**

```sql
-- Get the menu ID
SET @menu_id = (SELECT id FROM menu WHERE link = '/accounting_periods' LIMIT 1);

-- Get the admin role ID
SET @admin_role_id = (SELECT id FROM roles WHERE name IN ('Admin', 'Super Admin') ORDER BY id LIMIT 1);

-- Grant permission
INSERT IGNORE INTO role_permission (role_id, menu_id, created_at)
VALUES (@admin_role_id, @menu_id, NOW());
```

**Then user must:**
1. Logout
2. Login again

---

### Solution 4: All-in-One Fix Script

**Run this complete SQL script to fix everything:**

```sql
-- ==================================================
-- ACCOUNTING PERIODS PERMISSION FIX - ALL IN ONE
-- ==================================================

-- Step 1: Create or update menu entry (with correct underscore format)
INSERT INTO menu (name, link, icon, parent_id, sort_order, created_at)
VALUES ('Accounting Periods', '/accounting_periods', 'fa-calendar-alt', NULL, 100, NOW())
ON DUPLICATE KEY UPDATE link = '/accounting_periods';

-- Step 2: Get IDs
SET @menu_id = (SELECT id FROM menu WHERE link = '/accounting_periods' LIMIT 1);
SET @admin_role_id = (SELECT id FROM roles WHERE name IN ('Admin', 'Super Admin') ORDER BY id LIMIT 1);

-- Step 3: Grant permission if not exists
INSERT IGNORE INTO role_permission (role_id, menu_id, created_at)
VALUES (@admin_role_id, @menu_id, NOW());

-- Step 4: Verify the fix
SELECT
    'SUCCESS - Permission granted!' as status,
    r.name as role_name,
    m.name as menu_name,
    m.link as menu_link,
    rp.created_at as granted_at
FROM role_permission rp
JOIN roles r ON rp.role_id = r.id
JOIN menu m ON rp.menu_id = m.id
WHERE m.link = '/accounting_periods'
  AND r.id = @admin_role_id;
```

**After running SQL:**
1. **User MUST logout** from https://dev001.workstation.co.uk
2. **Clear browser cookies** (optional but recommended)
3. **Login again** as admin@admin.com
4. **Test access** to accounting_periods

---

## Verification Steps

### 1. Check Menu Entry
```sql
SELECT id, name, link, icon
FROM menu
WHERE link LIKE '%accounting%';
```

**Expected result:**
```
id  | name                | link                 | icon
----+---------------------+----------------------+-------------------
123 | Accounting Periods  | /accounting_periods  | fa-calendar-alt
```

**Key check:** Link should have **underscore** not hyphen!

### 2. Check Permission Exists
```sql
SELECT
    r.name as role_name,
    m.name as menu_name,
    m.link as menu_link
FROM role_permission rp
JOIN roles r ON rp.role_id = r.id
JOIN menu m ON rp.menu_id = m.id
WHERE m.link = '/accounting_periods'
  AND r.name IN ('Admin', 'Super Admin');
```

**Expected result:**
```
role_name    | menu_name           | menu_link
-------------+---------------------+-------------------
Admin        | Accounting Periods  | /accounting_periods
```

### 3. Test Access (After Logout/Login)
```bash
# Test list page
curl -I https://dev001.workstation.co.uk/accounting_periods

# Should return 200 OK (after login)
```

---

## Why User Must Logout/Login

The permissions are cached in the session when user logs in. Changes to the database (menu, role_permission tables) **do not automatically update the session**.

The session stores:
```php
session('permissions') = [
    ['id' => 1, 'name' => 'Dashboard', 'link' => '/dashboard'],
    ['id' => 2, 'name' => 'Users', 'link' => '/users'],
    // ... etc
];
```

This is loaded during login and stays cached. Only logging out and in again refreshes it.

---

## Alternative: Bypass Permission Check

If you want to make accounting_periods accessible without strict permission checks, modify the controller:

**File:** `/home/bwalia/workerra-ci/ci4/app/Controllers/AccountingPeriods.php`

**Change line 8:**
```php
// FROM:
class AccountingPeriods extends CommonController

// TO:
class AccountingPeriods extends BaseController
```

**Add constructor:**
```php
public function __construct()
{
    parent::__construct();

    // Basic auth check only
    if(!session()->get('uuid')){
        return redirect()->to('/');
    }

    $this->businessUuid = session('uuid_business');
    $this->periods_model = new AccountingPeriods_model();
}
```

This removes the strict menu-based permission check and only requires user to be logged in.

---

## Prevention for Future

### 1. Consistent Naming Convention
**Always use underscores in URLs and menu links:**
- ✓ `/accounting_periods`
- ✓ `/hospital_staff`
- ✓ `/patient_logs`
- ❌ `/accounting-periods`
- ❌ `/hospital-staff`

### 2. Permission Seeder
Create a seeder to auto-grant all menu items to Admin role:

```php
// database/Seeds/AdminPermissionsSeeder.php
public function run()
{
    $db = \Config\Database::connect();

    // Get all menus
    $menus = $db->table('menu')->select('id')->get()->getResultArray();

    // Get admin role
    $adminRole = $db->table('roles')
        ->where('name', 'Admin')
        ->get()
        ->getRowArray();

    // Grant all permissions
    foreach ($menus as $menu) {
        $db->table('role_permission')->insert([
            'role_id' => $adminRole['id'],
            'menu_id' => $menu['id'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
```

### 3. Better Error Messages
Modify CommonController to show which permission is missing:

```php
if (!in_array($this->table, $user_permissions) && $currentPath !== "/dashboard") {
    // Show helpful error
    $this->data['missing_permission'] = $this->table;
    $this->data['user_permissions'] = $user_permissions;
    echo view("errors/html/error_403", $this->data);
    die;
}
```

### 4. Development Mode Bypass
In CommonController, add:

```php
// Skip permission check in development
if (ENVIRONMENT === 'development') {
    return; // Skip permission check
}

// ... rest of permission check code
```

---

## Testing Checklist

After applying the fix:

- [ ] Run the All-in-One SQL script
- [ ] Verify menu entry exists: `SELECT * FROM menu WHERE link = '/accounting_periods'`
- [ ] Verify permission granted: Check Step 2 query above
- [ ] User logout from application
- [ ] User login again
- [ ] Test: Visit `https://dev001.workstation.co.uk/accounting_periods`
- [ ] Test: Visit `https://dev001.workstation.co.uk/accounting_periods/edit/d9ddd936-f6d1-48e5-824b-06d73762c43e`
- [ ] Both pages should load without 403 error

---

## Still Not Working?

### Run Full Diagnostic:
1. Visit: `https://dev001.workstation.co.uk/debug-permissions`
2. Take screenshot of the output
3. Look for sections marked with ❌
4. Follow the specific fixes shown

### Check Browser Console:
1. Press F12
2. Go to Console tab
3. Look for any JavaScript errors
4. Look for 403 or 401 responses in Network tab

### Check Server Logs:
```bash
tail -f /home/bwalia/workerra-ci/ci4/writable/logs/log-*.php
```

### Contact Support:
Provide:
1. Screenshot of debug-permissions page
2. Output of verification SQL queries
3. Browser console errors
4. Server log errors

---

## Summary

**Most Likely Issue:** Menu link format mismatch (hyphen vs underscore)

**Quick Fix:**
1. Run the All-in-One SQL script above
2. User logout
3. User login
4. Test access

**Diagnostic Tool:** https://dev001.workstation.co.uk/debug-permissions

**Time to Fix:** 2 minutes (1 minute SQL + 1 minute logout/login)
