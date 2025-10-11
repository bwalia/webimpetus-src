# Role-Based Permissions Issue - RESOLVED ✅

## 🎯 The Real Issue

Your user (ID 19, Balinder Walia) has a **UUID-based role** assigned, which means permissions are loaded from the `roles__permissions` table, **NOT** from the direct `permissions` field in the users table.

## 🔍 What We Found

### User Details:
- **ID**: 19
- **Name**: Balinder Walia
- **Email**: balinder.walia@gmail.com
- **Role UUID**: `63486190-f1d2-59cc-a9b1-a6711da43806`
- **Direct Permissions**: `["25","23","1",...,"42"]` ← **IGNORED when role is set!**

### The Problem:
The role was missing several menu permissions including:
- **Deployments** (ID 42) - The one you specifically couldn't access
- VAT Returns (ID 37)
- Launchpad (ID 38)
- Incidents (ID 39)
- Knowledge Base (ID 40)
- Tags (ID 41)

## ✅ The Solution

I added the missing permissions to your role:

```sql
INSERT INTO roles__permissions (role_id, permission_id) VALUES
('63486190-f1d2-59cc-a9b1-a6711da43806', '20fab011-a64b-11f0-85b5-6a575c7e071d'), -- Deployments
('63486190-f1d2-59cc-a9b1-a6711da43806', '19a54e6c-a62e-11f0-85b5-6a575c7e071d'), -- VAT Returns
('63486190-f1d2-59cc-a9b1-a6711da43806', '81a0806d-a63c-11f0-85b5-6a575c7e071d'), -- Launchpad
('63486190-f1d2-59cc-a9b1-a6711da43806', '57762bd3-a645-11f0-85b5-6a575c7e071d'), -- Incidents
('63486190-f1d2-59cc-a9b1-a6711da43806', '57851178-a645-11f0-85b5-6a575c7e071d'), -- Knowledge Base
('63486190-f1d2-59cc-a9b1-a6711da43806', 'a11fd962-a64a-11f0-85b5-6a575c7e071d'); -- Tags
```

## 🧪 How to Test

1. **Logout** completely from your current session
2. **Login** again as Balinder Walia (user ID 19)
3. **Navigate to** `/deployments`
4. ✅ **You should now have access!**

## 📊 Current Permissions

After the fix, user ID 19 now has access to **27 modules**:

| ID | Module | Link |
|----|--------|------|
| 1 | Dashboard | /dashboard |
| 2 | Categories | /categories |
| 3 | Users | /users |
| 7 | Web Pages | /webpages |
| 12 | Image Gallery | /gallery |
| 13 | Blocks | /blocks |
| 14 | Enquiries | /enquiries |
| 16 | Customers | /customers |
| 17 | Contacts | /contacts |
| 19 | Work Orders | /work_orders |
| 21 | Projects | /projects |
| 22 | Templates | /templates |
| 23 | Sales Invoices | /sales_invoices |
| 24 | Tasks | /tasks |
| 25 | Timeslips | /timeslips |
| 26 | Timeslips Calendar | /fullcalendar |
| 27 | Purchase Orders | /purchase_orders |
| 29 | Purchase Invoices | /purchase_invoices |
| 31 | Sprints | /sprints |
| 32 | Kanban Board | /kanban_board |
| 36 | Roles | /roles |
| 37 | **VAT Returns** ✅ | /vat_returns |
| 38 | **Launchpad** ✅ | /launchpad |
| 39 | **Incidents** ✅ | /incidents |
| 40 | **Knowledge Base** ✅ | /knowledge_base |
| 41 | **Tags** ✅ | /tags |
| 42 | **Deployments** ✅ | /deployments |

## 🔄 Permission Priority System

Understanding how permissions work in your system:

### 1. Admin Override (User ID = 1)
```php
if ($row->id == "1") {
    $userMenus = $this->menu_model->getRows(); // ALL menus
}
```
Admin gets ALL menus regardless of permissions/role.

### 2. UUID-Based Role (Priority)
```php
else if (isUUID($row->role)) {
    $menuArray = getResultWithoutBusiness('roles__permissions', ['role_id' => $row->role]);
    $menuIds = array_map(function($val) { return $val['permission_id']; }, $menuArray);
    $userMenus = $this->menu_model->getWhereinByUUID($menuIds);
}
```
If user has a UUID role, load permissions from `roles__permissions` table.
**Direct permissions field is IGNORED!**

### 3. Direct Permissions (Fallback)
```php
else {
    $arr = json_decode($row->permissions, true);
    $userMenus = $this->menu_model->getWherein($arr);
}
```
Only used if user has NO role or role is not a UUID.

## 🎯 Key Takeaway

**When a user has a UUID role assigned:**
- The `permissions` field in the `users` table is **IGNORED**
- Permissions come from the `roles__permissions` table
- You must add menu UUIDs to the role, not menu IDs to the user

## 🛠️ Managing Role Permissions

### To Add a Module to a Role:

1. **Get the menu UUID**:
```sql
SELECT id, uuid, name FROM menu WHERE name = 'Module Name';
```

2. **Add to role**:
```sql
INSERT INTO roles__permissions (role_id, permission_id)
VALUES ('your-role-uuid', 'menu-uuid-from-step-1');
```

3. **User must logout/login** for changes to take effect

### To View Role Permissions:

```sql
SELECT m.id, m.name, m.link, rp.permission_id
FROM roles__permissions rp
LEFT JOIN menu m ON m.uuid = rp.permission_id
WHERE rp.role_id = 'your-role-uuid'
ORDER BY m.id;
```

### To Remove a Module from a Role:

```sql
DELETE FROM roles__permissions
WHERE role_id = 'your-role-uuid'
AND permission_id = 'menu-uuid';
```

## 🔐 Security Note

Role-based permissions are more secure and easier to manage than direct permissions because:
- Changes apply to all users with that role
- Centralized permission management
- Easier to audit
- Less prone to permission drift

## 📱 Using the Debug Page

You can always check what permissions a user has by accessing:
```
https://your-domain.com/debug_permissions
```

This shows:
- Session information
- Loaded permissions
- Database permissions
- Access checks
- All accessible modules

## ✅ Verification

Run this to verify the fix:
```bash
docker exec webimpetus-dev php /var/www/html/test_permission_load.php 19
```

Should show **27 menus** including Deployments at the bottom.

## 🎊 Success!

The issue was **NOT** with the `json_decode()` fix (that was still important and correct).

The issue was that you were assigning **direct permissions** via the UI, but the user has a **UUID role** which overrides those direct permissions.

**Solution**: Permissions were added to the role itself, and now you can access Deployments and all other modules! 🎉
