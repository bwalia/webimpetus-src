# Granular Permissions System Implementation Guide

## Overview
This document describes the implementation of granular (read, create, update, delete) permissions for the entire application at both the role and user level.

## Database Changes

### 1. Run the SQL Migration
Execute the following SQL file to add granular permission support:
```bash
mysql -u [username] -p [database] < SQLs/add_granular_permissions.sql
```

This creates:
- **roles__permissions table**: Adds `can_read`, `can_create`, `can_update`, `can_delete` columns
- **user_permissions table**: New table for user-specific granular permissions

### 2. Table Structures

####roles__permissions (Updated)
```sql
- id
- uuid
- role_id
- permission_id
- can_read (TINYINT, default 1)
- can_create (TINYINT, default 0)
- can_update (TINYINT, default 0)
- can_delete (TINYINT, default 0)
```

#### user_permissions (New)
```sql
- id
- uuid
- user_id
- menu_id
- can_read (TINYINT, default 1)
- can_create (TINYINT, default 0)
- can_update (TINYINT, default 0)
- can_delete (TINYINT, default 0)
- created_at
- updated_at
```

## Code Changes Completed

### 1. Home Controller (Login Permission Loading)
**File**: `ci4/app/Controllers/Home.php` (lines 129-214)

**What it does**:
- Loads role-based granular permissions
- Loads user-specific granular permissions (override role)
- Handles legacy permissions from users.permissions JSON field
- Stores both `permissions` and `permission_map` in session

**Session Data Structure**:
```php
$_SESSION['permissions'] = [
    [
        'id' => 1,
        'name' => 'Dashboard',
        'link' => '/dashboard',
        'can_read' => true,
        'can_create' => false,
        'can_update' => false,
        'can_delete' => false
    ],
    // ... more modules
];

$_SESSION['permission_map'] = [
    1 => ['read' => true, 'create' => false, 'update' => false, 'delete' => false],
    // ... keyed by module ID
];
```

## Code Changes Needed

### 2. CommonController - Permission Checking
**File**: `ci4/app/Controllers/Core/CommonController.php`

**Current**: Only checks if user has access to module
**Needed**: Check granular permissions for specific actions

```php
// Add this helper function to CommonController
protected function checkPermission($action = 'read')
{
    $permissionMap = $this->session->get('permission_map');
    $tableName = $this->table;

    // Get menu ID for this module
    $menu = $this->db->table('menu')
        ->where('link', '/' . $tableName)
        ->orWhere('link LIKE', '%' . $tableName . '%')
        ->get()
        ->getRow();

    if (!$menu) {
        return false;
    }

    $menuId = $menu->id;

    if (!isset($permissionMap[$menuId])) {
        return false; // No permission at all
    }

    return $permissionMap[$menuId][$action] ?? false;
}

// Update index() method
public function index()
{
    if (!$this->checkPermission('read')) {
        echo view("errors/html/error_403");
        die;
    }

    // ... rest of code
    $data['can_create'] = $this->checkPermission('create');
    $data['can_update'] = $this->checkPermission('update');
    $data['can_delete'] = $this->checkPermission('delete');

    return view($viewPath, $data);
}

// Update edit() method
public function edit($uuid = 0)
{
    if ($uuid && !$this->checkPermission('update')) {
        echo view("errors/html/error_403");
        die;
    }

    if (!$uuid && !$this->checkPermission('create')) {
        echo view("errors/html/error_403");
        die;
    }

    // ... rest of code
}

// Update delete() method
public function delete($uuid)
{
    if (!$this->checkPermission('delete')) {
        echo view("errors/html/error_403");
        die;
    }

    // ... rest of code
}
```

### 3. Common List View - Hide/Show Buttons
**File**: `ci4/app/Views/common/list.php` and module-specific list views

```php
<!-- Add New Button - Show only if can_create -->
<?php if ($can_create ?? false): ?>
    <a href="/<?= $tableName ?>/edit" class="btn btn-primary">
        <i class="fa fa-plus"></i> Add New
    </a>
<?php endif; ?>

<!-- Edit Button in table - Show only if can_update -->
<?php if ($can_update ?? false): ?>
    <a href="/<?= $tableName ?>/edit/<?= $row['uuid'] ?>">
        <i class="fa fa-edit"></i> Edit
    </a>
<?php endif; ?>

<!-- Delete Button - Show only if can_delete -->
<?php if ($can_delete ?? false): ?>
    <a href="/<?= $tableName ?>/delete/<?= $row['uuid'] ?>"
       onclick="return confirm('Are you sure?')">
        <i class="fa fa-trash"></i> Delete
    </a>
<?php endif; ?>

<!-- View Only mode -->
<?php if ($can_read && !$can_update): ?>
    <span class="badge badge-info">View Only</span>
<?php endif; ?>
```

### 4. Users Edit Form - Granular Permission Matrix UI
**File**: `ci4/app/Views/users/edit.php`

Add after the current permission section:

