# Permission Issues Troubleshooting Guide

## 🔍 Debug Steps

### Step 1: Access Debug Page

1. **Login** as the user who cannot access modules
2. **Navigate to**: `https://your-domain.com/debug_permissions`
3. **Review** the information displayed

The debug page shows:
- Session information
- Permissions loaded in session
- User data from database
- Access check tests
- List of accessible modules

### Step 2: Check Common Issues

#### Issue #1: Permissions Not in Session
**Symptom**: Debug page shows "NO PERMISSIONS IN SESSION"

**Causes**:
1. User logged in before permissions were assigned
2. json_decode() is not returning an array
3. whereIn() query is failing silently

**Solution**:
```bash
# 1. Logout completely
# 2. Clear browser cookies/cache
# 3. Login again
```

#### Issue #2: Menu ID Mismatch
**Symptom**: Permissions are in session but modules still inaccessible

**Causes**:
1. Permission IDs don't match menu IDs
2. Menu items were deleted/recreated with new IDs
3. Wrong business context

**Solution**:
```sql
-- Check if menu IDs exist
SELECT id, name, link FROM menu WHERE id IN (3,5,16,17,4,2);

-- Check user's permissions
SELECT id, name, permissions FROM users WHERE email = 'user@example.com';
```

#### Issue #3: Link/Table Name Mismatch
**Symptom**: Permission exists but URL segment doesn't match

**Example**:
- Menu link: `/deployments`
- Processed: `deployments`
- But table name is: `deployment` (singular)

**Solution**: Ensure menu links match controller names

#### Issue #4: Role-Based Permissions Not Working
**Symptom**: User has role but still can't access

**Causes**:
1. Role UUIDs don't match menu UUIDs
2. roles__permissions table is empty
3. isUUID() function not detecting role correctly

**Solution**:
```sql
-- Check role permissions
SELECT * FROM roles__permissions WHERE role_id = 'your-role-uuid';

-- Check menu UUIDs
SELECT id, uuid, name, link FROM menu WHERE id IN (1,2,3,4,5);
```

### Step 3: Database Verification

```sql
-- 1. Check user's permissions
SELECT id, name, email, permissions, role
FROM users
WHERE email = 'user@example.com';

-- 2. Check if menu IDs exist
SELECT id, name, link
FROM menu
WHERE id IN (/* paste permission IDs here */);

-- 3. Check role permissions (if using roles)
SELECT rp.*, m.name, m.link
FROM roles__permissions rp
LEFT JOIN menu m ON m.uuid = rp.permission_id
WHERE rp.role_id = 'role-uuid-here';

-- 4. Verify business context
SELECT uuid, name FROM businesses;
```

### Step 4: Session Check

```php
// In any controller or view, check session:
<?php
$session = \Config\Services::session();
$permissions = $session->get('permissions');
echo "<pre>";
print_r($permissions);
echo "</pre>";
?>
```

### Step 5: Test json_decode Fix

```php
// Test if permissions decode correctly
<?php
$db = \Config\Database::connect();
$user = $db->table('users')
    ->select('permissions')
    ->where('id', 2)
    ->get()
    ->getRow();

echo "Raw: " . $user->permissions . "\n";

$without_true = json_decode($user->permissions);
$with_true = json_decode($user->permissions, true);

echo "Without true: " . gettype($without_true) . " - " . (is_array($without_true) ? "Array" : "Object") . "\n";
echo "With true: " . gettype($with_true) . " - " . (is_array($with_true) ? "Array" : "Object") . "\n";
?>
```

## 🔧 Common Fixes

### Fix #1: Force Session Refresh

```php
// In Home controller after login, after setting permissions:
$this->session->set('permissions', $userMenus);
$this->session->markAsFlashdata('permissions'); // Clear on next request
$this->session->keepFlashdata('permissions'); // But keep it
```

### Fix #2: Clear All Sessions

```bash
# On server
cd ci4/writable/session
rm -f ci_session*
```

### Fix #3: Verify json_decode Fix is Applied

```bash
# Check if the fix is in place
grep -n "json_decode.*permissions.*true" ci4/app/Controllers/Home.php

# Should show:
# 135: $arr = json_decode($row->permissions, true);
# 206: $arr = json_decode($row->permissions, true);
```

### Fix #4: Add Debug Logging

```php
// In Home.php after line 136, add:
log_message('debug', 'User permissions decoded: ' . print_r($arr, true));
log_message('debug', 'User menus loaded: ' . print_r($userMenus, true));

// In CommonController.php after line 53, add:
log_message('debug', 'Session permissions: ' . print_r($permissions, true));
log_message('debug', 'Checking access to table: ' . $this->table);
```

### Fix #5: Bypass CommonController for Debug

If you need to test without permission checks:

```php
// Temporarily comment out lines 60-63 in CommonController.php
// if (!in_array($this->table, $user_permissions) && $currentPath !== "/dashboard") {
//     echo view("errors/html/error_403");
//     die;
// }
```

**⚠️ REMEMBER TO UNCOMMENT AFTER TESTING!**

## 📋 Checklist

Before reporting an issue, verify:

- [ ] User has logged out and back in after permission assignment
- [ ] Browser cookies/cache are cleared
- [ ] Permissions are visible in /debug_permissions page
- [ ] Menu IDs in permissions match actual menu table IDs
- [ ] json_decode fix is applied (with `true` parameter)
- [ ] CI4 logs don't show errors (writable/logs/)
- [ ] Database connection is working
- [ ] Session is being created (check writable/session/)

## 🐛 Known Issues

### Issue: "Class 'DebugPermissions' not found"
**Solution**: Clear route cache
```bash
php spark cache:clear
```

### Issue: Permissions work for admin but not other users
**Cause**: Admin (ID=1) bypasses permission checks
**Solution**: Test with user ID > 1

### Issue: Changes to permissions don't take effect
**Cause**: Session cache
**Solution**: Logout/login required

### Issue: Some modules work, others don't
**Cause**: Module might extend different base controller
**Solution**: Check if module extends CommonController

## 📞 Getting Help

If you're still having issues:

1. Access `/debug_permissions` and screenshot the output
2. Check `writable/logs/log-YYYY-MM-DD.log` for errors
3. Run the test script:
   ```bash
   docker exec workerra-ci-dev php /var/www/html/test_permission_load.php USER_ID
   ```
4. Provide:
   - User ID and email
   - Module you're trying to access
   - Screenshot of debug page
   - Any error messages from logs

## 🔄 Quick Reset

If all else fails, reset user permissions:

```sql
-- Get all menu IDs
SELECT GROUP_CONCAT(id) FROM menu;

-- Set user permissions to all menus
UPDATE users
SET permissions = '["1","2","3","4","5", ... all IDs ...]'
WHERE id = YOUR_USER_ID;
```

Then logout/login and test again.
