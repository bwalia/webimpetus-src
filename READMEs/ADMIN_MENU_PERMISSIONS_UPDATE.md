# Admin Menu Permissions Update

**Date:** 2025-10-11
**User:** admin@admin.com (UUID: 51735)

## Summary

Successfully granted all menu access permissions to the admin@admin.com user. The user now has access to all 45 menu items in the system.

## Changes Made

### Database Updates

**Table:** `users`
**Field:** `permissions`
**User:** admin@admin.com (UUID: 51735)

**Previous Permission Count:** 42
**New Permission Count:** 45
**Added Permissions:** IDs 35, 36, 38, 44, 45

## Full Menu Access List

The admin user now has access to all of the following menu items:

| ID | Menu Name | Link |
|----|-----------|------|
| 1  | Dashboard | /dashboard |
| 2  | Categories | /categories |
| 3  | Users | /users |
| 4  | Tenants | /tenants |
| 5  | Services | /services |
| 6  | Domains | /domains |
| 7  | Web Pages | /webpages |
| 8  | Blog | /blog |
| 9  | Blog Comments | /blog_comments |
| 10 | Job Vacancies | /jobs |
| 11 | Job Applications | /jobapps |
| 12 | Image Gallery | /gallery |
| 13 | Blocks | /blocks |
| 14 | Enquiries | /enquiries |
| 15 | Secrets | /secrets |
| 16 | Customers | /customers |
| 17 | Contacts | /contacts |
| 18 | My Workspaces | /businesses |
| 19 | Work Orders | /work_orders |
| 20 | Employees | /employees |
| 21 | Projects | /projects |
| 22 | Templates | /templates |
| 23 | Sales Invoices | /sales_invoices |
| 24 | Tasks | /tasks |
| 25 | Timeslips | /timeslips |
| 26 | Timeslips Calendar | /fullcalendar |
| 27 | Purchase Orders | /purchase_orders |
| 28 | Documents | /documents |
| 29 | Purchase Invoices | /purchase_invoices |
| 30 | Strategies | /webpages?cat=strategies |
| 31 | Sprints | /sprints |
| 32 | Kanban Board | /kanban_board |
| 33 | Menu | /menu |
| 34 | User Workspaces | /user_business |
| 35 | VAT Codes | /taxes |
| 36 | Roles | /roles |
| 37 | VAT Returns | /vat_returns |
| 38 | Launchpad | /launchpad |
| 39 | Incidents | /incidents |
| 40 | Knowledge Base | /knowledge_base |
| 41 | Tags | /tags |
| 42 | Deployments | /deployments |
| 43 | Email Campaigns | /email_campaigns |
| 44 | Scrum Board | /scrum_board |
| 45 | **Interviews** | **/interviews** |

## Scripts Created

Two scripts have been created for future reference and reuse:

### 1. SQL Script
**Location:** `SQLs/grant_all_menu_permissions_to_admin.sql`

This script dynamically fetches all menu IDs and assigns them to the admin user.

**Usage:**
```bash
docker exec workerra-ci-db mariadb -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev < SQLs/grant_all_menu_permissions_to_admin.sql
```

### 2. PHP Script
**Location:** `SQLs/grant_all_menu_permissions_to_admin.php`

This script provides a more detailed output with verification steps.

**Usage:**
```bash
# From host (if PHP installed)
php SQLs/grant_all_menu_permissions_to_admin.php

# Or via Docker
docker exec workerra-ci-dev php /var/www/html/SQLs/grant_all_menu_permissions_to_admin.php
```

## Verification

To verify permissions at any time, run:

```sql
SELECT
    uuid,
    email,
    JSON_LENGTH(permissions) as permission_count
FROM users
WHERE email = 'admin@admin.com';
```

Expected output:
- **permission_count:** 45

## Database Schema Reference

### Users Table Structure
- **Field:** `permissions`
- **Type:** `text`
- **Format:** JSON array of menu IDs as strings
- **Example:** `["1", "2", "3", ... "45"]`

### Menu Table Structure
- **Primary Key:** `id` (int)
- **Fields:** name, link, icon, uuid_business_id, sort_order, language_code

## Notes

- The permissions are stored as a JSON array in the `permissions` field
- Menu IDs are stored as strings, not integers
- The admin user's role UUID is: `3b2de757-f6b0-5d80-975d-37d534ea0cb9`
- The role is "Administrator" (ID 6)

## Future Maintenance

When new menu items are added to the system:

1. Run the SQL or PHP script to automatically update admin permissions
2. Or manually add the new menu ID to the permissions array
3. Ensure menu items are properly registered in the `menu` table

## Related Files

- `ci4/app/Controllers/Interviews.php` - Interview controller
- `ci4/app/Views/interviews/dashboard.php` - Interview dashboard
- `ci4/app/Views/interviews/schedule.php` - Interview scheduling form
- `ci4/app/Views/interviews/view.php` - Interview evaluation page
- `ci4/app/Database/Migrations/2025-10-11-130000_CreateInterviewsTable.php`
- `ci4/app/Database/Migrations/2025-10-11-131000_CreateInterviewCandidatesTable.php`

## Testing

To test the permissions:

1. Log in as admin@admin.com
2. Navigate to https://dev001.workstation.co.uk/interviews
3. Verify that the Interviews menu is visible in the sidebar
4. Verify access to all interview functionality:
   - Dashboard view
   - Schedule new interview
   - View/edit interviews
   - Candidate evaluation

All menu items should now be accessible without permission errors.
