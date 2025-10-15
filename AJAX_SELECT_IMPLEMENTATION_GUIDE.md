# AJAX Select2 Implementation Guide
## Centralized Search for All Modules

### Overview
This system provides **centralized AJAX search endpoints** that work across all modules, eliminating the need for hundreds/thousands of records to be loaded upfront in dropdowns.

---

## 🎯 **What's Implemented**

### 1. **Centralized CommonAjax Controller**
- **File**: `ci4/app/Controllers/CommonAjax.php`
- **Purpose**: Single controller with all AJAX search methods
- **17 Entity Types Supported**:
  - Employees
  - Customers
  - Contacts
  - Projects
  - Tasks
  - Users
  - Businesses
  - Categories
  - Sprints
  - Templates
  - Roles
  - Tags
  - Services
  - Purchase Invoices
  - Sales Invoices
  - Domains
  - Work Orders

### 2. **JavaScript Library**
- **File**: `ci4/public/js/ajax-select-init.js`
- **Purpose**: Reusable Select2 initialization functions
- **Features**:
  - One-line initialization
  - Cascading select support
  - Filter select support
  - Auto-initialization
  - Custom formatting

### 3. **Routes Configuration**
- **Base URL**: `/common/search{EntityType}`
- **Examples**:
  - `/common/searchEmployees?q=john`
  - `/common/searchCustomers?q=acme`
  - `/common/searchProjects?q=website&customer_id=5`

---

## 🚀 **Quick Start - How to Use**

### Option 1: Simple Implementation (Auto-Init)

**Step 1**: Add the JavaScript library to your view
```php
<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<!-- Your form HTML -->

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script src="/js/ajax-select-init.js"></script>
<script>
    var autoInitAjaxSelects = true; // Enable auto-init
</script>
```

**Step 2**: Use standard CSS classes in your select elements
```html
<select name="employee_id" id="employee_id" class="form-control select-employee-ajax">
    <option value="">-- Type to search employees --</option>
    <?php if (!empty($selected_employee)): ?>
        <option value="<?= $selected_employee['id'] ?>" selected>
            <?= $selected_employee['first_name'] ?> <?= $selected_employee['surname'] ?>
        </option>
    <?php endif; ?>
</select>
```

**Step 3**: Load selected value in controller (for edit mode)
```php
if (!empty($id) && !empty($this->data[$this->rawTblName])) {
    $record = $this->data[$this->rawTblName];

    if (!empty($record->employee_id)) {
        $this->data['selected_employee'] = $this->db->table('employees')
            ->where('id', $record->employee_id)
            ->get()->getRowArray();
    }
}
```

Done! 🎉

---

### Option 2: Manual Initialization

```html
<script src="/js/ajax-select-init.js"></script>
<script>
$(document).ready(function() {
    // Initialize individual selects
    initAjaxSelect('.select-employee-ajax', 'employees');
    initAjaxSelect('.select-customer-ajax', 'customers');
    initAjaxSelect('.select-project-ajax', 'projects');

    // Or initialize all at once
    initCommonSelects();
});
</script>
```

---

### Option 3: Cascading Selects

For Customer → Project → Task hierarchy:

```html
<script src="/js/ajax-select-init.js"></script>
<script>
$(document).ready(function() {
    initCascadingSelects({
        customer: {
            selector: '#customer_id',
            entityType: 'customers'
        },
        project: {
            selector: '#project_id',
            entityType: 'projects',
            dependsOn: 'customer'
        },
        task: {
            selector: '#task_id',
            entityType: 'tasks',
            dependsOn: 'project'
        }
    });
});
</script>
```

---

### Option 4: Filter Selects (Lists/Reports)

For filter dropdowns that should load top 50 on open (without typing):

```html
<script src="/js/ajax-select-init.js"></script>
<script>
$(document).ready(function() {
    initFilterSelect('#filterEmployee', 'employees');
    initFilterSelect('#filterProject', 'projects');
});
</script>
```

---

## 📋 **Standard CSS Classes**

Use these classes for auto-initialization:

| Entity Type | CSS Class |
|------------|-----------|
| Employees | `.select-employee-ajax` |
| Customers | `.select-customer-ajax` |
| Contacts | `.select-contact-ajax` |
| Projects | `.select-project-ajax` |
| Tasks | `.select-task-ajax` |
| Users | `.select-user-ajax` |
| Businesses | `.select-business-ajax` |
| Categories | `.select-category-ajax` |
| Sprints | `.select-sprint-ajax` |
| Templates | `.select-template-ajax` |
| Roles | `.select-role-ajax` |
| Tags | `.select-tag-ajax` |
| Services | `.select-service-ajax` |

For filters, add `-filter` suffix: `.select-employee-filter-ajax`

---

## 🔧 **Controller Pattern**

### Remove Old Data Loading

