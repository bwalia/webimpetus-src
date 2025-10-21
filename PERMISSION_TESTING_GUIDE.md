# Permission Testing Guide

**Date:** 2025-10-20  
**Purpose:** Test granular permission system (read, create, update, delete)  
**Affected Files:** 11 controllers + CommonController

---

## 🔧 Changes Implemented

### Files Modified

1. ✅ `ci4/app/Controllers/Core/CommonController.php`
   - Fixed `update()` method (lines 266-295)
   - Fixed `status()` method (lines 360-375)

2. ✅ `ci4/app/Controllers/Customers.php` - Fixed `update()` method
3. ✅ `ci4/app/Controllers/Users.php` - Fixed `update()` method
4. ✅ `ci4/app/Controllers/Documents.php` - Fixed `update()` method
5. ✅ `ci4/app/Controllers/Businesses.php` - Fixed `update()` method
6. ✅ `ci4/app/Controllers/Receipts.php` - Already had permission checks ✓
7. ✅ `ci4/app/Controllers/Accounts.php` - Fixed `update()` method
8. ✅ `ci4/app/Controllers/Contacts.php` - Fixed `update()` method
9. ✅ `ci4/app/Controllers/Companies.php` - Fixed `update()` method
10. ✅ `ci4/app/Controllers/HospitalStaff.php` - Fixed `update()` method

### What Was Fixed

**Before:** Users could POST to `/module/update` and modify data even with read-only permissions.

**After:** All `update()` methods now check:
- **Update permission** when modifying existing records
- **Create permission** when creating new records
- Returns error message and redirects if permission denied

---

## 🧪 Testing Procedure

### Test Environment Setup

1. **Create Test User**
   ```sql
   INSERT INTO users (uuid, name, email, password, role, uuid_business_id, status)
   VALUES (UUID(), 'Test User', 'test@test.com', MD5('test123'), 
           (SELECT uuid FROM roles WHERE role_name = 'User' LIMIT 1),
           (SELECT uuid FROM businesses LIMIT 1), 1);
   ```

2. **Get Test User ID**
   ```sql
   SELECT id, email FROM users WHERE email = 'test@test.com';
   -- Note the ID (e.g., 25)
   ```

---

## Test Scenario 1: Read-Only Access

### Setup
Grant ONLY read permission to Customers module:

```sql
-- Clear existing permissions
DELETE FROM user_permissions WHERE user_id = 25;

-- Grant READ ONLY for Customers (menu_id = 16)
INSERT INTO user_permissions (uuid, user_id, menu_id, can_read, can_create, can_update, can_delete, created_at, updated_at)
VALUES (UUID(), 25, 16, 1, 0, 0, 0, NOW(), NOW());
```

### Test Cases

#### Test 1.1: View List Page ✓ Should Work
```bash
# Login as test@test.com
# Navigate to: /customers

Expected: ✓ Can see customer list
Expected: ✓ No "Add New" button visible
Expected: ✓ No "Edit" buttons in action menu
Expected: ✓ No "Delete" buttons in action menu
Expected: ✓ "View Only" badge shown in action column
```

#### Test 1.2: Try to Access Edit Form ✗ Should Block
```bash
# Navigate to: /customers/edit/some-uuid

Expected: ✗ Shows 403 Forbidden error
```

#### Test 1.3: Try to POST Update ✗ Should Block (CRITICAL TEST)
```bash
# Open browser console and run:
fetch('/customers/update', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'uuid=some-existing-uuid&company_name=HACKED&email=hacker@evil.com'
});

Expected: ✗ Redirects back to /customers
Expected: ✗ Flash message: "You do not have permission to update records"
Expected: ✗ Data NOT modified in database
```

#### Test 1.4: Try to POST Create ✗ Should Block
```bash
# Open browser console and run:
fetch('/customers/update', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'company_name=NEW HACK&email=newh ack@evil.com'
});

Expected: ✗ Redirects back to /customers
Expected: ✗ Flash message: "You do not have permission to create records"
Expected: ✗ No new record created in database
```

#### Test 1.5: Try to Change Status ✗ Should Block
```bash
# Open browser console and run:
fetch('/customers/status', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'id=123&status=0'
});

Expected: ✗ Returns JSON error: {"status":"error","message":"You do not have permission"}
Expected: ✗ Status NOT changed in database
```

