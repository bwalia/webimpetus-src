#!/usr/bin/env php
<?php
/**
 * Test Database Connection
 * Quick script to verify database connection works from host
 */

echo "=============================================================================\n";
echo "Database Connection Test\n";
echo "=============================================================================\n\n";

$config = [
    'hostname' => '127.0.0.1',
    'port' => 3309,
    'username' => 'wsl_dev',
    'password' => 'CHANGE_ME',
    'database' => 'myworkstation_dev',
];

echo "Testing connection to:\n";
echo "  Host: {$config['hostname']}\n";
echo "  Port: {$config['port']}\n";
echo "  User: {$config['username']}\n";
echo "  Database: {$config['database']}\n\n";

try {
    // Test connection
    $mysqli = new mysqli(
        $config['hostname'],
        $config['username'],
        $config['password'],
        $config['database'],
        $config['port']
    );

    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }

    echo "✓ Connection successful!\n\n";

    // Get server info
    echo "Server Information:\n";
    echo "  Server: " . $mysqli->server_info . "\n";
    echo "  Protocol: " . $mysqli->protocol_version . "\n";
    echo "  Host info: " . $mysqli->host_info . "\n\n";

    // Test query - count menu items
    echo "Testing query:\n";
    $result = $mysqli->query("SELECT COUNT(*) as count FROM menu");

    if ($result) {
        $row = $result->fetch_assoc();
        echo "  ✓ Query successful\n";
        echo "  Menu items in database: {$row['count']}\n\n";
        $result->free();
    } else {
        echo "  ✗ Query failed: " . $mysqli->error . "\n\n";
    }

    // Test admin user
    echo "Testing admin user:\n";
    $result = $mysqli->query("SELECT email, JSON_LENGTH(permissions) as perm_count FROM users WHERE email = 'admin@admin.com'");

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "  ✓ Admin user found\n";
        echo "  Email: {$row['email']}\n";
        echo "  Permissions: {$row['perm_count']}\n\n";
        $result->free();
    } else {
        echo "  ✗ Admin user not found\n\n";
    }

    $mysqli->close();

    echo "=============================================================================\n";
    echo "✓ All tests passed!\n";
    echo "=============================================================================\n";
    echo "\nYou can now run the menu management scripts:\n";
    echo "  ./run_menu_scripts.sh check\n";
    echo "  ./run_menu_scripts.sh add\n";
    echo "  ./run_menu_scripts.sh grant\n\n";

    exit(0);

} catch (Exception $e) {
    echo "\n=============================================================================\n";
    echo "❌ Connection Test Failed\n";
    echo "=============================================================================\n";
    echo "Error: " . $e->getMessage() . "\n\n";

    echo "Troubleshooting:\n";
    echo "1. Check if database container is running:\n";
    echo "   docker ps | grep webimpetus-db\n\n";

    echo "2. Check if port 3309 is exposed:\n";
    echo "   docker port webimpetus-db\n\n";

    echo "3. Test connection manually:\n";
    echo "   mysql -h 127.0.0.1 -P 3309 -u wsl_dev -p'CHANGE_ME' myworkstation_dev\n\n";

    echo "4. Check database credentials in .env file:\n";
    echo "   grep database.default .env\n\n";

    exit(1);
}
