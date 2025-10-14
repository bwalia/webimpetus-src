<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DebugPermissions extends Controller
{
    public function index()
    {
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
        echo "<strong>ℹ️ How Permissions Work:</strong><br>";
        echo "<ul style='margin-top: 10px; margin-bottom: 0;'>";
        echo "<li><strong>Role Permissions:</strong> If you're assigned to a role, you inherit all permissions from that role</li>";
        echo "<li><strong>Additional Permissions:</strong> Any modules directly assigned to you are ADDED to your role's permissions</li>";
        echo "<li><strong>Final Access:</strong> You get ALL modules from your role + any additional modules = Maximum access</li>";
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

        if (empty($permissions)) {
            echo "<p class='error'>⚠️ NO PERMISSIONS IN SESSION!</p>";
        } else {
            echo "<p class='success'>✓ " . count($permissions) . " permissions loaded</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Link</th><th>Processed (lowercase, no /, - to _)</th></tr>";

            foreach ($permissions as $perm) {
                $processed = strtolower(str_replace(["/", "-"], ["", "_"], $perm['link']));
                echo "<tr>";
                echo "<td>" . ($perm['id'] ?? 'N/A') . "</td>";
                echo "<td>" . ($perm['name'] ?? 'N/A') . "</td>";
                echo "<td>" . ($perm['link'] ?? 'N/A') . "</td>";
                echo "<td><strong>" . $processed . "</strong></td>";
                echo "</tr>";
            }
            echo "</table>";
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

            // Show role permissions breakdown
            if (!empty($user['role']) && isUUID($user['role'])) {
                echo "<h3>Role Permissions Breakdown</h3>";
                echo "<p>You are assigned to a role. Here's the breakdown:</p>";

                // Get role info
                $roleQuery = $db->query("SELECT role_name FROM roles WHERE uuid = ?", [$user['role']]);
                $roleInfo = $roleQuery->getRowArray();

                if ($roleInfo) {
                    echo "<p><strong>Your Role:</strong> <span class='success'>" . $roleInfo['role_name'] . "</span></p>";

                    // Get role permissions
                    $rolePermsQuery = $db->query(
                        "SELECT m.id, m.name, m.link
                         FROM menu m
                         INNER JOIN roles__permissions rp ON m.id = rp.permission_id OR m.uuid = rp.permission_id
                         WHERE rp.role_id = ?",
                        [$user['role']]
                    );
                    $rolePerms = $rolePermsQuery->getResultArray();

                    echo "<h4>Permissions from Role (" . count($rolePerms) . " modules)</h4>";
                    if (!empty($rolePerms)) {
                        echo "<ul>";
                        foreach ($rolePerms as $perm) {
                            echo "<li>" . $perm['name'] . " <small>(" . $perm['link'] . ")</small></li>";
                        }
                        echo "</ul>";
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

        // Show all accessible modules
        echo "<h2>5. All Modules You Can Access</h2>";
        if (!empty($permissions)) {
            echo "<ul>";
            foreach ($permissions as $perm) {
                $processed = strtolower(str_replace(["/", "-"], ["", "_"], $perm['link']));
                echo "<li><a href='" . $perm['link'] . "'>" . $perm['name'] . "</a> (URL segment: <code>$processed</code>)</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='error'>No accessible modules!</p>";
        }

        echo "<hr>";
        echo "<p><a href='/dashboard'>← Back to Dashboard</a> | <a href='/home/logout'>Logout</a></p>";
    }
}
