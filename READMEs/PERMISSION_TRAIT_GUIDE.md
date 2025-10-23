# Permission Trait Implementation Guide

## Overview

The `PermissionTrait` provides easy-to-use permission checking methods for controllers that override `CommonController` methods. This trait helps enforce granular permissions (read, create, update, delete) across the application.

## Problem Statement

Many controllers in the application extend `CommonController` and override methods like `index()`, `edit()`, `update()`, and `delete()` without checking permissions. This creates a security vulnerability where users can bypass permission checks.

## Solution

Use the `PermissionTrait` to add permission checks to controller methods with minimal code.

---

## Quick Start

### 1. Add the Trait to Your Controller

```php
<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Traits\PermissionTrait;  // Add this

class YourController extends CommonController
{
    use PermissionTrait;  // Add this line

    // Your controller code...
}
```

### 2. Add Permission Checks to Methods

#### For index() method (listing/viewing records):
```php
public function index()
{
    $this->requireReadPermission();  // Add this line at the start

    // Pass permissions to view for button visibility
    $this->addPermissionsToView($this->data);

    // Your existing code...
    echo view($this->table . "/list", $this->data);
}
```

#### For edit() method (form to create/edit):
```php
public function edit($id = '')
{
    $this->requireEditPermission($id);  // Automatically checks create or update

    // Your existing code...
}
```

#### For update() method (save data):
```php
public function update()
{
    $uuid = $this->request->getPost('uuid');
    $this->requireEditPermission($uuid, true);  // true = redirect with message

    // Your existing code...
}
```

#### For delete() method:
```php
public function delete($uuid)
{
    $this->requireDeletePermission(true);  // true = redirect with message

    // Your existing code...
}
```

---

## Complete Example

Here's a complete example of a properly secured controller:

```php
<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Core\Common_model;
use App\Traits\PermissionTrait;

class Payments extends CommonController
{
    use PermissionTrait;

    private $payment_model;

    function __construct()
    {
        parent::__construct();
        $this->payment_model = new Payments_model();
        $this->model = new Common_model();
        $this->table = "payments";
        $this->rawTblName = "payment";
    }

    public function index()
    {
        // Check read permission
        $this->requireReadPermission();

        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;
        $this->data['is_add_permission'] = 1;

        // Pass permissions to view (for hiding/showing buttons)
        $this->addPermissionsToView($this->data);

        echo view($this->table . "/list", $this->data);
    }

    public function edit($id = '')
    {
        // Auto-checks create permission for new, update permission for existing
        $this->requireEditPermission($id);

        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;

        if (!empty($id)) {
            $this->data[$this->rawTblName] = $this->payment_model->getPaymentByUuid($id);
            // ... rest of code
        } else {
            $this->data[$this->rawTblName] = new stdClass();
            // ... rest of code
        }

        echo view($this->table . "/edit", $this->data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');

        // Check permission and redirect with message if denied
        $this->requireEditPermission($uuid, true);

        $data = $this->request->getPost();

        // Generate UUID for new payment
        if (empty($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'payments');
            $data['uuid_business_id'] = session('uuid_business');
        }

        $response = $this->model->insertOrUpdateByUUID($uuid, $data, $this->table);

        if (!$response) {
            session()->setFlashdata('message', 'Something went wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        } else {
            session()->setFlashdata('message', 'Payment saved successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        }

        return redirect()->to('/' . $this->table);
    }

    public function delete($uuid)
    {
        // Check delete permission with redirect
        $this->requireDeletePermission(true);

        $payment = $this->payment_model->where('uuid', $uuid)->first();

        if (!$payment) {
            session()->setFlashdata('message', 'Payment not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $this->model->deleteTableData($this->table, $uuid, 'uuid');

        session()->setFlashdata('message', 'Payment deleted successfully!');
        session()->setFlashdata('alert-class', 'alert-success');

        return redirect()->to('/' . $this->table);
    }
}
```

---

## Available Methods

### Basic Permission Checks

#### `requireReadPermission()`
Shows 403 error if user doesn't have read permission.

```php
$this->requireReadPermission();
```

#### `requireCreatePermission($redirect = false)`
Checks create permission. If `$redirect = true`, redirects with error message instead of showing 403.

```php
$this->requireCreatePermission();        // Shows 403 error
$this->requireCreatePermission(true);    // Redirects with message
```

#### `requireUpdatePermission($redirect = false)`
Checks update permission.

```php
$this->requireUpdatePermission();        // Shows 403 error
$this->requireUpdatePermission(true);    // Redirects with message
```

#### `requireDeletePermission($redirect = false)`
Checks delete permission.

```php
$this->requireDeletePermission();        // Shows 403 error
$this->requireDeletePermission(true);    // Redirects with message
```

#### `requireEditPermission($id = null, $redirect = false)`
**Smart method** that automatically checks:
- Create permission if `$id` is empty
- Update permission if `$id` has a value

```php
$this->requireEditPermission($uuid);        // Auto-detects, shows 403
$this->requireEditPermission($uuid, true);  // Auto-detects, redirects
```

### Helper Methods

#### `addPermissionsToView(&$data)`
Adds all permission flags to view data array. Use in `index()` methods.

```php
$this->addPermissionsToView($this->data);
// Adds: can_read, can_create, can_update, can_delete to $this->data
```

#### `getPermissions()`
Returns array of all permissions for current module.

```php
$perms = $this->getPermissions();
// Returns: ['read' => true, 'create' => false, 'update' => false, 'delete' => false]
```

#### `isViewOnly()`
Checks if user has view-only access (read but no modify permissions).

```php
if ($this->isViewOnly()) {
    // User can only view, not modify
}
```

#### `hasFullAccess()`
Checks if user has all CRUD permissions.