**Before** (loads all records):
```php
public function edit($id = '') {
    $this->data['employees'] = $this->model->getAllDataFromTable('employees');
    $this->data['customers'] = $this->model->getAllDataFromTable('customers');
    $this->data['projects'] = $this->model->getAllDataFromTable('projects');

    echo view($this->table . "/edit", $this->data);
}
```

**After** (only load selected for edit mode):
```php
public function edit($id = '') {
    // Load selected records only for edit mode
    if (!empty($id) && !empty($this->data[$this->rawTblName])) {
        $record = $this->data[$this->rawTblName];

        // Load selected employee
        if (!empty($record->employee_id)) {
            $this->data['selected_employee'] = $this->db->table('employees')
                ->where('id', $record->employee_id)
                ->get()->getRowArray();
        }

        // Load selected customer
        if (!empty($record->customer_id)) {
            $this->data['selected_customer'] = $this->db->table('customers')
                ->where('id', $record->customer_id)
                ->get()->getRowArray();
        }
    }

    echo view($this->table . "/edit", $this->data);
}
```

---

## 📝 **View Pattern**

### Edit Forms

**Before** (loops through all records):
```php
<select name="employee_id" id="employee_id" class="form-control">
    <option value="">-- Select Employee --</option>
    <?php foreach ($employees as $emp): ?>
        <option value="<?= $emp['id'] ?>" <?= @$record->employee_id == $emp['id'] ? 'selected' : '' ?>>
            <?= $emp['first_name'] ?> <?= $emp['surname'] ?>
        </option>
    <?php endforeach; ?>
</select>
```

**After** (AJAX search):
```php
<select name="employee_id" id="employee_id" class="form-control select-employee-ajax required" required>
    <option value="">-- Type to search employees --</option>
    <?php if (!empty($selected_employee)): ?>
        <option value="<?= $selected_employee['id'] ?>" selected>
            <?= $selected_employee['first_name'] ?> <?= $selected_employee['surname'] ?>
        </option>
    <?php endif; ?>
</select>
```

### List Filters

**Before**:
```php
<select class="form-control" id="filterEmployee">
    <option value="">All Employees</option>
    <?php foreach ($employees ?? [] as $emp): ?>
        <option value="<?= $emp['id'] ?>"><?= $emp['first_name'] ?> <?= $emp['surname'] ?></option>
    <?php endforeach; ?>
</select>
```

**After**:
```php
<select class="form-control select-employee-filter-ajax" id="filterEmployee">
    <option value="">All Employees</option>
</select>
```

---

## 🎨 **Custom Formatting**

### Example: Custom Display Format

```javascript
initAjaxSelect('.select-employee-ajax', 'employees', {
    formatResult: function(item) {
        return item.first_name + ' ' + item.surname +
               ' (' + item.job_title + ') - ' + item.email;
    },
    formatSelection: function(item) {
        return item.first_name + ' ' + item.surname;
    }
});
```

### Example: With Custom Parameters

```javascript
initAjaxSelect('.select-project-ajax', 'projects', {
    customParams: {
        customer_id: function() {
            return $('#customer_id').val();
        },
        status: 'active'
    }
});
```

---

## 📊 **Available Endpoints**

All endpoints support these parameters:
- `q` - Search term (string)
- Additional entity-specific filters (e.g., `customer_id`, `project_id`)

| Endpoint | Searches | Filters |
|----------|----------|---------|
| `/common/searchEmployees` | first_name, surname, email | - |
| `/common/searchCustomers` | company_name, email, phone | - |
| `/common/searchContacts` | first_name, surname, email, company | customer_id |
| `/common/searchProjects` | name, project_code, customer | customer_id |
| `/common/searchTasks` | name, project_name | project_id |
| `/common/searchUsers` | name, email | - |
| `/common/searchBusinesses` | name, company_email | - |
| `/common/searchCategories` | name | - |
| `/common/searchSprints` | sprint_name | project_id |
| `/common/searchTemplates` | name, type | - |
| `/common/searchRoles` | name | - |
| `/common/searchTags` | name | - |
| `/common/searchServices` | name, description | - |
| `/common/searchPurchaseInvoices` | invoice_number, supplier_name | - |
| `/common/searchSalesInvoices` | invoice_number, customer | customer_id |
| `/common/searchDomains` | name | - |
| `/common/searchWorkOrders` | order_number, title, customer | customer_id |

---

## 🔒 **Security**

All endpoints:
- ✅ Filter by `uuid_business_id` automatically (multi-tenant safe)
- ✅ Require user authentication (extends CommonController)
- ✅ Use parameter binding (SQL injection safe)
- ✅ Limit results to 50 records (performance safe)

---

## 📦 **Module Rollout Plan**

### High Priority (Large Datasets)
1. ✅ **Timesheets** - DONE
2. **Tasks** - employees, customers, contacts, projects
3. **Projects** - customers, employees
4. **Work Orders** - customers, projects
5. **Sales Invoices** - customers, projects
6. **Purchase Invoices** - suppliers (if applicable)
7. **Contacts** - customers

