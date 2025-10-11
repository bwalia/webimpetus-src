#!/usr/bin/env php
<?php
/**
 * Check API Coverage for Menu Items
 * Identifies which menu items have API controllers and which are missing
 */

// Menu items from database
$menuItems = [
    'Dashboard' => '/dashboard',
    'Categories' => '/categories',
    'Users' => '/users',
    'Tenants' => '/tenants',
    'Services' => '/services',
    'Domains' => '/domains',
    'Web Pages' => '/webpages',
    'Blog' => '/blog',
    'Blog Comments' => '/blog_comments',
    'Job Vacancies' => '/jobs',
    'Job Applications' => '/jobapps',
    'Image Gallery' => '/gallery',
    'Blocks' => '/blocks',
    'Enquiries' => '/enquiries',
    'Secrets' => '/secrets',
    'Customers' => '/customers',
    'Contacts' => '/contacts',
    'My Workspaces' => '/businesses',
    'Work Orders' => '/work_orders',
    'Employees' => '/employees',
    'Projects' => '/projects',
    'Templates' => '/templates',
    'Sales Invoices' => '/sales_invoices',
    'Tasks' => '/tasks',
    'Timeslips' => '/timeslips',
    'Timeslips Calendar' => '/fullcalendar',
    'Purchase Orders' => '/purchase_orders',
    'Documents' => '/documents',
    'Purchase Invoices' => '/purchase_invoices',
    'Strategies' => '/webpages?cat=strategies',
    'Sprints' => '/sprints',
    'Kanban Board' => '/kanban_board',
    'Menu' => '/menu',
    'User Workspaces' => '/user_business',
    'VAT Codes' => '/taxes',
    'Roles' => '/roles',
    'VAT Returns' => '/vat_returns',
    'Launchpad' => '/launchpad',
    'Incidents' => '/incidents',
    'Knowledge Base' => '/knowledge_base',
    'Tags' => '/tags',
    'Deployments' => '/deployments',
    'Email Campaigns' => '/email_campaigns',
    'Scrum Board' => '/scrum_board',
    'Interviews' => '/interviews',
    'Chart of Accounts' => '/accounts',
    'Journal Entries' => '/journal-entries',
    'Accounting Periods' => '/accounting-periods',
    'Balance Sheet' => '/balance-sheet',
    'Trial Balance' => '/trial-balance',
    'Profit & Loss' => '/profit-loss',
    'API Documentation' => '/swagger',
    'Products' => '/products',
];

// Existing API v2 controllers (from filesystem)
$apiControllers = [
    'Blocks', 'Businesses', 'Categories', 'Companies', 'Contacts',
    'Create_domain', 'Customers', 'Deployments', 'Documents',
    'EmailCampaigns', 'Employees', 'Enquiries', 'Incidents',
    'KnowledgeBase', 'Launchpad', 'Media', 'Menu', 'Projects',
    'Purchase_invoices', 'Purchase_orders', 'Roles', 'Sales_invoices',
    'ScimGroupController', 'ScimUserController', 'Secrets', 'Services',
    'Sprints', 'Tags', 'Tasks', 'Taxes', 'Timeslips', 'UserBusiness',
    'Users', 'VatReturns', 'VmController', 'Webpages', 'Work_orders'
];

// Normalize for comparison
$normalizedApis = array_map('strtolower', $apiControllers);

echo str_repeat("=", 80) . "\n";
echo "API Coverage Analysis for Menu Items\n";
echo str_repeat("=", 80) . "\n\n";

$hasApi = [];
$missingApi = [];
$notApplicable = [];

