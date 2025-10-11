<?php
/**
 * Script: Check Missing Routes (Dry Run)
 * Description: Shows what routes would be added without modifying database
 * Author: Claude Code
 * Date: 2025-10-11
 *
 * Usage (from host machine):
 *   php SQLs/check_missing_routes.php
 *
 * This script connects to the database via exposed Docker port (localhost:3309)
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

// Define potential routes to check
$routesToCheck = [
    [
        'name' => 'Chart of Accounts',
        'link' => '/accounts',
        'icon' => 'fa fa-book',
        'category' => 'Accounting',
        'priority' => 'HIGH'
    ],
    [
        'name' => 'Journal Entries',
        'link' => '/journal-entries',
        'icon' => 'fa fa-edit',
        'category' => 'Accounting',
        'priority' => 'HIGH'
    ],
    [
        'name' => 'Accounting Periods',
        'link' => '/accounting-periods',
        'icon' => 'fa fa-calendar-check',
        'category' => 'Accounting',
        'priority' => 'HIGH'
    ],
    [
        'name' => 'Balance Sheet',
        'link' => '/balance-sheet',
        'icon' => 'fa fa-balance-scale',
        'category' => 'Financial Reports',
        'priority' => 'HIGH'
    ],
    [
        'name' => 'Trial Balance',
        'link' => '/trial-balance',
        'icon' => 'fa fa-calculator',
        'category' => 'Financial Reports',
        'priority' => 'HIGH'
    ],
    [
        'name' => 'Profit & Loss',
        'link' => '/profit-loss',
        'icon' => 'fa fa-chart-line',
        'category' => 'Financial Reports',
        'priority' => 'HIGH'
    ],
    [
        'name' => 'Cash Flow Report',
        'link' => '/cash-flow',
        'icon' => 'fa fa-money-bill-wave',
        'category' => 'Financial Reports',
        'priority' => 'MEDIUM'
    ],
    [
        'name' => 'API Documentation',
        'link' => '/swagger',
        'icon' => 'fa fa-code',
        'category' => 'Developer Tools',
        'priority' => 'MEDIUM'
    ],
];

echo str_repeat("=", 80) . "\n";
echo "Check Missing Routes (Dry Run)\n";
echo "This script will NOT modify the database\n";
echo str_repeat("=", 80) . "\n\n";

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

    // Get current menu items
    echo "Current Menu Status:\n";
    echo str_repeat("-", 80) . "\n";

    $countQuery = "SELECT COUNT(*) as count FROM menu";
    $result = $mysqli->query($countQuery);
    $totalCount = $result->fetch_assoc()['count'];
    echo "  Total menu items: {$totalCount}\n";

    // Get existing links
    $existingLinksQuery = "SELECT id, name, link FROM menu ORDER BY id";
    $existingResult = $mysqli->query($existingLinksQuery);
    $existingLinks = [];
    $existingData = [];

    while ($row = $existingResult->fetch_assoc()) {
        $existingLinks[] = $row['link'];
        $existingData[$row['link']] = $row;
    }

    echo "  Existing routes: " . count($existingLinks) . "\n\n";

    // Check which routes are missing
    $missing = [];
    $existing = [];

    foreach ($routesToCheck as $route) {
        if (in_array($route['link'], $existingLinks)) {
            $existing[] = $route;
        } else {
            $missing[] = $route;
        }
    }

    // Display results
    echo str_repeat("=", 80) . "\n";
    echo "ANALYSIS RESULTS\n";
    echo str_repeat("=", 80) . "\n\n";

    // Show missing routes
    if (count($missing) > 0) {
        echo "Routes NOT in Menu Table (" . count($missing) . "):\n";
        echo str_repeat("-", 80) . "\n";
        printf("%-30s %-30s %-15s %-10s\n", "Name", "Link", "Category", "Priority");
        echo str_repeat("-", 80) . "\n";

        foreach ($missing as $route) {
            printf(
                "%-30s %-30s %-15s %-10s\n",
                substr($route['name'], 0, 30),
                substr($route['link'], 0, 30),
                $route['category'],
                $route['priority']
            );
        }
        echo str_repeat("-", 80) . "\n\n";
    } else {
        echo "✓ All checked routes exist in menu table\n\n";
    }

    // Show existing routes
    if (count($existing) > 0) {
        echo "Routes Already in Menu Table (" . count($existing) . "):\n";
        echo str_repeat("-", 80) . "\n";
        printf("%-5s %-30s %-30s\n", "ID", "Name", "Link");
        echo str_repeat("-", 80) . "\n";

        foreach ($existing as $route) {
            $menuData = $existingData[$route['link']];
            printf(
                "%-5s %-30s %-30s\n",
                $menuData['id'],
                substr($menuData['name'], 0, 30),
                substr($menuData['link'], 0, 30)
            );
        }
        echo str_repeat("-", 80) . "\n\n";
    }

    // Summary by category
    if (count($missing) > 0) {
        echo "Missing Routes by Category:\n";
        echo str_repeat("-", 80) . "\n";

        $byCategory = [];
        foreach ($missing as $route) {
            if (!isset($byCategory[$route['category']])) {
                $byCategory[$route['category']] = [];
            }
            $byCategory[$route['category']][] = $route;
        }

        foreach ($byCategory as $category => $routes) {
            echo "\n{$category} ({" . count($routes) . " routes):\n";
            foreach ($routes as $route) {
                echo "  - {$route['name']} ({$route['link']})\n";
            }
        }
        echo "\n";
    }

    // Summary by priority
    if (count($missing) > 0) {
        echo "\nMissing Routes by Priority:\n";
        echo str_repeat("-", 80) . "\n";

        $byPriority = [];
        foreach ($missing as $route) {
            if (!isset($byPriority[$route['priority']])) {
                $byPriority[$route['priority']] = [];
            }
            $byPriority[$route['priority']][] = $route;
        }

        foreach (['HIGH', 'MEDIUM', 'LOW'] as $priority) {
            if (isset($byPriority[$priority])) {
                echo "\n{$priority} Priority (" . count($byPriority[$priority]) . " routes):\n";
                foreach ($byPriority[$priority] as $route) {
                    echo "  - {$route['name']} ({$route['link']})\n";
                }
            }
        }
        echo "\n";
    }

    // Recommendations
    echo str_repeat("=", 80) . "\n";
    echo "RECOMMENDATIONS\n";
    echo str_repeat("=", 80) . "\n\n";

    if (count($missing) > 0) {
        echo "To add missing routes to the menu table, run:\n\n";
        echo "  php SQLs/add_missing_routes_to_menu.php\n\n";
        echo "This will:\n";
        echo "  1. Add " . count($missing) . " new route(s) to the menu table\n";
        echo "  2. Automatically update admin@admin.com permissions\n";
        echo "  3. Assign sequential sort orders\n";
        echo "  4. Generate UUIDs for new entries\n\n";

        echo "After running the script:\n";
        echo "  - Total menu items will be: " . ($totalCount + count($missing)) . "\n";
        echo "  - Admin will have access to all routes\n";
        echo "  - New routes will appear in sidebar navigation\n\n";
    } else {
        echo "✓ No action needed - all routes are already in the menu table\n\n";
    }

    // Admin permissions check
    echo "Admin User Permissions:\n";
    echo str_repeat("-", 80) . "\n";

    $adminQuery = "SELECT email, JSON_LENGTH(permissions) as perm_count FROM users WHERE email = 'admin@admin.com'";
    $adminResult = $mysqli->query($adminQuery);

    if ($adminResult && $adminResult->num_rows > 0) {
        $admin = $adminResult->fetch_assoc();
        echo "  User: {$admin['email']}\n";
        echo "  Current permissions: {$admin['perm_count']}\n";
        echo "  Total menu items: {$totalCount}\n";

        if ($admin['perm_count'] < $totalCount) {
            echo "  ⚠ WARNING: User has fewer permissions than menu items!\n";
            echo "  Run: php SQLs/grant_all_menu_permissions_to_admin.php\n";
        } elseif ($admin['perm_count'] == $totalCount) {
            echo "  ✓ User has access to all menu items\n";
        } else {
            echo "  ⚠ User has MORE permissions than menu items (may include deleted items)\n";
        }
    } else {
        echo "  ⚠ Admin user not found\n";
    }

    echo "\n";

    // Close connection
    $mysqli->close();

    echo str_repeat("=", 80) . "\n";
    echo "Dry run completed successfully\n";
    echo "No changes were made to the database\n";
    echo str_repeat("=", 80) . "\n\n";

    exit(0);

} catch (Exception $e) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "❌ ERROR\n";
    echo str_repeat("=", 80) . "\n";
    echo $e->getMessage() . "\n\n";
    exit(1);
}
