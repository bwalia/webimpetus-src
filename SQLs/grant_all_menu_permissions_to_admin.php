<?php
/**
 * Script: Grant All Menu Permissions to Admin User
 * Description: This PHP script assigns all menu permissions to admin@admin.com
 * Author: Claude Code
 * Date: 2025-10-11
 *
 * Usage (from host machine):
 *   php SQLs/grant_all_menu_permissions_to_admin.php
 *
 * This script connects to the database via exposed Docker port (localhost:3309)
 */

// Database connection parameters
// Connects via exposed Docker port
$config = [
    'hostname' => '127.0.0.1',
    'port' => 3309,
    'username' => 'workerra-ci-dev',
    'password' => 'CHANGE_ME',
    'database' => 'myworkstation_dev',
];

echo "=============================================================================\n";
echo "Grant All Menu Permissions to Admin User\n";
echo "=============================================================================\n\n";

try {
    // Create database connection
    $mysqli = new mysqli(
        $config['hostname'],
        $config['username'],
        $config['password'],
        $config['database'],
        $config['port']
    );

    // Check connection
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }

    echo "✓ Connected to database: {$config['database']}\n\n";

    // Step 1: Get admin user details
    echo "Step 1: Fetching admin user details...\n";
    $adminQuery = "SELECT id, uuid, email, name, role, permissions FROM users WHERE email = 'admin@admin.com'";
    $adminResult = $mysqli->query($adminQuery);

    if (!$adminResult || $adminResult->num_rows === 0) {
        throw new Exception("Admin user not found!");
    }

    $admin = $adminResult->fetch_assoc();
    echo "  Found user: {$admin['name']} ({$admin['email']})\n";
    echo "  UUID: {$admin['uuid']}\n";
    echo "  Current permissions count: " . (empty($admin['permissions']) ? 0 : count(json_decode($admin['permissions']))) . "\n\n";

    // Step 2: Get all menu items
    echo "Step 2: Fetching all menu items...\n";
    $menuQuery = "SELECT id, name, link FROM menu ORDER BY id";
    $menuResult = $mysqli->query($menuQuery);

    if (!$menuResult) {
        throw new Exception("Failed to fetch menu items: " . $mysqli->error);
    }

    $menuIds = [];
    $menuItems = [];
    while ($row = $menuResult->fetch_assoc()) {
        $menuIds[] = (string)$row['id'];
        $menuItems[] = $row;
    }

    echo "  Found " . count($menuIds) . " menu items\n\n";

    // Step 3: Update admin user with all menu permissions
    echo "Step 3: Updating admin user permissions...\n";
    $permissionsJson = json_encode($menuIds);
    $updateQuery = "UPDATE users SET permissions = ? WHERE uuid = ?";
    $stmt = $mysqli->prepare($updateQuery);

    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $mysqli->error);
    }

    $stmt->bind_param('ss', $permissionsJson, $admin['uuid']);

    if (!$stmt->execute()) {
        throw new Exception("Failed to update permissions: " . $stmt->error);
    }

    $affectedRows = $stmt->affected_rows;
    echo "  ✓ Updated {$affectedRows} user record(s)\n\n";

    // Step 4: Verify the update
    echo "Step 4: Verifying permissions...\n";
    $verifyQuery = "SELECT permissions FROM users WHERE uuid = ?";
    $verifyStmt = $mysqli->prepare($verifyQuery);
    $verifyStmt->bind_param('s', $admin['uuid']);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();
    $updatedUser = $verifyResult->fetch_assoc();

    $updatedPermissions = json_decode($updatedUser['permissions'], true);
    echo "  New permissions count: " . count($updatedPermissions) . "\n";
    echo "  Permissions: " . implode(', ', $updatedPermissions) . "\n\n";

    // Step 5: Display all menu items for reference
    echo "Step 5: Menu Items with Permissions Granted:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-5s %-40s %-30s\n", "ID", "Name", "Link");
    echo str_repeat("-", 80) . "\n";

    foreach ($menuItems as $item) {
        printf("%-5s %-40s %-30s\n", $item['id'], substr($item['name'], 0, 40), substr($item['link'], 0, 30));
    }

    echo str_repeat("-", 80) . "\n\n";

    // Summary
    echo "=============================================================================\n";
    echo "✓ SUCCESS\n";
    echo "=============================================================================\n";
    echo "Admin user 'admin@admin.com' now has access to all " . count($menuIds) . " menu items.\n";
    echo "The user can now access all modules in the system.\n\n";

    // Close connections
    $stmt->close();
    $verifyStmt->close();
    $mysqli->close();

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "Script completed successfully!\n";
exit(0);