```php
<div class="card mt-4">
    <div class="card-header bg-success text-white">
        <h5><i class="fa fa-key"></i> Granular Permissions (Read/Create/Update/Delete)</h5>
    </div>
    <div class="card-body">
        <p class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            Fine-tune what this user can do within each module. These override role permissions.
        </p>

        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Module</th>
                    <th class="text-center"><i class="fa fa-eye"></i> Read</th>
                    <th class="text-center"><i class="fa fa-plus"></i> Create</th>
                    <th class="text-center"><i class="fa fa-edit"></i> Update</th>
                    <th class="text-center"><i class="fa fa-trash"></i> Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menu as $module): ?>
                <tr>
                    <td><strong><?= $module['name'] ?></strong></td>
                    <td class="text-center">
                        <input type="checkbox" name="perms[<?= $module['id'] ?>][read]" value="1"
                               <?= (isset($user_perms[$module['id']]['can_read']) && $user_perms[$module['id']]['can_read']) ? 'checked' : '' ?>>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="perms[<?= $module['id'] ?>][create]" value="1"
                               <?= (isset($user_perms[$module['id']]['can_create']) && $user_perms[$module['id']]['can_create']) ? 'checked' : '' ?>>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="perms[<?= $module['id'] ?>][update]" value="1"
                               <?= (isset($user_perms[$module['id']]['can_update']) && $user_perms[$module['id']]['can_update']) ? 'checked' : '' ?>>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" name="perms[<?= $module['id'] ?>][delete]" value="1"
                               <?= (isset($user_perms[$module['id']]['can_delete']) && $user_perms[$module['id']]['can_delete']) ? 'checked' : '' ?>>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-3">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkAllPerms('read')">
                <i class="fa fa-check-square"></i> Check All Read
            </button>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="checkAllPerms('create')">
                <i class="fa fa-check-square"></i> Check All Create
            </button>
            <button type="button" class="btn btn-sm btn-outline-info" onclick="checkAllPerms('update')">
                <i class="fa fa-check-square"></i> Check All Update
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="checkAllPerms('delete')">
                <i class="fa fa-check-square"></i> Check All Delete
            </button>
        </div>
    </div>
</div>

<script>
function checkAllPerms(type) {
    document.querySelectorAll(`input[name*="[${type}]"]`).forEach(cb => cb.checked = true);
}
</script>
```

### 5. Users Controller - Save Granular Permissions
**File**: `ci4/app/Controllers/Users.php` - update() method

```php
public function update()
{
    // ... existing code ...

    // Save granular permissions
    $perms = $this->request->getPost('perms');
    $userId = $this->request->getPost('id');

    if ($perms && $userId) {
        // Delete existing user permissions
        $this->db->table('user_permissions')->where('user_id', $userId)->delete();

        // Insert new permissions
        foreach ($perms as $menuId => $actions) {
            $data = [
                'uuid' => \App\Libraries\UUID::v4(),
                'user_id' => $userId,
                'menu_id' => $menuId,
                'can_read' => isset($actions['read']) ? 1 : 0,
                'can_create' => isset($actions['create']) ? 1 : 0,
                'can_update' => isset($actions['update']) ? 1 : 0,
                'can_delete' => isset($actions['delete']) ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Only save if at least one permission is granted
            if ($data['can_read'] || $data['can_create'] || $data['can_update'] || $data['can_delete']) {
                $this->db->table('user_permissions')->insert($data);
            }
        }
    }

    // ... existing code ...
}
```

### 6. Helper Function for Views
**File**: `ci4/app/Helpers/permission_helper.php` (create new)

```php
<?php

if (!function_exists('can')) {
    function can($action, $moduleId = null)
    {
        $session = \Config\Services::session();
        $permissionMap = $session->get('permission_map');

        if (!$moduleId) {
            // Get current module ID from URI
            $uri = service('uri');
            $tableName = $uri->getSegment(1);
            $db = \Config\Database::connect();
            $menu = $db->table('menu')
                ->where('link', '/' . $tableName)
                ->orWhere('link LIKE', '%' . $tableName . '%')
                ->get()
                ->getRow();

            if (!$menu) {
                return false;
            }
            $moduleId = $menu->id;
        }

        return $permissionMap[$moduleId][$action] ?? false;
    }
}

if (!function_exists('canRead')) {
    function canRead($moduleId = null) {
        return can('read', $moduleId);
    }
}

if (!function_exists('canCreate')) {
    function canCreate($moduleId = null) {
        return can('create', $moduleId);
    }
}

if (!function_exists('canUpdate')) {
    function canUpdate($moduleId = null) {
        return can('update', $moduleId);
    }
}

if (!function_exists('canDelete')) {
    function canDelete($moduleId = null) {
        return can('delete', $moduleId);
    }
}
```

Load in views:
```php
<?php helper('permission'); ?>

<?php if (canCreate()): ?>
    <button>Add New</button>
<?php endif; ?>
```

## Testing Checklist

1. [ ] Run SQL migration
2. [ ] Login as user with role
3. [ ] Verify permissions load correctly
4. [ ] Test read-only access (can view but not edit/delete)
5. [ ] Test partial permissions (can create but not delete)
6. [ ] Test user-specific permissions override role permissions
7. [ ] Test that buttons/links hide when no permission
8. [ ] Test that direct URL access is blocked (403)
9. [ ] Test role edit form with granular permission matrix
10. [ ] Test user edit form with granular permission matrix

## Migration Path

1. **Phase 1**: Database migration (Complete)
2. **Phase 2**: Home controller update (Complete)
3. **Phase 3**: CommonController permission checks (TODO)
4. **Phase 4**: Update all views to respect permissions (TODO)
5. **Phase 5**: Add granular permission UI to users/roles forms (TODO)
6. **Phase 6**: System-wide testing (TODO)

## Notes

- Super admin (ID=1) bypasses all permission checks
- Legacy permissions from `users.permissions` JSON are supported with full permissions
- User-specific permissions ALWAYS override role permissions
- If a module is not in the permission map, access is denied
