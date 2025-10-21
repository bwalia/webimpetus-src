# Admin User (ID 19) - Full Menu Permissions Setup

## Overview

This document describes SQL scripts created to grant full access to all CRM menus for the superuser `admin@admin.com` (User ID: 19).

## Created Scripts

### 1. SQL Script
**Location:** `SQLs/grant_all_permissions_to_admin_id19.sql`

Pure SQL script that grants all permissions without requiring PHP.

**Features:**
- Updates legacy `users.permissions` JSON field with all menu IDs
- Inserts/updates `user_permissions` table with full CRUD permissions
- Provides detailed verification output
- Shows summary of all granted permissions

**Usage:**
```bash
# Via Docker (recommended)
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < SQLs/grant_all_permissions_to_admin_id19.sql

# Or directly if you have MySQL client
mysql -h 127.0.0.1 -P 3309 -u wsl_dev -p'CHANGE_ME' myworkstation_dev < SQLs/grant_all_permissions_to_admin_id19.sql
```

### 2. PHP Script
**Location:** `SQLs/grant_all_permissions_to_admin_id19.php`

PHP script with enhanced output and error handling.

**Features:**
- More detailed progress reporting
- Better error handling and validation
- Formatted output with tables
- Step-by-step verification

**Usage:**
```bash
# From host (if PHP installed)
php SQLs/grant_all_permissions_to_admin_id19.php

# Or via Docker
docker exec webimpetus-dev php /var/www/html/SQLs/grant_all_permissions_to_admin_id19.php
```

## What These Scripts Do

### 1. Legacy Permissions (Backward Compatibility)
Updates the `users.permissions` field with a JSON array of all menu IDs:
```json
["1", "2", "3", "4", "5", ... "n"]
```

**Table:** `users`  
**Field:** `permissions` (TEXT)  
**Format:** JSON array of menu IDs as strings

### 2. Granular Permissions (Modern Approach)
Inserts records into `user_permissions` table with full CRUD access:

**Table:** `user_permissions`

| Field | Value | Description |
|-------|-------|-------------|
| user_id | 19 | The admin user ID |
| menu_id | 1, 2, 3... | Each menu item ID |
| can_read | 1 | Full read access |
| can_create | 1 | Full create access |
| can_update | 1 | Full update access |
| can_delete | 1 | Full delete access |

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY,
    uuid VARCHAR(36),
    email VARCHAR(255),
    name VARCHAR(255),
    role VARCHAR(36),
    permissions TEXT,  -- JSON array of menu IDs
    ...
);
```

### Menu Table
```sql
CREATE TABLE menu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    link VARCHAR(255),
    icon VARCHAR(45) DEFAULT 'fa fa-globe',
    sort_order INT(11),
    language_code VARCHAR(10) DEFAULT 'en',
    uuid VARCHAR(36)
);
```

### User Permissions Table
```sql
CREATE TABLE user_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36),
    user_id INT,
    menu_id INT,
    can_read TINYINT(1) DEFAULT 1,
    can_create TINYINT(1) DEFAULT 0,
    can_update TINYINT(1) DEFAULT 0,
    can_delete TINYINT(1) DEFAULT 0,
    created_at DATETIME,
    updated_at DATETIME,
    UNIQUE KEY (user_id, menu_id)
);
```

## Verification

After running either script, you can verify the permissions using these SQL queries:

### Check Legacy Permissions
```sql
SELECT 
    id,
    email,
    name,
    JSON_LENGTH(permissions) as permission_count,
    permissions
FROM users
WHERE id = 19 AND email = 'admin@admin.com';
```

Expected: `permission_count` should equal the total number of menu items.

### Check Granular Permissions
```sql
SELECT 
    user_id,
    COUNT(*) as total_permissions,
    SUM(can_read) as read_count,
    SUM(can_create) as create_count,
    SUM(can_update) as update_count,
    SUM(can_delete) as delete_count
FROM user_permissions
WHERE user_id = 19
GROUP BY user_id;
```

Expected: All counts should equal the total number of menu items.

### View Detailed Permissions
```sql
SELECT 
    up.menu_id,
    m.name as menu_name,
    m.link as menu_link,
    up.can_read,
    up.can_create,
    up.can_update,
    up.can_delete
FROM user_permissions up
JOIN menu m ON m.id = up.menu_id
WHERE up.user_id = 19
ORDER BY up.menu_id;
```

## User Information

| Field | Value |
|-------|-------|
| User ID | 19 |
| Email | admin@admin.com |
| Role | Superuser/Administrator |
| Access Level | Full CRUD on all modules |

## Permission Priority System

The CRM uses a layered permission system:

1. **Super Admin Override** (User ID = 1)
   - Gets all menus regardless of permissions

2. **User-Specific Granular Permissions** (Highest Priority)
   - From `user_permissions` table
   - Overrides role-based permissions
   - **This is what we're setting up for User ID 19**

3. **Role-Based Granular Permissions**
   - From `roles__permissions` table
   - Used if no user-specific permissions exist

4. **Legacy Permissions** (Fallback)
   - From `users.permissions` JSON field
   - Used for backward compatibility

## Current Menu Items

The scripts dynamically fetch all available menu items. As of this documentation, the system includes modules such as:

- Dashboard
- Tasks
- Projects
- Work Orders
- Sales Invoices
- Purchase Orders
- Customers
- Contacts
- Timeslips
- Calendar
- Reports
- VAT Returns
- Accounting
- Interviews
- Knowledge Base
- Deployments
- And more...

## When to Re-run These Scripts

Run these scripts again when:

1. **New Menu Items Added**
   - After adding new modules to the CRM
   - After system upgrades that introduce new features

2. **Permission Reset Needed**
   - If admin permissions are accidentally removed
   - After database migrations or restores

3. **Troubleshooting Access Issues**
   - If admin@admin.com can't access certain modules
   - After role or permission system changes

## Related Documentation

- `SQLs/README_MENU_SCRIPTS.md` - General menu permission scripts
- `GRANULAR_PERMISSIONS_IMPLEMENTATION_GUIDE.md` - Granular permission system details
- `READMEs/ADMIN_MENU_PERMISSIONS_UPDATE.md` - Original admin permission documentation
- `READMEs/ROLE_PERMISSIONS_ISSUE_RESOLVED.md` - Permission system troubleshooting

## Troubleshooting

### Issue: Script fails with "User not found"
**Solution:** Verify the user exists:
```sql
SELECT id, email, name FROM users WHERE id = 19;
```

### Issue: "Table user_permissions doesn't exist"
**Solution:** Run the migration first:
```bash
php spark migrate
# Or use the SQL file
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < SQLs/add_granular_permissions.sql
```

### Issue: User still can't access some menus
**Solution:**
1. Clear browser cache and cookies
2. Log out and log back in (permissions are loaded at login)
3. Check if the menu items exist:
```sql
SELECT id, name, link FROM menu ORDER BY id;
```
4. Verify session data in CodeIgniter

## Security Notes

- These scripts grant **FULL ACCESS** to all CRM modules
- Only use for trusted superuser accounts
- User ID 19 should be a designated admin account
- Consider using role-based permissions for regular users
- The granular permission system allows fine-tuned access control

## Support

For issues or questions:
1. Check the verification queries above
2. Review related documentation files
3. Check CodeIgniter logs: `ci4/writable/logs/`
4. Review permission loading in `ci4/app/Controllers/Home.php` (login handler)

---

**Last Updated:** 2025-10-20  
**Author:** Claude Code  
**Version:** 1.0

