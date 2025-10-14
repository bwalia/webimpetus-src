<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DebugPermissions extends Controller
{
    public function index()
    {
        // Load global helper for isUUID function
        helper('global');

        $session = \Config\Services::session();

        if (!$session->get('uuid')) {
            return "Please login first to see debug info";
        }

        echo "<h1>Permission Debug Information</h1>";
        echo "<style>
            body { font-family: monospace; margin: 20px; }
            h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 5px; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #667eea; color: white; }
            .error { color: red; font-weight: bold; }
            .success { color: green; font-weight: bold; }
            .warning { color: orange; font-weight: bold; background: #fff3cd; padding: 15px; border-left: 4px solid #ff991f; margin: 20px 0; }
            pre { background: #f4f4f4; padding: 10px; border-radius: 4px; }
        </style>";

        echo "<div class='warning'>";
        echo "<strong>⚠️ IMPORTANT:</strong> Permissions are loaded into your session when you log in.<br>";
        echo "If an administrator changed your permissions, you need to <strong>log out and log back in</strong> to see the changes.<br>";
        echo "<a href='/home/logout' style='color: #667eea; font-weight: bold;'>Click here to logout</a>";
        echo "</div>";

        echo "<div class='alert alert-info' style='background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 20px 0;'>";
        echo "<strong>ℹ️ How Granular Permissions Work:</strong><br>";
        echo "<ul style='margin-top: 10px; margin-bottom: 0;'>";
        echo "<li><strong>Role Permissions:</strong> If you're assigned to a role, you inherit all permissions from that role (with Read/Create/Update/Delete access levels)</li>";
        echo "<li><strong>User Override Permissions:</strong> User-specific permissions in the user_permissions table OVERRIDE role permissions</li>";
        echo "<li><strong>Read Access Default:</strong> Read permission is automatically granted for any module you have access to</li>";
        echo "<li><strong>Action Permissions:</strong> Create, Update, and Delete require explicit permission grants</li>";
        echo "<li><strong>View-Only Mode:</strong> If you only have Read permission, you'll see \"View Only\" badge and no action buttons</li>";
        echo "</ul>";
        echo "</div>";

        // Session info
        echo "<h2>1. Session Information</h2>";
        echo "<table>";
        echo "<tr><th>Key</th><th>Value</th></tr>";
        echo "<tr><td>User ID</td><td>" . $session->get('uuid') . "</td></tr>";
        echo "<tr><td>User Name</td><td>" . $session->get('uname') . "</td></tr>";
        echo "<tr><td>Email</td><td>" . $session->get('uemail') . "</td></tr>";
        echo "<tr><td>Role</td><td>" . $session->get('role') . "</td></tr>";
        echo "<tr><td>Business UUID</td><td>" . $session->get('uuid_business') . "</td></tr>";
        echo "</table>";

        // Permissions from session
        echo "<h2>2. Permissions Loaded in Session</h2>";
        $permissions = $session->get('permissions');
        $permissionMap = $session->get('permission_map');

        if (empty($permissions)) {
            echo "<p class='error'>⚠️ NO PERMISSIONS IN SESSION!</p>";
        } else {
            echo "<p class='success'>✓ " . count($permissions) . " permissions loaded</p>";
            echo "<table>";
            echo "<tr>";
            echo "<th>ID</th><th>Name</th><th>Link</th><th>Processed</th>";
            echo "<th style='background: #2196f3;'>Read</th>";
            echo "<th style='background: #4caf50;'>Create</th>";
            echo "<th style='background: #ff9800;'>Update</th>";
            echo "<th style='background: #f44336;'>Delete</th>";
            echo "</tr>";

            foreach ($permissions as $perm) {
                $processed = strtolower(str_replace(["/", "-"], ["", "_"], $perm['link']));
                $canRead = $perm['can_read'] ?? true; // Default read to true
                $canCreate = $perm['can_create'] ?? false;
                $canUpdate = $perm['can_update'] ?? false;
                $canDelete = $perm['can_delete'] ?? false;

                echo "<tr>";
                echo "<td>" . ($perm['id'] ?? 'N/A') . "</td>";
                echo "<td>" . ($perm['name'] ?? 'N/A') . "</td>";
                echo "<td>" . ($perm['link'] ?? 'N/A') . "</td>";
                echo "<td><strong>" . $processed . "</strong></td>";
                echo "<td style='text-align: center;'>" . ($canRead ? "✓" : "✗") . "</td>";
                echo "<td style='text-align: center;'>" . ($canCreate ? "✓" : "✗") . "</td>";
                echo "<td style='text-align: center;'>" . ($canUpdate ? "✓" : "✗") . "</td>";
                echo "<td style='text-align: center;'>" . ($canDelete ? "✓" : "✗") . "</td>";
                echo "</tr>";
            }
            echo "</table>";

            // Show permission map
            echo "<h3>Permission Map (Quick Lookup)</h3>";
            if (!empty($permissionMap)) {
                echo "<table>";
                echo "<tr><th>Module ID</th><th>Read</th><th>Create</th><th>Update</th><th>Delete</th></tr>";
                foreach ($permissionMap as $moduleId => $perms) {
                    echo "<tr>";
                    echo "<td>" . $moduleId . "</td>";
                    echo "<td style='text-align: center;'>" . ($perms['read'] ? "✓" : "✗") . "</td>";
                    echo "<td style='text-align: center;'>" . ($perms['create'] ? "✓" : "✗") . "</td>";
                    echo "<td style='text-align: center;'>" . ($perms['update'] ? "✓" : "✗") . "</td>";
                    echo "<td style='text-align: center;'>" . ($perms['delete'] ? "✓" : "✗") . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='error'>Permission map not found in session</p>";
            }
        }

        // User data from database
        echo "<h2>3. User Data from Database</h2>";
        $db = \Config\Database::connect();
        $user = $db->table('users')
            ->select('id, name, email, permissions, role')
            ->where('id', $session->get('uuid'))
            ->get()
            ->getRowArray();

        if ($user) {
            echo "<table>";
            echo "<tr><th>Field</th><th>Value</th></tr>";
            foreach ($user as $key => $value) {
                echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
            }
            echo "</table>";

            // Decode permissions
            if (!empty($user['permissions'])) {
                echo "<h3>Decoded Permissions (from database)</h3>";
                $decoded = json_decode($user['permissions'], true);
                echo "<pre>";
                print_r($decoded);
                echo "</pre>";

                // Show which menus these IDs correspond to
                if (is_array($decoded) && !empty($decoded)) {
                    echo "<h3>Menu Items for These IDs (What SHOULD be in session after login)</h3>";
                    $placeholders = implode(',', array_fill(0, count($decoded), '?'));
                    $query = $db->query("SELECT id, name, link FROM menu WHERE id IN (" . implode(',', $decoded) . ")");
                    $menus = $query->getResultArray();

                    echo "<table>";
                    echo "<tr><th>ID</th><th>Name</th><th>Link</th><th>Processed</th></tr>";
                    foreach ($menus as $menu) {
                        $processed = strtolower(str_replace(["/", "-"], ["", "_"], $menu['link']));
                        $inSession = false;
                        if (!empty($permissions)) {
                            $sessionProcessed = array_map(function ($perm) {
                                return strtolower(str_replace(["/", "-"], ["", "_"], $perm['link']));
                            }, $permissions);
                            $inSession = in_array($processed, $sessionProcessed);
                        }
                        $statusIcon = $inSession ? '✓' : '✗';
                        $statusClass = $inSession ? 'success' : 'error';
                        echo "<tr>";
                        echo "<td>" . $menu['id'] . "</td>";
                        echo "<td>" . $menu['name'] . "</td>";
                        echo "<td>" . $menu['link'] . "</td>";
                        echo "<td><strong>" . $processed . "</strong> <span class='$statusClass'>$statusIcon</span></td>";
                        echo "</tr>";
                    }
                    echo "</table>";

                    echo "<p><strong>Note:</strong> Items marked with ✗ are in your database permissions but NOT in your current session. You need to logout and login to refresh.</p>";
                }
            }

            // Show role permissions breakdown with granular permissions
            if (!empty($user['role']) && isUUID($user['role'])) {
                echo "<h3>Role Permissions Breakdown (Granular)</h3>";
                echo "<p>You are assigned to a role. Here's the breakdown with granular permissions:</p>";

                // Get role info
                $roleQuery = $db->query("SELECT role_name FROM roles WHERE uuid = ?", [$user['role']]);
                $roleInfo = $roleQuery->getRowArray();

                if ($roleInfo) {
                    echo "<p><strong>Your Role:</strong> <span class='success'>" . $roleInfo['role_name'] . "</span></p>";

                    // Get role permissions with granular access
                    $rolePermsQuery = $db->query(
                        "SELECT m.id, m.name, m.link, rp.can_read, rp.can_create, rp.can_update, rp.can_delete
                         FROM menu m
                         INNER JOIN roles__permissions rp ON m.id = rp.permission_id OR m.uuid = rp.permission_id
                         WHERE rp.role_id = ?",
                        [$user['role']]
                    );
                    $rolePerms = $rolePermsQuery->getResultArray();

                    echo "<h4>Permissions from Role (" . count($rolePerms) . " modules)</h4>";
                    if (!empty($rolePerms)) {
                        echo "<table>";
                        echo "<tr><th>Module</th><th>Link</th><th>Read</th><th>Create</th><th>Update</th><th>Delete</th></tr>";
                        foreach ($rolePerms as $perm) {
                            echo "<tr>";
                            echo "<td>" . $perm['name'] . "</td>";
                            echo "<td><small>" . $perm['link'] . "</small></td>";
                            echo "<td style='text-align: center;'>" . ($perm['can_read'] ? "✓" : "✗") . "</td>";
                            echo "<td style='text-align: center;'>" . ($perm['can_create'] ? "✓" : "✗") . "</td>";
                            echo "<td style='text-align: center;'>" . ($perm['can_update'] ? "✓" : "✗") . "</td>";
                            echo "<td style='text-align: center;'>" . ($perm['can_delete'] ? "✓" : "✗") . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p class='error'>No permissions defined for this role!</p>";
                    }

                    // Get user's additional permissions
                    $userPermIds = json_decode($user['permissions'], true);
                    if (!empty($userPermIds) && is_array($userPermIds)) {
                        $rolePermIds = array_column($rolePerms, 'id');
                        $additionalPermIds = array_diff($userPermIds, $rolePermIds);

                        if (!empty($additionalPermIds)) {
                            $additionalPermsQuery = $db->query(
                                "SELECT id, name, link FROM menu WHERE id IN (" . implode(',', $additionalPermIds) . ")"
                            );
                            $additionalPerms = $additionalPermsQuery->getResultArray();

                            echo "<h4>Additional Permissions (Beyond Role) (" . count($additionalPerms) . " modules)</h4>";
                            echo "<ul>";
                            foreach ($additionalPerms as $perm) {
                                echo "<li><strong class='success'>" . $perm['name'] . "</strong> <small>(" . $perm['link'] . ")</small> <span class='success'>✓ Extra</span></li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<h4>Additional Permissions</h4>";
                            echo "<p>No additional permissions beyond role.</p>";
                        }
                    }
                }
            }

            // Show user-specific granular permissions from user_permissions table
            echo "<h3>User-Specific Granular Permissions (Override Role)</h3>";
            $userGranularPerms = $db->table('user_permissions up')
                ->select('up.can_read, up.can_create, up.can_update, up.can_delete, m.id, m.name, m.link')
                ->join('menu m', 'm.id = up.menu_id')
                ->where('up.user_id', $user['id'])
                ->get()
                ->getResultArray();

            if (!empty($userGranularPerms)) {
                echo "<p class='success'>✓ " . count($userGranularPerms) . " user-specific permissions found (these override role permissions)</p>";
                echo "<table>";
                echo "<tr><th>Module</th><th>Link</th><th>Read</th><th>Create</th><th>Update</th><th>Delete</th></tr>";
                foreach ($userGranularPerms as $perm) {
                    echo "<tr>";
                    echo "<td><strong>" . $perm['name'] . "</strong></td>";
                    echo "<td><small>" . $perm['link'] . "</small></td>";
                    echo "<td style='text-align: center;'>" . ($perm['can_read'] ? "✓" : "✗") . "</td>";
                    echo "<td style='text-align: center;'>" . ($perm['can_create'] ? "✓" : "✗") . "</td>";
                    echo "<td style='text-align: center;'>" . ($perm['can_update'] ? "✓" : "✗") . "</td>";
                    echo "<td style='text-align: center;'>" . ($perm['can_delete'] ? "✓" : "✗") . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No user-specific granular permissions set. Using role defaults.</p>";
            }
        } else {
            echo "<p class='error'>User not found in database!</p>";
        }

        // Test access check
        echo "<h2>4. Access Check Test</h2>";
        echo "<p>Testing if you can access <strong>/email_campaigns</strong></p>";

        $table = "email_campaigns";
        if (!empty($permissions)) {
            $user_permissions = array_map(function ($perm) {
                return strtolower(str_replace(["/", "-"], ["", "_"], $perm['link']));
            }, $permissions);

            echo "<p>Looking for: <strong>$table</strong></p>";
            echo "<p>In permissions array: <pre>" . implode(', ', $user_permissions) . "</pre></p>";

            if (in_array($table, $user_permissions)) {
                echo "<p class='success'>✓ ACCESS GRANTED to /$table</p>";
            } else {
                echo "<p class='error'>✗ ACCESS DENIED to /$table</p>";
                echo "<p>The module '$table' is NOT in your permissions list.</p>";
            }
        }

        // Show all accessible modules with granular permissions
        echo "<h2>5. All Modules You Can Access (With Actions)</h2>";
        if (!empty($permissions)) {
            echo "<table>";
            echo "<tr><th>Module</th><th>URL Segment</th><th>Read</th><th>Create</th><th>Update</th><th>Delete</th></tr>";
            foreach ($permissions as $perm) {
                $processed = strtolower(str_replace(["/", "-"], ["", "_"], $perm['link']));
                $canRead = $perm['can_read'] ?? true;
                $canCreate = $perm['can_create'] ?? false;
                $canUpdate = $perm['can_update'] ?? false;
                $canDelete = $perm['can_delete'] ?? false;

                echo "<tr>";
                echo "<td><a href='" . $perm['link'] . "'>" . $perm['name'] . "</a></td>";
                echo "<td><code>$processed</code></td>";
                echo "<td style='text-align: center; background: " . ($canRead ? "#e8f5e9" : "#ffebee") . ";'>" . ($canRead ? "✓" : "✗") . "</td>";
                echo "<td style='text-align: center; background: " . ($canCreate ? "#e8f5e9" : "#ffebee") . ";'>" . ($canCreate ? "✓" : "✗") . "</td>";
                echo "<td style='text-align: center; background: " . ($canUpdate ? "#e8f5e9" : "#ffebee") . ";'>" . ($canUpdate ? "✓" : "✗") . "</td>";
                echo "<td style='text-align: center; background: " . ($canDelete ? "#e8f5e9" : "#ffebee") . ";'>" . ($canDelete ? "✓" : "✗") . "</td>";
                echo "</tr>";
            }
            echo "</table>";

            // Add a summary
            $readOnly = array_filter($permissions, function($p) {
                return ($p['can_read'] ?? true) && !($p['can_create'] ?? false) && !($p['can_update'] ?? false) && !($p['can_delete'] ?? false);
            });
            $fullAccess = array_filter($permissions, function($p) {
                return ($p['can_read'] ?? true) && ($p['can_create'] ?? false) && ($p['can_update'] ?? false) && ($p['can_delete'] ?? false);
            });

            echo "<p><strong>Summary:</strong></p>";
            echo "<ul>";
            echo "<li>Total modules: " . count($permissions) . "</li>";
            echo "<li>View-only access: " . count($readOnly) . " modules</li>";
            echo "<li>Full access (CRUD): " . count($fullAccess) . " modules</li>";
            echo "</ul>";
        } else {
            echo "<p class='error'>No accessible modules!</p>";
        }

        echo "<hr>";
        echo "<p><a href='/dashboard'>← Back to Dashboard</a> | <a href='/home/logout'>Logout</a></p>";
    }
}