foreach ($menuItems as $name => $link) {
    // Clean link
    $cleanLink = trim(str_replace('/', '', explode('?', $link)[0]));

    // Items that don't need APIs (UI-only)
    $uiOnly = [
        'dashboard', 'fullcalendar', 'strategies', 'kanban_board',
        'scrum_board', 'swagger', 'accounts', 'journal-entries',
        'accounting-periods', 'balance-sheet', 'trial-balance', 'profit-loss',
        'gallery', 'blog'
    ];

    if (in_array($cleanLink, $uiOnly)) {
        $notApplicable[] = [$name, $link, 'UI-only / Reports'];
        continue;
    }

    // Check if API exists
    $found = false;
    foreach ($normalizedApis as $api) {
        if (str_replace('_', '', $api) === str_replace(['_', '-'], '', $cleanLink)) {
            $hasApi[] = [$name, $link, '/api/v2/' . $cleanLink];
            $found = true;
            break;
        }
    }

    if (!$found) {
        // Special cases
        if ($cleanLink === 'email_campaigns' && in_array('emailcampaigns', $normalizedApis)) {
            $hasApi[] = [$name, $link, '/api/v2/email-campaigns'];
        } elseif ($cleanLink === 'knowledge_base' && in_array('knowledgebase', $normalizedApis)) {
            $hasApi[] = [$name, $link, '/api/v2/knowledge-base'];
        } elseif ($cleanLink === 'vat_returns' && in_array('vatreturns', $normalizedApis)) {
            $hasApi[] = [$name, $link, '/api/v2/vat-returns'];
        } else {
            $missingApi[] = [$name, $link];
        }
    }
}

// Display results
echo "Menu Items WITH API Controllers (" . count($hasApi) . "):\n";
echo str_repeat("-", 80) . "\n";
printf("%-30s %-25s %-25s\n", "Menu Item", "Route", "API Endpoint");
echo str_repeat("-", 80) . "\n";
foreach ($hasApi as $item) {
    printf("%-30s %-25s %-25s\n",
        substr($item[0], 0, 30),
        substr($item[1], 0, 25),
        substr($item[2], 0, 25)
    );
}

echo "\n\n";
echo "Menu Items MISSING API Controllers (" . count($missingApi) . "):\n";
echo str_repeat("-", 80) . "\n";
printf("%-30s %-25s %-25s\n", "Menu Item", "Route", "Needs API?");
echo str_repeat("-", 80) . "\n";
foreach ($missingApi as $item) {
    printf("%-30s %-25s %-25s\n",
        substr($item[0], 0, 30),
        substr($item[1], 0, 25),
        "YES - Create Controller"
    );
}

echo "\n\n";
echo "Menu Items Not Needing APIs (" . count($notApplicable) . "):\n";
echo str_repeat("-", 80) . "\n";
printf("%-30s %-25s %-25s\n", "Menu Item", "Route", "Reason");
echo str_repeat("-", 80) . "\n";
foreach ($notApplicable as $item) {
    printf("%-30s %-25s %-25s\n",
        substr($item[0], 0, 30),
        substr($item[1], 0, 25),
        substr($item[2], 0, 25)
    );
}

// Summary
echo "\n\n";
echo str_repeat("=", 80) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 80) . "\n";
echo "Total Menu Items: " . count($menuItems) . "\n";
echo "With APIs: " . count($hasApi) . " ✓\n";
echo "Missing APIs: " . count($missingApi) . " ✗\n";
echo "Not Applicable: " . count($notApplicable) . " (UI-only)\n";
echo "\nAPI Coverage: " . round((count($hasApi) / (count($hasApi) + count($missingApi))) * 100, 1) . "%\n";

echo "\n";
echo str_repeat("=", 80) . "\n";
echo "RECOMMENDED ACTIONS\n";
echo str_repeat("=", 80) . "\n";

if (count($missingApi) > 0) {
    echo "\n1. Create missing API controllers:\n";
    foreach ($missingApi as $item) {
        $controller = ucfirst(str_replace(['/', '-'], ['', '_'], $item[1]));
        echo "   - ci4/app/Controllers/Api/V2/{$controller}.php\n";
    }
}

echo "\n2. After creating controllers, regenerate swagger.json:\n";
echo "   docker exec webimpetus-dev php -r \"/* regeneration script */\"\n";

echo "\n3. Add @OA annotations to new controllers for proper API documentation\n";

echo "\n";