---

## Test Scenario 2: Read + Create (No Update)

### Setup
```sql
-- Grant READ + CREATE for Customers
UPDATE user_permissions 
SET can_create = 1 
WHERE user_id = 25 AND menu_id = 16;
```

### Test Cases

#### Test 2.1: Create New Record ✓ Should Work
```bash
# Navigate to: /customers/edit
Expected: ✓ Shows empty form
Expected: ✓ Can fill in details
Expected: ✓ Can submit form successfully
Expected: ✓ New record created in database
```

#### Test 2.2: Try to Edit Existing ✗ Should Block
```bash
# Navigate to: /customers/edit/some-existing-uuid
Expected: ✗ Shows 403 Forbidden error
```

#### Test 2.3: Try to POST Update ✗ Should Block
```bash
# Try to update existing record via POST
Expected: ✗ Blocked with permission error
Expected: ✗ Data NOT modified
```

---

## Test Scenario 3: Read + Update (No Create)

### Setup
```sql
-- Grant READ + UPDATE for Customers
UPDATE user_permissions 
SET can_create = 0, can_update = 1 
WHERE user_id = 25 AND menu_id = 16;
```

### Test Cases

#### Test 3.1: Edit Existing Record ✓ Should Work
```bash
# Navigate to: /customers/edit/some-existing-uuid
Expected: ✓ Shows filled form
Expected: ✓ Can modify details
Expected: ✓ Can submit successfully
Expected: ✓ Changes saved to database
```

#### Test 3.2: Try to Create New ✗ Should Block
```bash
# Navigate to: /customers/edit
Expected: ✗ Shows 403 Forbidden error OR empty form blocked on submit
```

#### Test 3.3: Try to POST Create ✗ Should Block
```bash
# Try to create via POST without UUID
Expected: ✗ Blocked with permission error
Expected: ✗ No new record created
```

---

## Test Scenario 4: Read + Delete (No Update)

### Setup
```sql
-- Grant READ + DELETE for Customers
UPDATE user_permissions 
SET can_update = 0, can_delete = 1 
WHERE user_id = 25 AND menu_id = 16;
```

### Test Cases

#### Test 4.1: Delete Record ✓ Should Work
```bash
# Navigate to: /customers
Expected: ✓ Delete button visible in actions
Expected: ✓ Can click delete
Expected: ✓ Record deleted successfully
```

#### Test 4.2: Try to Update ✗ Should Block
```bash
Expected: ✗ No edit button visible
Expected: ✗ Direct navigation to /customers/edit/uuid blocked
Expected: ✗ POST to /customers/update blocked
```

---

## Test Scenario 5: Full CRUD Access

### Setup
```sql
-- Grant ALL permissions for Customers
UPDATE user_permissions 
SET can_create = 1, can_update = 1, can_delete = 1 
WHERE user_id = 25 AND menu_id = 16;
```

### Test Cases

#### Test 5.1: All Operations ✓ Should Work
```bash
Expected: ✓ Can view list
Expected: ✓ Can create new records
Expected: ✓ Can edit existing records
Expected: ✓ Can delete records
Expected: ✓ All buttons visible
Expected: ✓ All POST operations succeed
```

---

## Test Scenario 6: Multiple Modules

### Setup
```sql
-- Grant different permissions for different modules
-- Customers: Read + Update
INSERT INTO user_permissions (uuid, user_id, menu_id, can_read, can_create, can_update, can_delete, created_at, updated_at)
VALUES (UUID(), 25, 16, 1, 0, 1, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE can_read=1, can_create=0, can_update=1, can_delete=0;

-- Contacts: Read + Create
INSERT INTO user_permissions (uuid, user_id, menu_id, can_read, can_create, can_update, can_delete, created_at, updated_at)
VALUES (UUID(), 25, 17, 1, 1, 0, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE can_read=1, can_create=1, can_update=0, can_delete=0;

-- Projects: Read Only
INSERT INTO user_permissions (uuid, user_id, menu_id, can_read, can_create, can_update, can_delete, created_at, updated_at)
VALUES (UUID(), 25, 21, 1, 0, 0, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE can_read=1, can_create=0, can_update=0, can_delete=0;
```

### Test Cases

Test each module has the correct permission level working.

---

## Automated Testing Script

### Using cURL

