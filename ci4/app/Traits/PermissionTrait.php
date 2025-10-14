<?php

namespace App\Traits;

/**
 * Permission Trait
 *
 * Provides easy-to-use permission checking methods for controllers
 * that override CommonController methods and need granular permission checks.
 *
 * Usage:
 * 1. Add to your controller: use \App\Traits\PermissionTrait;
 * 2. Call methods at the start of your controller methods:
 *    - $this->requireReadPermission();
 *    - $this->requireCreatePermission();
 *    - $this->requireUpdatePermission();
 *    - $this->requireDeletePermission();
 *    - $this->requireEditPermission($uuid); // Auto-detects create vs update
 *
 * Example:
 *
 * public function update()
 * {
 *     $uuid = $this->request->getPost('uuid');
 *     $this->requireEditPermission($uuid); // Checks create or update based on UUID
 *
 *     // Your update logic here...
 * }
 */
trait PermissionTrait
{
    /**
     * Require read permission - shows 403 error if user doesn't have permission
     * Use at the start of index() or view methods
     *
     * @return void Dies with 403 error if no permission
     */
    protected function requireReadPermission()
    {
        if (!$this->checkPermission('read')) {
            echo view("errors/html/error_403");
            die;
        }
    }

    /**
     * Require create permission - shows 403 error or redirects with message
     * Use at the start of methods that create new records
     *
     * @param bool $redirect Whether to redirect with message instead of showing 403
     * @return void Dies or redirects if no permission
     */
    protected function requireCreatePermission($redirect = false)
    {
        if (!$this->checkPermission('create')) {
            if ($redirect && isset($this->table)) {
                session()->setFlashdata('message', 'You do not have permission to create records in this module!');
                session()->setFlashdata('alert-class', 'alert-danger');
                return redirect()->to('/' . $this->table);
            }
            echo view("errors/html/error_403");
            die;
        }
    }

    /**
     * Require update permission - shows 403 error or redirects with message
     * Use at the start of methods that update existing records
     *
     * @param bool $redirect Whether to redirect with message instead of showing 403
     * @return void Dies or redirects if no permission
     */
    protected function requireUpdatePermission($redirect = false)
    {
        if (!$this->checkPermission('update')) {
            if ($redirect && isset($this->table)) {
                session()->setFlashdata('message', 'You do not have permission to update records in this module!');
                session()->setFlashdata('alert-class', 'alert-danger');
                return redirect()->to('/' . $this->table);
            }
            echo view("errors/html/error_403");
            die;
        }
    }

    /**
     * Require delete permission - shows 403 error or redirects with message
     * Use at the start of delete methods
     *
     * @param bool $redirect Whether to redirect with message instead of showing 403
     * @return void Dies or redirects if no permission
     */
    protected function requireDeletePermission($redirect = false)
    {
        if (!$this->checkPermission('delete')) {
            if ($redirect && isset($this->table)) {
                session()->setFlashdata('message', 'You do not have permission to delete records in this module!');
                session()->setFlashdata('alert-class', 'alert-danger');
                return redirect()->to('/' . $this->table);
            }
            echo view("errors/html/error_403");
            die;
        }
    }

    /**
     * Require edit permission - automatically checks create or update based on ID/UUID
     * Use at the start of edit() or update() methods
     *
     * @param mixed $id The record ID or UUID (empty = create, not empty = update)
     * @param bool $redirect Whether to redirect with message instead of showing 403
     * @return void Dies or redirects if no permission
     */
    protected function requireEditPermission($id = null, $redirect = false)
    {
        if (empty($id)) {
            $this->requireCreatePermission($redirect);
        } else {
            $this->requireUpdatePermission($redirect);
        }
    }

    /**
     * Pass granular permissions to view data
     * Use in index() methods to pass permission flags to views
     *
     * @param array &$data Reference to the data array to add permissions to
     * @return void
     */
    protected function addPermissionsToView(&$data)
    {
        $data['can_create'] = $this->checkPermission('create');
        $data['can_update'] = $this->checkPermission('update');
        $data['can_delete'] = $this->checkPermission('delete');
        $data['can_read'] = $this->checkPermission('read');
    }

    /**
     * Get all granular permissions for current module
     * Returns array with read, create, update, delete booleans
     *
     * @return array ['read' => bool, 'create' => bool, 'update' => bool, 'delete' => bool]
     */
    protected function getPermissions()
    {
        return [
            'read' => $this->checkPermission('read'),
            'create' => $this->checkPermission('create'),
            'update' => $this->checkPermission('update'),
            'delete' => $this->checkPermission('delete'),
        ];
    }

    /**
     * Check if user has view-only access (can read but not modify)
     *
     * @return bool
     */
    protected function isViewOnly()
    {
        return $this->checkPermission('read')
            && !$this->checkPermission('create')
            && !$this->checkPermission('update')
            && !$this->checkPermission('delete');
    }

    /**
     * Check if user has full access (all CRUD permissions)
     *
     * @return bool
     */
    protected function hasFullAccess()
    {
        return $this->checkPermission('read')
            && $this->checkPermission('create')
            && $this->checkPermission('update')
            && $this->checkPermission('delete');
    }

    /**
     * Require any of the specified permissions (OR logic)
     * Useful when multiple permission types should allow access
     *
     * @param array $permissions Array of permission types: ['create', 'update']
     * @param bool $redirect Whether to redirect with message instead of showing 403
     * @return void Dies or redirects if no permission
     */
    protected function requireAnyPermission(array $permissions, $redirect = false)
    {
        foreach ($permissions as $permission) {
            if ($this->checkPermission($permission)) {
                return; // Has at least one permission, allow access
            }
        }

        // No permissions found
        if ($redirect && isset($this->table)) {
            session()->setFlashdata('message', 'You do not have permission to access this feature!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }
        echo view("errors/html/error_403");
        die;
    }

    /**
     * Require all of the specified permissions (AND logic)
     * Useful when multiple permission types are required for access
     *
     * @param array $permissions Array of permission types: ['read', 'update']
     * @param bool $redirect Whether to redirect with message instead of showing 403
     * @return void Dies or redirects if missing any permission
     */
    protected function requireAllPermissions(array $permissions, $redirect = false)
    {
        foreach ($permissions as $permission) {
            if (!$this->checkPermission($permission)) {
                // Missing at least one permission
                if ($redirect && isset($this->table)) {
                    session()->setFlashdata('message', 'You do not have sufficient permissions to access this feature!');
                    session()->setFlashdata('alert-class', 'alert-danger');
                    return redirect()->to('/' . $this->table);
                }
                echo view("errors/html/error_403");
                die;
            }
        }
    }
}
