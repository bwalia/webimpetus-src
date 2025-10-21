<?php
/**
 * Script: Add Missing Routes to Menu Table
 * Description: Automatically adds missing accounting and report routes to menu
 * Author: Claude Code
 * Date: 2025-10-11
 *
 * Usage (from host machine):
 *   php SQLs/add_missing_routes_to_menu.php
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

// Define routes to add
$routesToAdd = [
    [
        'name' => 'Chart of Accounts',
        'link' => '/accounts',
        'icon' => 'fa fa-book',
        'menu_fts' => 'Chart of Accounts accounts accounting finance',
        'category' => 'Accounting'
    ],
    [
        'name' => 'Journal Entries',
        'link' => '/journal-entries',
        'icon' => 'fa fa-edit',
        'menu_fts' => 'Journal Entries accounting bookkeeping transactions',
        'category' => 'Accounting'
    ],
    [
        'name' => 'Accounting Periods',
        'link' => '/accounting-periods',
        'icon' => 'fa fa-calendar-check',
        'menu_fts' => 'Accounting Periods fiscal year-end closing',
        'category' => 'Accounting'
    ],
    [
        'name' => 'Balance Sheet',
        'link' => '/balance-sheet',
        'icon' => 'fa fa-balance-scale',
        'menu_fts' => 'Balance Sheet financial report assets liabilities equity',
        'category' => 'Financial Reports'
    ],
    [
        'name' => 'Trial Balance',
        'link' => '/trial-balance',
        'icon' => 'fa fa-calculator',
        'menu_fts' => 'Trial Balance accounting report verification',
        'category' => 'Financial Reports'
    ],
    [
        'name' => 'Profit & Loss',
        'link' => '/profit-loss',
        'icon' => 'fa fa-chart-line',
        'menu_fts' => 'Profit Loss income statement revenue expenses P&L',
        'category' => 'Financial Reports'
    ],
    [
        'name' => 'API Documentation',
        'link' => '/swagger',
        'icon' => 'fa fa-code',
        'menu_fts' => 'API Documentation swagger developer tools',
        'category' => 'Developer Tools'
    ],
];

echo str_repeat("=", 80) . "\n";
echo "Add Missing Routes to Menu Table\n";
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

    // Step 1: Check current menu items
    echo "Step 1: Checking current menu items...\n";
    $currentMenuQuery = "SELECT COUNT(*) as count FROM menu";
    $result = $mysqli->query($currentMenuQuery);
    $currentCount = $result->fetch_assoc()['count'];
    echo "  Current menu items: {$currentCount}\n\n";

    // Step 2: Get existing menu links to avoid duplicates
    echo "Step 2: Checking for existing routes...\n";
    $existingLinksQuery = "SELECT link FROM menu";
    $existingResult = $mysqli->query($existingLinksQuery);
    $existingLinks = [];
    while ($row = $existingResult->fetch_assoc()) {
        $existingLinks[] = $row['link'];
    }
    echo "  Found " . count($existingLinks) . " existing menu links\n\n";

    // Step 3: Get next sort order
    echo "Step 3: Calculating next sort order...\n";
    $sortQuery = "SELECT COALESCE(MAX(sort_order), 0) + 10 as next_sort FROM menu";
    $sortResult = $mysqli->query($sortQuery);
    $nextSort = $sortResult->fetch_assoc()['next_sort'];
    echo "  Next sort order: {$nextSort}\n\n";

    // Step 4: Add routes
    echo "Step 4: Adding missing routes...\n";
    echo str_repeat("-", 80) . "\n";

    $added = 0;
    $skipped = 0;
    $errors = 0;
    $currentSort = $nextSort;

    foreach ($routesToAdd as $route) {
        // Check if route already exists
        if (in_array($route['link'], $existingLinks)) {
            echo "  ⊘ SKIP: {$route['name']} - Already exists\n";
            $skipped++;
            continue;
        }

        // Prepare insert statement
        $insertQuery = "INSERT INTO menu (name, link, icon, sort_order, language_code, uuid, menu_fts)
                        VALUES (?, ?, ?, ?, 'en', UUID(), ?)";

        $stmt = $mysqli->prepare($insertQuery);

        if (!$stmt) {
            echo "  ✗ ERROR: {$route['name']} - Failed to prepare statement\n";
            $errors++;
            continue;
        }

        $stmt->bind_param(
            'sssis',
            $route['name'],
            $route['link'],
            $route['icon'],
            $currentSort,
            $route['menu_fts']
        );

        if ($stmt->execute()) {
            $newId = $stmt->insert_id;
            echo "  ✓ ADDED: {$route['name']} (ID: {$newId}, Link: {$route['link']})\n";
            $added++;
            $currentSort += 10;
        } else {
            echo "  ✗ ERROR: {$route['name']} - {$stmt->error}\n";
            $errors++;
        }

        $stmt->close();
    }

    echo str_repeat("-", 80) . "\n\n";

    // Step 5: Verify results
    echo "Step 5: Verifying results...\n";
    $newCountQuery = "SELECT COUNT(*) as count FROM menu";
    $newResult = $mysqli->query($newCountQuery);
    $newCount = $newResult->fetch_assoc()['count'];
    echo "  New total menu items: {$newCount}\n";
    echo "  Items added: {$added}\n";
    echo "  Items skipped: {$skipped}\n";
    if ($errors > 0) {
        echo "  Errors: {$errors}\n";
    }
    echo "\n";

    // Step 6: Show newly added items
    if ($added > 0) {
        echo "Step 6: Newly added menu items:\n";
        echo str_repeat("-", 80) . "\n";
        printf("%-5s %-30s %-30s %-20s\n", "ID", "Name", "Link", "Icon");
        echo str_repeat("-", 80) . "\n";

        $newItemsQuery = "SELECT id, name, link, icon FROM menu WHERE id > ? ORDER BY id";
        $stmt = $mysqli->prepare($newItemsQuery);
        $stmt->bind_param('i', $currentCount);
        $stmt->execute();
        $newItemsResult = $stmt->get_result();

        while ($row = $newItemsResult->fetch_assoc()) {
            printf(
                "%-5s %-30s %-30s %-20s\n",
                $row['id'],
                substr($row['name'], 0, 30),
                substr($row['link'], 0, 30),
                $row['icon']
            );
        }
        echo str_repeat("-", 80) . "\n\n";
        $stmt->close();
    }

    // Step 7: Update admin permissions
    if ($added > 0) {
        echo "Step 7: Updating admin user permissions...\n";

        // Get all menu IDs
        $allMenuQuery = "SELECT GROUP_CONCAT(CONCAT('\"', id, '\"') ORDER BY id SEPARATOR ', ') as ids FROM menu";
        $allMenuResult = $mysqli->query($allMenuQuery);
        $allIds = $allMenuResult->fetch_assoc()['ids'];
        $permissionsJson = '[' . $allIds . ']';

        // Update admin user
        $updateAdminQuery = "UPDATE users SET permissions = ? WHERE email = 'admin@admin.com'";
        $stmt = $mysqli->prepare($updateAdminQuery);
        $stmt->bind_param('s', $permissionsJson);

        if ($stmt->execute()) {
            $affectedRows = $stmt->affected_rows;
            echo "  ✓ Updated admin@admin.com permissions\n";
            echo "  Total permissions: " . substr_count($permissionsJson, '"') / 2 . "\n";
        } else {
            echo "  ✗ Failed to update admin permissions: {$stmt->error}\n";
        }
        $stmt->close();
        echo "\n";
    }

    // Summary
    echo str_repeat("=", 80) . "\n";
    if ($added > 0) {
        echo "✓ SUCCESS\n";
        echo str_repeat("=", 80) . "\n";
        echo "Added {$added} new menu item(s) to the system.\n";
        echo "Admin user permissions have been updated.\n\n";

        echo "NEXT STEPS:\n";
        echo "1. Refresh your browser to see new menu items\n";
        echo "2. Check sidebar navigation for new routes\n";
        echo "3. Verify all routes are accessible\n";
        echo "4. Configure role-based permissions as needed\n\n";
    } elseif ($skipped > 0 && $added == 0) {
        echo "✓ ALREADY UP TO DATE\n";
        echo str_repeat("=", 80) . "\n";
        echo "All routes already exist in the menu table.\n";
        echo "No changes were made.\n\n";
    } else {
        echo "⚠ PARTIAL SUCCESS\n";
        echo str_repeat("=", 80) . "\n";
        echo "Some routes were added, but there were errors.\n";
        echo "Please review the output above.\n\n";
    }

    // Close connection
    $mysqli->close();

    exit($errors > 0 ? 1 : 0);

} catch (Exception $e) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "❌ ERROR\n";
    echo str_repeat("=", 80) . "\n";
    echo $e->getMessage() . "\n\n";
    exit(1);
}
