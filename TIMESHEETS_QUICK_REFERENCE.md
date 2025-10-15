# Timesheets Module - Quick Reference

## 🚀 Quick Production Deployment (5 Minutes)

### Step 1: Database (1 minute)
```bash
mysql -u [user] -p [database] < SQLs/timesheets_module_production_deployment.sql
```

### Step 2: Upload Files (2 minutes)
```bash
# Upload these directories/files:
ci4/app/Controllers/Timesheets.php
ci4/app/Models/Timesheets_model.php
ci4/app/Views/timesheets/
ci4/app/Traits/PermissionTrait.php
ci4/app/Config/Routes.php (updated)
```

### Step 3: Clear Cache (1 minute)
```bash
php spark cache:clear
composer dump-autoload -o
service php-fpm restart && service nginx restart
```

### Step 4: Test (1 minute)
1. Log out and log back in
2. Look for "Timesheets" menu item
3. Navigate to `/timesheets`
4. Click "Start New Timer"

Done! ✅

---

## 📋 Files to Deploy

### New Files (Must Deploy)
```
ci4/app/Controllers/Timesheets.php          (353 lines)
ci4/app/Models/Timesheets_model.php         (229 lines)
ci4/app/Views/timesheets/list.php           (498 lines)
ci4/app/Views/timesheets/edit.php           (449 lines)
ci4/app/Traits/PermissionTrait.php          (if not deployed)
```

### Modified Files (Must Deploy)
```
ci4/app/Config/Routes.php                   (lines 54, 214-225 added)
```

### SQL Files (For Production)
```
SQLs/timesheets_module_production_deployment.sql    (Complete deployment)
SQLs/create_timesheets_table.sql                   (Table only)
```

---

## 🗄️ Database Changes

### New Table
- `timesheets` (25 columns, 8 indexes)

### New Menu Entry
- Name: "Timesheets"
- Link: `/timesheets`
- Icon: `fa fa-clock`
- Sort: 9822

---

## 🔗 URLs & Routes

### User Interface
```
/timesheets              → List view with summary cards
/timesheets/edit         → Create new timesheet (start timer)
/timesheets/edit/[uuid]  → Edit existing timesheet
```

### API Endpoints
```
POST /timesheets/update           → Save timesheet
POST /timesheets/startTimer       → Start new timer
POST /timesheets/stopTimer/[uuid] → Stop running timer
POST /timesheets/createInvoice    → Create invoice from selected
GET  /timesheets/timesheetsList   → DataTables AJAX endpoint
POST /timesheets/delete/[uuid]    → Delete timesheet
```

### API v2 Resource
```
GET    /api/v2/timesheets          → List all
POST   /api/v2/timesheets          → Create
GET    /api/v2/timesheets/[id]     → Get one
PUT    /api/v2/timesheets/[id]     → Update
DELETE /api/v2/timesheets/[id]     → Delete
```

---

## 🎨 Key Features

### Timer System
- ⏱️ Real-time timer display (HH:MM:SS)
- ▶️ One-click start
- ⏹️ One-click stop with auto-calculation
- 🔴 Pulsing animation for active timers

### Invoice Generation
- ☑️ Bulk select timesheets
- 💷 Auto-calculate totals
- 📄 Create invoice with one click
- ✅ Auto-mark as invoiced

### Dashboard Cards
1. **Hours This Week** - Blue card
2. **Hours This Month** - Green card
3. **Uninvoiced Amount** - Orange card
4. **Running Timers** - Purple card

### Quick Time Buttons
- +15 min | +30 min | +1 hour | +2 hours | +4 hours | Full Day (8h)

---

## 🔐 Permissions

### Permission Checks (Automatic)
- **Read**: View timesheets list (default: granted to all)
- **Create**: Start timer, create timesheet
- **Update**: Edit, stop timer
- **Delete**: Remove timesheet

### Admin Access
- User ID 1 has full access automatically
- No permission checks for super admin

### User Access
- Configured via User Management → Edit User → Timesheets permissions
- Users must log out/in after permission changes

---

## 🔧 Troubleshooting Quick Fixes

### "Menu not showing"
```bash
# Users must log out and log back in
```

### "Trait not found"
```bash
composer dump-autoload -o
service php-fpm restart
```

### "Table doesn't exist"
```bash
mysql -u [user] -p [db] < SQLs/timesheets_module_production_deployment.sql
```

### "Permission denied"
```bash
chmod 755 /var/www/html/app/Traits/
chmod 644 /var/www/html/app/Traits/PermissionTrait.php
chown -R www-data:www-data /var/www/html/app/
```

### "Routes not working"
```bash
php spark cache:clear
service nginx restart
```

---

## 📊 Database Schema (Quick View)

