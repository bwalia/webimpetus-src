# Quick Start: Grant All Permissions to Admin ID 19

## TL;DR - Just Run This

```bash
# Option 1: SQL Script (fastest)
docker exec workerra-ci-db mariadb -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev < SQLs/grant_all_permissions_to_admin_id19.sql

# Option 2: PHP Script (more detailed output)
php SQLs/grant_all_permissions_to_admin_id19.php
```

## What This Does

✅ Grants **admin@admin.com (User ID 19)** full access to ALL CRM menus  
✅ Sets up both legacy and granular permissions  
✅ Enables full CRUD (Create, Read, Update, Delete) on all modules

## Quick Verification

```sql
-- Check total permissions
SELECT 
    email, 
    JSON_LENGTH(permissions) as menu_count 
FROM users 
WHERE id = 19;

-- Check granular permissions
SELECT 
    COUNT(*) as total,
    SUM(can_read) as read,
    SUM(can_create) as create,
    SUM(can_update) as update,
    SUM(can_delete) as delete
FROM user_permissions 
WHERE user_id = 19;
```

## After Running

1. **Log out** from admin@admin.com
2. **Log back in** (permissions load at login)
3. **Done!** All menus should now be accessible

## Troubleshooting

| Problem | Solution |
|---------|----------|
| User still can't see menus | Clear browser cache, log out/in |
| Script says "user not found" | Check user exists: `SELECT * FROM users WHERE id = 19` |
| Permission count is 0 | Run the script again, check for errors |

## Files Created

- `SQLs/grant_all_permissions_to_admin_id19.sql` - SQL script
- `SQLs/grant_all_permissions_to_admin_id19.php` - PHP script  
- `SQLs/README_ADMIN_ID19_PERMISSIONS.md` - Full documentation

## User Details

| Field | Value |
|-------|-------|
| **User ID** | 19 |
| **Email** | admin@admin.com |
| **Access Level** | Superuser - Full CRUD on all modules |

---
📖 **Full Documentation:** See `SQLs/README_ADMIN_ID19_PERMISSIONS.md`