```bash
#!/bin/bash

# Login and get session cookie
LOGIN_RESPONSE=$(curl -c cookies.txt -d "email=test@test.com&password=test123" http://yourcrm.local/home/validate)

# Test 1: Try to update customer (should fail with read-only)
echo "Test 1: Attempting unauthorized update..."
RESPONSE=$(curl -b cookies.txt -X POST http://yourcrm.local/customers/update \
  -d "uuid=test-uuid&company_name=HACKED")
echo "$RESPONSE" | grep -q "permission" && echo "✓ PASSED: Update blocked" || echo "✗ FAILED: Update allowed!"

# Test 2: Try to change status (should fail)
echo "Test 2: Attempting unauthorized status change..."
RESPONSE=$(curl -b cookies.txt -X POST http://yourcrm.local/customers/status \
  -d "id=123&status=0")
echo "$RESPONSE" | grep -q "permission" && echo "✓ PASSED: Status change blocked" || echo "✗ FAILED: Status change allowed!"

# Cleanup
rm cookies.txt
```

---

## Database Verification Queries

### Check User Permissions
```sql
SELECT 
    u.id,
    u.email,
    m.id as menu_id,
    m.name as module_name,
    up.can_read,
    up.can_create,
    up.can_update,
    up.can_delete
FROM users u
JOIN user_permissions up ON up.user_id = u.id
JOIN menu m ON m.id = up.menu_id
WHERE u.email = 'test@test.com'
ORDER BY m.id;
```

### Verify No Unauthorized Changes
```sql
-- Before test
SELECT company_name, email FROM customers WHERE id = 123;

-- Run unauthorized update attempt

-- After test (should be unchanged)
SELECT company_name, email FROM customers WHERE id = 123;
```

---

## Common Test Menu IDs

| ID | Module | Link |
|----|--------|------|
| 16 | Customers | /customers |
| 17 | Contacts | /contacts |
| 19 | Work Orders | /work_orders |
| 21 | Projects | /projects |
| 23 | Sales Invoices | /sales_invoices |
| 24 | Tasks | /tasks |
| 27 | Purchase Orders | /purchase_orders |
| 29 | Purchase Invoices | /purchase_invoices |

---

## Expected Results Summary

| Permission | View List | Access Edit Form | POST Create | POST Update | Delete |
|------------|-----------|------------------|-------------|-------------|--------|
| **Read Only** | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Read + Create** | ✅ | ✅ (new only) | ✅ | ❌ | ❌ |
| **Read + Update** | ✅ | ✅ (existing only) | ❌ | ✅ | ❌ |
| **Read + Delete** | ✅ | ❌ | ❌ | ❌ | ✅ |
| **Full CRUD** | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## Regression Testing

After making permission fixes, also test:

1. **Super Admin (User ID = 1)** - Should bypass ALL checks
2. **Normal Admin Users** - Should work as before
3. **Role-Based Permissions** - Should still work
4. **Legacy Permission Field** - Should still be respected
5. **Session Persistence** - Permissions should load correctly at login

---

## Troubleshooting

### User Still Can Modify Data

**Check:**
1. Is user ID = 1? (Super admin bypasses checks)
2. Did user logout and login after permission changes?
3. Are permissions in `user_permissions` table correct?
4. Check `permission_map` in session:
   ```php
   echo '<pre>';
   print_r($_SESSION['permission_map']);
   echo '</pre>';
   ```

### Permission Check Not Working

**Check:**
1. Is `checkPermission()` method being called?
2. Is menu ID correctly mapped in `menu` table?
3. Check logs: `ci4/writable/logs/`

### 403 Errors for Valid Permissions

**Check:**
1. Menu `link` field matches route (e.g., `/customers`)
2. User has permission for that specific menu_id
3. Session has loaded `permission_map` correctly

---

## Success Criteria

✅ All 6 test scenarios pass  
✅ Read-only users CANNOT modify data via any method  
✅ Buttons hide/show correctly based on permissions  
✅ Direct POST requests are blocked  
✅ Status changes are blocked without permission  
✅ Super admin still has full access  
✅ No legitimate users are blocked  

---

**Test Completion:** After all tests pass, mark this branch ready for code review and merge to main.

**Next Steps:** 
1. Run through all test scenarios
2. Document any issues found
3. Fix any regressions
4. Update this guide with any additional findings

