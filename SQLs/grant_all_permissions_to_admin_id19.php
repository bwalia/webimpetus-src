<?php
/**
 * Script: Grant All Menu Permissions to Admin User (ID 19)
 * Description: This PHP script assigns all menu permissions to admin@admin.com (id 19)
 *              with full granular permissions (read, create, update, delete)
 * Author: Claude Code
 * Date: 2025-10-20
 *
 * Usage (from host machine):
 *   php SQLs/grant_all_permissions_to_admin_id19.php
 *
 * Or via Docker:
 *   docker exec webimpetus-dev php /var/www/html/SQLs/grant_all_permissions_to_admin_id19.php
 */

// Database connection parameters
// Connects via exposed Docker port
$config = [
    'hostname' => '127.0.0.1',
    'port' => 3309,
    'username' => 'wsl_dev',
    'password' => 'CHANGE_ME',
    'database' => 'myworkstation_dev',
];

echo "=============================================================================\n";
echo "Grant All Menu Permissions to Admin User (ID 19)\n";
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

    // ========================================================================
    // STEP 1: Get admin user details
    // ========================================================================
    echo "Step 1: Fetching admin user details...\n";
    $adminQuery = "SELECT id, uuid, email, name, role, permissions FROM users WHERE id = 19 AND email = 'admin@admin.com'";
    $adminResult = $mysqli->query($adminQuery);

    if (!$adminResult || $adminResult->num_rows === 0) {
        throw new Exception("Admin user (ID 19, admin@admin.com) not found!");
    }

    $admin = $adminResult->fetch_assoc();
    echo "  Found user:\n";
    echo "    - ID: {$admin['id']}\n";
    echo "    - Name: {$admin['name']}\n";
    echo "    - Email: {$admin['email']}\n";
    echo "    - UUID: {$admin['uuid']}\n";
    echo "    - Role: {$admin['role']}\n";
    $currentPermCount = empty($admin['permissions']) ? 0 : count(json_decode($admin['permissions'], true));
    echo "    - Current legacy permissions count: {$currentPermCount}\n\n";

    // ========================================================================
    // STEP 2: Get all menu items
    // ========================================================================
    echo "Step 2: Fetching all menu items...\n";
    $menuQuery = "SELECT id, name, link, icon FROM menu ORDER BY id";
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

    // ========================================================================
    // STEP 3: Update legacy permissions field
    // ========================================================================
    echo "Step 3: Updating users.permissions field...\n";
    $permissionsJson = json_encode($menuIds);
    $updateQuery = "UPDATE users SET permissions = ? WHERE id = 19";
    $stmt = $mysqli->prepare($updateQuery);

    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $mysqli->error);
    }

    $stmt->bind_param('s', $permissionsJson);

    if (!$stmt->execute()) {
        throw new Exception("Failed to update permissions: " . $stmt->error);
    }

    echo "  ✓ Updated permissions field with " . count($menuIds) . " menu IDs\n\n";

    // ========================================================================
    // STEP 4: Clear existing granular permissions
    // ========================================================================
    echo "Step 4: Clearing existing granular permissions...\n";
    $deleteQuery = "DELETE FROM user_permissions WHERE user_id = 19";
    if (!$mysqli->query($deleteQuery)) {
        throw new Exception("Failed to delete existing permissions: " . $mysqli->error);
    }
    echo "  ✓ Cleared existing granular permissions\n\n";

    // ========================================================================
    // STEP 5: Insert granular permissions with full CRUD
    // ========================================================================
    echo "Step 5: Inserting granular permissions (read, create, update, delete)...\n";
    
    $insertCount = 0;
    $insertStmt = $mysqli->prepare(
        "INSERT INTO user_permissions (uuid, user_id, menu_id, can_read, can_create, can_update, can_delete, created_at, updated_at) 
         VALUES (UUID(), 19, ?, 1, 1, 1, 1, NOW(), NOW())"
    );

    if (!$insertStmt) {
        throw new Exception("Failed to prepare insert statement: " . $mysqli->error);
    }

    foreach ($menuIds as $menuId) {
        $insertStmt->bind_param('i', $menuId);
        if ($insertStmt->execute()) {
            $insertCount++;
        } else {
            echo "  Warning: Failed to insert permission for menu ID {$menuId}: " . $insertStmt->error . "\n";
        }
    }

    echo "  ✓ Inserted {$insertCount} granular permission records\n\n";

    // ========================================================================
    // VERIFICATION
    // ========================================================================
    echo "=============================================================================\n";
    echo "VERIFICATION\n";
    echo "=============================================================================\n\n";

    // Verify legacy permissions
    echo "Legacy Permissions (users.permissions field):\n";
    echo str_repeat("-", 80) . "\n";
    $verifyQuery = "SELECT id, email, JSON_LENGTH(permissions) as perm_count FROM users WHERE id = 19";
    $verifyResult = $mysqli->query($verifyQuery);
    $verifyUser = $verifyResult->fetch_assoc();
    echo "  User ID: {$verifyUser['id']}\n";
    echo "  Email: {$verifyUser['email']}\n";
    echo "  Total Menu Permissions: {$verifyUser['perm_count']}\n\n";

    // Verify granular permissions
    echo "Granular Permissions (user_permissions table):\n";
    echo str_repeat("-", 80) . "\n";
    $granularQuery = "SELECT 
        COUNT(*) as total,
        SUM(can_read) as can_read,
        SUM(can_create) as can_create,
        SUM(can_update) as can_update,
        SUM(can_delete) as can_delete
        FROM user_permissions WHERE user_id = 19";
    $granularResult = $mysqli->query($granularQuery);
    $granularStats = $granularResult->fetch_assoc();
    echo "  Total Permissions: {$granularStats['total']}\n";
    echo "  Can Read: {$granularStats['can_read']}\n";
    echo "  Can Create: {$granularStats['can_create']}\n";
    echo "  Can Update: {$granularStats['can_update']}\n";
    echo "  Can Delete: {$granularStats['can_delete']}\n\n";

    // ========================================================================
    // DISPLAY MENU ITEMS
    // ========================================================================
    echo "=============================================================================\n";
    echo "ALL MENU ITEMS WITH PERMISSIONS GRANTED\n";
    echo "=============================================================================\n\n";
    echo str_repeat("-", 100) . "\n";
    printf("%-5s %-40s %-35s %-15s\n", "ID", "Name", "Link", "Icon");
    echo str_repeat("-", 100) . "\n";

    foreach ($menuItems as $item) {
        printf(
            "%-5s %-40s %-35s %-15s\n",
            $item['id'],
            substr($item['name'], 0, 40),
            substr($item['link'], 0, 35),
            substr($item['icon'] ?? 'fa fa-globe', 0, 15)
        );
    }

    echo str_repeat("-", 100) . "\n\n";

    // ========================================================================
    // SAMPLE GRANULAR PERMISSIONS
    // ========================================================================
    echo "Sample Granular Permissions (First 10):\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-5s %-30s %-6s %-6s %-6s %-6s\n", "ID", "Menu Name", "Read", "Create", "Update", "Delete");
    echo str_repeat("-", 80) . "\n";

    $sampleQuery = "SELECT 
        up.menu_id,
        m.name,
        up.can_read,
        up.can_create,
        up.can_update,
        up.can_delete
        FROM user_permissions up
        JOIN menu m ON m.id = up.menu_id
        WHERE up.user_id = 19
        ORDER BY up.menu_id
        LIMIT 10";
    $sampleResult = $mysqli->query($sampleQuery);

    while ($row = $sampleResult->fetch_assoc()) {
        printf(
            "%-5s %-30s %-6s %-6s %-6s %-6s\n",
            $row['menu_id'],
            substr($row['name'], 0, 30),
            $row['can_read'] ? '✓' : '✗',
            $row['can_create'] ? '✓' : '✗',
            $row['can_update'] ? '✓' : '✗',
            $row['can_delete'] ? '✓' : '✗'
        );
    }

    echo str_repeat("-", 80) . "\n\n";

    // ========================================================================
    // SUCCESS SUMMARY
    // ========================================================================
    echo "=============================================================================\n";
    echo "✓ SUCCESS\n";
    echo "=============================================================================\n\n";
    echo "Admin user 'admin@admin.com' (ID 19) has been granted:\n\n";
    echo "  ✓ Legacy Permissions: {$verifyUser['perm_count']} menu items\n";
    echo "  ✓ Granular Permissions: {$granularStats['total']} CRUD permission sets\n";
    echo "  ✓ Full Access: Read, Create, Update, Delete for all modules\n\n";
    echo "The user can now access all modules in the CRM system with full privileges.\n\n";

    // ========================================================================
    // NOTES
    // ========================================================================
    echo "=============================================================================\n";
    echo "NOTES\n";
    echo "=============================================================================\n\n";
    echo "Permissions were granted in two ways:\n\n";
    echo "1. Legacy Method:\n";
    echo "   - Location: users.permissions field (JSON array)\n";
    echo "   - Purpose: Backward compatibility\n";
    echo "   - Content: Array of menu IDs as strings\n\n";
    echo "2. Granular Method:\n";
    echo "   - Location: user_permissions table\n";
    echo "   - Purpose: Fine-grained CRUD control\n";
    echo "   - Content: Individual read/create/update/delete flags per menu\n\n";
    echo "The granular permissions system takes precedence and provides better\n";
    echo "control over user actions within each module.\n\n";

    // Close connections
    $stmt->close();
    $insertStmt->close();
    $mysqli->close();

} catch (Exception $e) {
    echo "\n=============================================================================\n";
    echo "❌ ERROR\n";
    echo "=============================================================================\n\n";
    echo $e->getMessage() . "\n\n";
    exit(1);
}

echo "=============================================================================\n";
echo "Script completed successfully!\n";
echo "=============================================================================\n";
exit(0);

