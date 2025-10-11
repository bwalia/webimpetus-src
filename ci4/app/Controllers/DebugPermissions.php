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
            pre { background: #f4f4f4; padding: 10px; border-radius: 4px; }
        </style>";

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
            echo "<tr><th>ID</th><th>Name</th><th>Link</th><th>Processed (lowercase, no /)</th></tr>";

            foreach ($permissions as $perm) {
                $processed = strtolower(str_replace("/", "", $perm['link']));
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
                echo "<h3>Decoded Permissions</h3>";
                $decoded = json_decode($user['permissions'], true);
                echo "<pre>";
                print_r($decoded);
                echo "</pre>";

                // Show which menus these IDs correspond to
                if (is_array($decoded) && !empty($decoded)) {
                    echo "<h3>Menu Items for These IDs</h3>";
                    $placeholders = implode(',', array_fill(0, count($decoded), '?'));
                    $query = $db->query("SELECT id, name, link FROM menu WHERE id IN (" . implode(',', $decoded) . ")");
                    $menus = $query->getResultArray();

                    echo "<table>";
                    echo "<tr><th>ID</th><th>Name</th><th>Link</th></tr>";
                    foreach ($menus as $menu) {
                        echo "<tr><td>" . $menu['id'] . "</td><td>" . $menu['name'] . "</td><td>" . $menu['link'] . "</td></tr>";
                    }
                    echo "</table>";
                }
            }
        } else {
            echo "<p class='error'>User not found in database!</p>";
        }

        // Test access check
        echo "<h2>4. Access Check Test</h2>";
        echo "<p>Testing if you can access <strong>/deployments</strong></p>";

        $table = "deployments";
        if (!empty($permissions)) {
            $user_permissions = array_map(function ($perm) {
                return strtolower(str_replace("/", "", $perm['link']));
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
                $processed = strtolower(str_replace("/", "", $perm['link']));
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
