<?php
/**
 * Debug script to test permission loading
 * Run this with a user ID to see what permissions would load
 */

// Database connection
$host = 'workerra-ci-db';
$dbname = 'myworkstation_dev';
$username = 'wsl_dev';
$password = 'CHANGE_ME';

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== PERMISSION LOADING DEBUG ===\n\n";

// Get user ID from command line or use default
$user_id = isset($argv[1]) ? (int)$argv[1] : 2;

echo "Testing for User ID: $user_id\n";
echo "-----------------------------------\n\n";

// Get user data
$query = "SELECT id, name, email, permissions, role FROM users WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found!\n");
}

echo "USER DATA:\n";
echo "  Name: {$user['name']}\n";
echo "  Email: {$user['email']}\n";
echo "  Role: {$user['role']}\n";
echo "  Permissions (raw): {$user['permissions']}\n\n";

// Check if user has UUID role
function isUUID($string) {
    return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $string);
}

if ($user['id'] == 1) {
    echo "USER IS ADMIN (ID=1):\n";
    echo "  ✅ Gets ALL menus automatically\n\n";

    $menus = $mysqli->query("SELECT id, name, link FROM menu ORDER BY id");
    echo "ALL MENUS (Admin has access to everything):\n";
    while ($menu = $menus->fetch_assoc()) {
        echo "  [{$menu['id']}] {$menu['name']} -> {$menu['link']}\n";
    }
} else if (!empty($user['role']) && isUUID($user['role'])) {
    echo "USER HAS UUID ROLE: {$user['role']}\n";
    echo "  Loading permissions from roles__permissions table...\n\n";

    $roleQuery = "SELECT permission_id FROM roles__permissions WHERE role_id = ?";
    $roleStmt = $mysqli->prepare($roleQuery);
    $roleStmt->bind_param('s', $user['role']);
    $roleStmt->execute();
    $roleResult = $roleStmt->get_result();

    $permissionUUIDs = [];
    while ($perm = $roleResult->fetch_assoc()) {
        $permissionUUIDs[] = $perm['permission_id'];
    }

    echo "PERMISSION UUIDs FROM ROLE:\n";
    print_r($permissionUUIDs);
    echo "\n";

    if (!empty($permissionUUIDs)) {
        $placeholders = implode(',', array_fill(0, count($permissionUUIDs), '?'));
        $menuQuery = "SELECT id, name, link, uuid FROM menu WHERE uuid IN ($placeholders)";
        $menuStmt = $mysqli->prepare($menuQuery);
        $menuStmt->bind_param(str_repeat('s', count($permissionUUIDs)), ...$permissionUUIDs);
        $menuStmt->execute();
        $menuResult = $menuStmt->get_result();

        echo "MENUS USER HAS ACCESS TO (via role):\n";
        $count = 0;
        while ($menu = $menuResult->fetch_assoc()) {
            echo "  [{$menu['id']}] {$menu['name']} -> {$menu['link']}\n";
            $count++;
        }

        if ($count == 0) {
            echo "  ⚠️  NO MENUS FOUND! Role UUIDs don't match any menu UUIDs\n";
        }
    } else {
        echo "  ⚠️  NO PERMISSIONS IN ROLE!\n";
    }
} else {
    echo "USER HAS DIRECT PERMISSIONS:\n";

    // Decode permissions
    $permissions_string = $user['permissions'];

    if (empty($permissions_string) || $permissions_string === 'null') {
        echo "  ⚠️  NO PERMISSIONS SET!\n\n";
        die();
    }

    echo "  Decoding permissions...\n";

    // Test both decode methods
    $decoded_object = json_decode($permissions_string);
    $decoded_array = json_decode($permissions_string, true);

    echo "\n  WITHOUT 'true': " . gettype($decoded_object) . " - ";
    var_export($decoded_object);
    echo "\n";

    echo "  WITH 'true': " . gettype($decoded_array) . " - ";
    var_export($decoded_array);
    echo "\n\n";

    // Check if it's an array
    if (!is_array($decoded_array)) {
        echo "  ⚠️  ERROR: Decoded permissions is not an array!\n";
        die();
    }

    if (empty($decoded_array)) {
        echo "  ⚠️  ERROR: Permissions array is empty!\n";
        die();
    }

    echo "PERMISSION IDs:\n";
    print_r($decoded_array);
    echo "\n";

    // Query menus with these IDs
    $placeholders = implode(',', array_fill(0, count($decoded_array), '?'));
    $menuQuery = "SELECT id, name, link FROM menu WHERE id IN ($placeholders)";
    $menuStmt = $mysqli->prepare($menuQuery);

    // Bind all IDs (they're strings, so bind as strings)
    $types = str_repeat('s', count($decoded_array));
    $menuStmt->bind_param($types, ...$decoded_array);
    $menuStmt->execute();
    $menuResult = $menuStmt->get_result();

    echo "MENUS USER HAS ACCESS TO:\n";
    $count = 0;
    while ($menu = $menuResult->fetch_assoc()) {
        echo "  [{$menu['id']}] {$menu['name']} -> {$menu['link']}\n";
        $count++;
    }

    if ($count == 0) {
        echo "  ⚠️  NO MENUS FOUND! Check if menu IDs exist\n";
    } else {
        echo "\n  ✅ Found $count menus\n";
    }
}

echo "\n=== END DEBUG ===\n";

$mysqli->close();