```php
if ($this->hasFullAccess()) {
    // User can do everything
}
```

### Advanced Methods

#### `requireAnyPermission(array $permissions, $redirect = false)`
Requires at least one of the specified permissions (OR logic).

```php
// User needs either create OR update permission
$this->requireAnyPermission(['create', 'update']);
$this->requireAnyPermission(['create', 'update'], true); // with redirect
```

#### `requireAllPermissions(array $permissions, $redirect = false)`
Requires all of the specified permissions (AND logic).

```php
// User needs both read AND update permission
$this->requireAllPermissions(['read', 'update']);
$this->requireAllPermissions(['read', 'update'], true); // with redirect
```

---

## Controllers That Need Updating

The following controllers extend `CommonController` and override methods without permission checks. They should be updated to use the `PermissionTrait`:

### High Priority (Financial/Accounting):
- ✅ **Payments** - COMPLETED
- ✅ **Receipts** - COMPLETED
- ❌ **AccountingPeriods**
- ❌ **Accounts**
- ❌ **JournalEntries**
- ❌ **Purchase_invoices**
- ❌ **Purchase_orders**
- ❌ **Sales_invoices**
- ❌ **Work_orders**

### Medium Priority (Core Business):
- ❌ **Contacts**
- ❌ **Customers**
- ❌ **Employees**
- ❌ **Products**
- ❌ **Projects**
- ❌ **Tasks**
- ❌ **Sprints**

### Medium Priority (Content/Documents):
- ❌ **Blog**
- ❌ **Blog_comments**
- ❌ **Documents**
- ❌ **Knowledge_base**
- ❌ **Media_list**
- ❌ **Gallery**
- ❌ **Templates**

### Lower Priority (Configuration):
- ❌ **Blocks**
- ❌ **Business_contacts**
- ❌ **Businesses**
- ❌ **Categories**
- ❌ **Menu**
- ❌ **Roles**
- ❌ **Users**
- ❌ **User_business**
- ❌ **Webpages**

### Infrastructure:
- ❌ **Deployments**
- ❌ **Domains**
- ❌ **Secrets**
- ❌ **Tenants**
- ❌ **Vm**

### Other:
- ❌ **Email_campaigns**
- ❌ **HospitalStaff**
- ❌ **Incidents**
- ❌ **Jobapps**
- ❌ **Jobs**
- ❌ **PatientLogs**

---

## Testing Checklist

After adding the trait to a controller, test these scenarios:

### 1. View-Only User
- ✅ Can access `/module` (index)
- ✅ Can view records
- ✅ Cannot see "Add New" button
- ✅ Cannot see "Edit" or "Delete" buttons
- ❌ Accessing `/module/edit` shows 403
- ❌ Accessing `/module/edit/123` shows 403
- ❌ Posting to `/module/update` redirects with error
- ❌ Accessing `/module/delete/123` redirects with error

### 2. Create-Only User (Read + Create)
- ✅ Can access `/module` (index)
- ✅ Can see "Add New" button
- ✅ Can access `/module/edit` (new form)
- ✅ Can save new records via POST to `/module/update`
- ❌ Cannot see "Edit" button on existing records
- ❌ Accessing `/module/edit/123` shows 403
- ❌ Cannot see "Delete" button

### 3. Full Access User
- ✅ Can do everything

---

## Common Patterns

### Pattern 1: Simple CRUD Controller
```php
use App\Traits\PermissionTrait;

class SimpleController extends CommonController
{
    use PermissionTrait;

    public function index()
    {
        $this->requireReadPermission();
        $this->addPermissionsToView($this->data);
        // ... rest of code
    }

    public function edit($id = '')
    {
        $this->requireEditPermission($id);
        // ... rest of code
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $this->requireEditPermission($uuid, true);
        // ... rest of code
    }

    public function delete($uuid)
    {
        $this->requireDeletePermission(true);
        // ... rest of code
    }
}
```

### Pattern 2: Special Actions Requiring Multiple Permissions
```php
public function post($uuid)
{
    // Posting requires both read and update permissions
    $this->requireAllPermissions(['read', 'update'], true);

    // Post to journal logic...
}
```

### Pattern 3: Actions That Don't Require Permissions
```php
public function export()
{
    // Export might only need read permission
    $this->requireReadPermission();

    // Export logic...
}
```

---

## FAQ

**Q: Do I need to update controllers that don't override CommonController methods?**
A: No. If your controller extends `CommonController` and doesn't override `index()`, `edit()`, `update()`, or `delete()`, then it's already using the permission checks from `CommonController`.

**Q: What's the difference between `requireEditPermission()` and calling `requireCreatePermission()` / `requireUpdatePermission()` separately?**
A: `requireEditPermission($id)` is a convenience method that automatically detects whether to check create or update based on whether `$id` is empty. It saves you from writing if/else logic.

**Q: When should I use `$redirect = true`?**
A: Use `$redirect = true` in `update()` and `delete()` methods that process form submissions. This provides better user experience by redirecting with a message instead of showing a 403 error page.

**Q: Can I customize the error messages?**
A: Currently, the trait uses generic error messages. If you need custom messages, you can check permissions manually using `$this->checkPermission('action')` and provide your own messages.

**Q: What if my controller uses different method names?**
A: If your methods have different names (e.g., `save()` instead of `update()`), just call the appropriate trait method at the start of your method.

---

## Support

For questions or issues with the permission trait:
1. Check the [DebugPermissions](https://dev001.workstation.co.uk/debug-permissions) page to verify user permissions
2. Ensure users logout/login after permission changes
3. Verify that the `user_permissions` and `roles__permissions` tables have the correct granular permission columns

---

## Version History

- **v1.0** (2025-01-14) - Initial implementation with all basic and advanced methods
- Controllers updated: Payments, Receipts
