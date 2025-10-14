<?php

/**
 * Permission Helper Functions
 *
 * These functions provide easy access to granular permissions from views
 */

if (!function_exists('can')) {
    /**
     * Check if user has a specific permission for a module
     *
     * @param string $action The action to check: 'read', 'create', 'update', 'delete'
     * @param int|null $moduleId The module ID (optional, will detect from current URI if not provided)
     * @return bool True if user has permission, false otherwise
     */
    function can($action, $moduleId = null)
    {
        // Super admin bypass (user ID = 1)
        if (session('id') == 1) {
            return true;
        }

        $session = \Config\Services::session();
        $permissionMap = $session->get('permission_map');

        if (empty($permissionMap)) {
            return false;
        }

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
                // Try with hyphen instead of underscore
                $tableNameWithHyphen = str_replace('_', '-', $tableName);
                $menu = $db->table('menu')
                    ->where('link', '/' . $tableNameWithHyphen)
                    ->orWhere('link LIKE', '%' . $tableNameWithHyphen . '%')
                    ->get()
                    ->getRow();
            }

            if (!$menu) {
                return false;
            }
            $moduleId = $menu->id;
        }

        if (!isset($permissionMap[$moduleId])) {
            return false; // No permission entry for this module
        }

        // Default: read permission is always granted if module access exists
        // Other actions (create, update, delete) require explicit permission
        if ($action === 'read') {
            return true; // Default read access for any accessible module
        }

        return $permissionMap[$moduleId][$action] ?? false;
    }
}

if (!function_exists('canRead')) {
    /**
     * Check if user can read/view a module
     *
     * @param int|null $moduleId The module ID (optional)
     * @return bool
     */
    function canRead($moduleId = null)
    {
        return can('read', $moduleId);
    }
}

if (!function_exists('canCreate')) {
    /**
     * Check if user can create new records in a module
     *
     * @param int|null $moduleId The module ID (optional)
     * @return bool
     */
    function canCreate($moduleId = null)
    {
        return can('create', $moduleId);
    }
}

if (!function_exists('canUpdate')) {
    /**
     * Check if user can update/edit records in a module
     *
     * @param int|null $moduleId The module ID (optional)
     * @return bool
     */
    function canUpdate($moduleId = null)
    {
        return can('update', $moduleId);
    }
}

if (!function_exists('canDelete')) {
    /**
     * Check if user can delete records in a module
     *
     * @param int|null $moduleId The module ID (optional)
     * @return bool
     */
    function canDelete($moduleId = null)
    {
        return can('delete', $moduleId);
    }
}

if (!function_exists('isViewOnly')) {
    /**
     * Check if user has view-only access (can read but cannot create/update/delete)
     *
     * @param int|null $moduleId The module ID (optional)
     * @return bool
     */
    function isViewOnly($moduleId = null)
    {
        return canRead($moduleId) && !canCreate($moduleId) && !canUpdate($moduleId) && !canDelete($moduleId);
    }
}

if (!function_exists('hasFullAccess')) {
    /**
     * Check if user has full access (can read, create, update, and delete)
     *
     * @param int|null $moduleId The module ID (optional)
     * @return bool
     */
    function hasFullAccess($moduleId = null)
    {
        return canRead($moduleId) && canCreate($moduleId) && canUpdate($moduleId) && canDelete($moduleId);
    }
}

if (!function_exists('getModulePermissions')) {
    /**
     * Get all permissions for a module
     *
     * @param int|null $moduleId The module ID (optional)
     * @return array Array with keys: read, create, update, delete
     */
    function getModulePermissions($moduleId = null)
    {
        return [
            'read' => canRead($moduleId),
            'create' => canCreate($moduleId),
            'update' => canUpdate($moduleId),
            'delete' => canDelete($moduleId),
        ];
    }
}