### Main Fields
```
id, uuid, uuid_business_id          → Identifiers
employee_id, project_id, task_id    → Relations
customer_id                         → Billing relation
start_time, end_time                → Time tracking
duration_minutes, billable_hours    → Calculated time
hourly_rate, total_amount           → Billing
is_billable, is_running, is_invoiced → Status flags
status (enum)                       → Workflow state
invoice_id                          → Invoice link
```

### Key Indexes
```
idx_uuid              → Fast UUID lookups
idx_business          → Multi-tenant isolation
idx_employee          → Filter by employee
idx_project           → Filter by project
idx_customer          → Filter by customer
idx_invoiced          → Find uninvoiced
idx_status            → Filter by status
idx_created           → Sort by date
```

---

## 🎯 Testing Checklist

### Basic Functionality
- [ ] Menu item visible after login
- [ ] List page loads with 4 cards
- [ ] Can create new timesheet
- [ ] Can start timer
- [ ] Timer displays correctly
- [ ] Can stop timer
- [ ] Duration auto-calculates
- [ ] Amount auto-calculates

### Advanced Features
- [ ] Can filter by status
- [ ] Can filter by employee
- [ ] Can filter by project
- [ ] Can select multiple timesheets
- [ ] Bulk invoice creation works
- [ ] Invoice links correctly
- [ ] Timesheets marked as invoiced

### Permissions
- [ ] Admin has full access
- [ ] Regular user sees read-only if no create permission
- [ ] Buttons disable based on permissions
- [ ] 403 error on unauthorized access

### Performance
- [ ] List page loads < 2 seconds
- [ ] Timer updates smoothly
- [ ] DataTable pagination works
- [ ] Large datasets load efficiently

---

## 📝 Common Queries

### Find Running Timers
```sql
SELECT t.*, e.first_name, e.surname
FROM timesheets t
LEFT JOIN employees e ON t.employee_id = e.id
WHERE t.is_running = 1
AND t.uuid_business_id = '[your-business-uuid]';
```

### Find Uninvoiced Billable Hours
```sql
SELECT SUM(billable_hours) as total_hours,
       SUM(total_amount) as total_amount
FROM timesheets
WHERE is_invoiced = 0
AND is_billable = 1
AND status IN ('stopped', 'completed')
AND uuid_business_id = '[your-business-uuid]';
```

### Hours by Employee This Month
```sql
SELECT e.first_name, e.surname,
       SUM(t.billable_hours) as hours,
       SUM(t.total_amount) as amount
FROM timesheets t
LEFT JOIN employees e ON t.employee_id = e.id
WHERE t.created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')
AND t.uuid_business_id = '[your-business-uuid]'
GROUP BY e.id
ORDER BY hours DESC;
```

---

## 💡 Usage Tips

### For Time Tracking
1. Start timer when beginning work
2. Let it run in background
3. Stop when done - auto-calculates everything
4. Add notes for detailed description

### For Manual Entry
1. Use Quick Time Buttons for common durations
2. Or set start/end times manually
3. System auto-calculates duration and amount
4. Set billable flag as needed

### For Invoicing
1. Filter to show completed timesheets
2. Check boxes for items to invoice
3. Click "Create Invoice from Selected"
4. System creates invoice and redirects to it
5. Review and adjust if needed

### Best Practices
- Start timer at beginning of work
- Add meaningful descriptions
- Tag timesheets for easier filtering
- Review and approve before invoicing
- Set accurate hourly rates per employee/project

---

## 🔄 Integration Points

### Integrates With
- ✅ Employees (for tracking who)
- ✅ Projects (for tracking what)
- ✅ Tasks (for detailed tracking)
- ✅ Customers (for billing)
- ✅ Sales Invoices (for invoicing)
- ✅ Menu System (for navigation)
- ✅ Permission System (for access control)

### Does NOT Affect
- ✅ Timeslips (legacy module remains intact)
- ✅ Existing permissions
- ✅ Other modules

---

## 📞 Support

### Documentation
- Full Guide: `TIMESHEETS_DEPLOYMENT_GUIDE.md`
- Permission System: `PERMISSION_TRAIT_GUIDE.md`
- UI Standards: `SUBMIT_BUTTON_GUIDE.md`

### Code Locations
- Controller: `ci4/app/Controllers/Timesheets.php`
- Model: `ci4/app/Models/Timesheets_model.php`
- Views: `ci4/app/Views/timesheets/`
- Routes: `ci4/app/Config/Routes.php` (lines 54, 214-225)

### SQL Scripts
- Full Deployment: `SQLs/timesheets_module_production_deployment.sql`
- Table Only: `SQLs/create_timesheets_table.sql`

---

**Version:** 1.0.0
**Created:** January 14, 2025
**Status:** Production Ready ✅