### Medium Priority
8. **Documents** - customers, projects, tags
9. **Employees** - users, roles
10. **Users** - businesses, roles
11. **Email Campaigns** - contacts, customers
12. **Incidents** - customers, assigned_to
13. **Deployments** - projects, services

### Low Priority (Small Datasets)
14. Categories
15. Templates
16. Roles
17. Tags
18. Sprints

---

## 🧪 **Testing Checklist**

For each module:
- [ ] Edit form loads without errors
- [ ] Employee dropdown: Type 2 chars → see results
- [ ] Customer dropdown: Type 2 chars → see results
- [ ] Project dropdown: Type 2 chars → see results (filtered by customer if selected)
- [ ] Task dropdown: Type 2 chars → see results (filtered by project if selected)
- [ ] Selected values display correctly when editing existing record
- [ ] Clear button (X) works
- [ ] Form submission saves correct IDs
- [ ] Filter dropdowns on list page work
- [ ] Page load is fast (< 1 second)

---

## 🚨 **Troubleshooting**

### "Select2 is not a function"
**Solution**: Ensure Select2 library is loaded before ajax-select-init.js
```html
<script src="/path/to/select2.js"></script>
<script src="/js/ajax-select-init.js"></script>
```

### "No results found" but data exists
**Solution**: Check minimum input length setting
```javascript
initAjaxSelect('.select-employee-ajax', 'employees', {
    minimumInputLength: 0  // Set to 0 for filters
});
```

### Selected value not showing on edit
**Solution**: Ensure controller loads selected record
```php
if (!empty($record->employee_id)) {
    $this->data['selected_employee'] = $this->db->table('employees')
        ->where('id', $record->employee_id)
        ->get()->getRowArray();
}
```

### Cascading not working
**Solution**: Ensure parent ID is accessible
```javascript
customParams: {
    customer_id: function() {
        return $('#customer_id').val();  // Must return value, not element
    }
}
```

---

## 📈 **Performance Benefits**

### Before (Loading All Data)
- 500 employees = ~50KB
- 1000 customers = ~100KB
- 2000 projects = ~200KB
- **Total: 350KB + slow page load (3-5 seconds)**

### After (AJAX Search)
- Initial load: ~5KB (no records)
- Each search: ~5KB (50 results max)
- **Total: ~95% reduction**
- **Page loads instantly (< 0.5 seconds)**

---

## 📚 **Examples**

### Example 1: Simple Task Form
```php
<!-- View: tasks/edit.php -->
<select name="projects_id" class="form-control select-project-ajax required" required>
    <option value="">-- Type to search projects --</option>
    <?php if (!empty($selected_project)): ?>
        <option value="<?= $selected_project['id'] ?>" selected>
            <?= $selected_project['name'] ?>
        </option>
    <?php endif; ?>
</select>

<select name="customers_id" class="form-control select-customer-ajax required" required>
    <option value="">-- Type to search customers --</option>
    <?php if (!empty($selected_customer)): ?>
        <option value="<?= $selected_customer['id'] ?>" selected>
            <?= $selected_customer['company_name'] ?>
        </option>
    <?php endif; ?>
</select>

<script src="/js/ajax-select-init.js"></script>
<script>
    var autoInitAjaxSelects = true;
</script>
```

```php
// Controller: Tasks.php
public function edit($id = '') {
    if (!empty($id)) {
        $this->data['task'] = $this->task_model->where('uuid', $id)->first();

        if (!empty($this->data['task']->projects_id)) {
            $this->data['selected_project'] = $this->db->table('projects')
                ->where('id', $this->data['task']->projects_id)
                ->get()->getRowArray();
        }

        if (!empty($this->data['task']->customers_id)) {
            $this->data['selected_customer'] = $this->db->table('customers')
                ->where('id', $this->data['task']->customers_id)
                ->get()->getRowArray();
        }
    }

    echo view('tasks/edit', $this->data);
}
```

---

## ✅ **Migration Checklist**

For each module:
1. [ ] Remove `getAllDataFromTable()` calls from controller
2. [ ] Add selected record loading for edit mode
3. [ ] Update view select elements with AJAX classes
4. [ ] Add selected value conditional display
5. [ ] Include ajax-select-init.js script
6. [ ] Enable auto-init or manual init
7. [ ] Test create mode (no selection)
8. [ ] Test edit mode (has selection)
9. [ ] Test save functionality
10. [ ] Test filter dropdowns if applicable

---

## 🎯 **Next Steps**

1. **Test timesheets module** (already implemented)
2. **Roll out to Tasks module** (high usage)
3. **Roll out to Projects module** (high usage)
4. **Document module-specific changes**
5. **Update remaining modules progressively**

---

**Version**: 1.0.0
**Created**: January 14, 2025
**Status**: Core Infrastructure Ready ✅
**Rollout**: In Progress 🚀
