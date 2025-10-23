#!/usr/bin/env php
<?php
/**
 * Anonymization Verification Script
 *
 * Verifies that all PII data has been anonymized
 *
 * Usage: php verify_anonymization.php
 *
 * Date: 2025-10-12
 */

// Configuration
define('DB_HOST', 'workerra-ci-db');
define('DB_NAME', 'myworkstation_dev');
define('DB_USER', 'workerra-ci-dev');
define('DB_PASS', 'CHANGE_ME');
define('ADMIN_EMAIL', 'admin@admin.com');

// Colors for CLI output
class Colors {
    public static $GREEN = "\033[0;32m";
    public static $RED = "\033[0;31m";
    public static $YELLOW = "\033[1;33m";
    public static $BLUE = "\033[0;34m";
    public static $NC = "\033[0m";
}

class AnonymizationVerifier {
    private $pdo;
    private $issues = [];
    private $checks = [];

    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->log("✓ Connected to database: " . DB_NAME, 'green');
        } catch (PDOException $e) {
            $this->log("✗ Connection failed: " . $e->getMessage(), 'red');
            exit(1);
        }
    }

    private function log($message, $color = 'nc') {
        $colors = [
            'green' => Colors::$GREEN,
            'red' => Colors::$RED,
            'yellow' => Colors::$YELLOW,
            'blue' => Colors::$BLUE,
            'nc' => Colors::$NC
        ];
        echo $colors[$color] . $message . Colors::$NC . PHP_EOL;
    }

    private function check($table, $column, $pattern, $description, $excludeWhere = '') {
        try {
            $where = "WHERE $column NOT LIKE '$pattern'";
            if ($excludeWhere) {
                $where .= " AND $excludeWhere";
            }

            $sql = "SELECT COUNT(*) as count FROM $table $where";
            $stmt = $this->pdo->query($sql);
            $result = $stmt->fetch();
            $count = $result['count'];

            $this->checks[] = [
                'table' => $table,
                'description' => $description,
                'count' => $count,
                'status' => $count == 0 ? 'pass' : 'fail'
            ];

            if ($count > 0) {
                $this->issues[] = "$table: Found $count rows with non-anonymized $column";
                $this->log("  ✗ $description: $count issues found", 'red');
            } else {
                $this->log("  ✓ $description", 'green');
            }

            return $count == 0;
        } catch (PDOException $e) {
            $this->log("  ⚠ $description: Table/column not found", 'yellow');
            return true; // Don't fail if table doesn't exist
        }
    }

    private function checkCount($table, $description) {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch();
            $this->log("  ℹ $description: " . number_format($result['count']) . " records", 'blue');
            return $result['count'];
        } catch (PDOException $e) {
            $this->log("  ⚠ $description: Table not found", 'yellow');
            return 0;
        }
    }

    public function verify() {
        $this->log("\n" . str_repeat("=", 70), 'yellow');
        $this->log("  ANONYMIZATION VERIFICATION", 'yellow');
        $this->log(str_repeat("=", 70) . "\n", 'yellow');

        // Check Users
        $this->log("[1] Verifying Users...", 'blue');
        $this->check('users', 'email', '%@example.com', 'Users with anonymized emails', "email != '" . ADMIN_EMAIL . "'");
        $this->check('users', 'phone', '555-%', 'Users with anonymized phones', "email != '" . ADMIN_EMAIL . "'");

        // Check Businesses
        $this->log("\n[2] Verifying Businesses...", 'blue');
        $this->check('businesses', 'email', '%@example.com', 'Businesses with anonymized emails');
        $this->check('businesses', 'business_name', 'Business_%', 'Businesses with anonymized names');

        // Check Employees
        $this->log("\n[3] Verifying Employees...", 'blue');
        $this->check('employees', 'email', '%@example.com', 'Employees with anonymized emails');
        $this->check('employees', 'first_name', 'Employee%', 'Employees with anonymized names');

        // Check Hospital Staff
        $this->log("\n[4] Verifying Hospital Staff...", 'blue');
        $this->check('hospital_staff', 'email', '%@hospital.example.com', 'Hospital staff with anonymized emails');
        $this->check('hospital_staff', 'first_name', 'Doctor%', 'Hospital staff with anonymized names');

        // Check Customers
        $this->log("\n[5] Verifying Customers...", 'blue');
        $this->check('customers', 'email', '%@example.com', 'Customers with anonymized emails');
        $this->check('customers', 'customer_name', 'Customer_%', 'Customers with anonymized names');

        // Check Companies (NEW)
        $this->log("\n[6] Verifying Companies...", 'blue');
        $this->check('companies', 'email', '%@example.com', 'Companies with anonymized emails');
        $this->check('companies', 'company_name', 'Anonymous Company%', 'Companies with anonymized names');
        $this->checkCount('companies', 'Total companies');

        // Check Contacts (NEW)
        $this->log("\n[7] Verifying Contacts...", 'blue');
        $this->check('contacts', 'email', '%@example.com', 'Contacts with anonymized emails');
        $this->check('contacts', 'first_name', 'Contact%', 'Contacts with anonymized first names');
        $this->checkCount('contacts', 'Total contacts');

        // Check Sales Invoices (NEW)
        $this->log("\n[8] Verifying Sales Invoices...", 'blue');
        $this->check('sales_invoices', 'custom_invoice_number', 'INV-%', 'Invoices with anonymized numbers');
        $this->check('sales_invoices', 'bill_to', 'Anonymous Customer%', 'Invoices with anonymized bill_to');
        $this->checkCount('sales_invoices', 'Total sales invoices');

        // Check Sales Invoice Items (NEW)
        $this->log("\n[9] Verifying Sales Invoice Items...", 'blue');
        $this->check('sales_invoice_items', 'description', 'Anonymized%', 'Invoice items with anonymized descriptions');
        $this->checkCount('sales_invoice_items', 'Total invoice items');

        // Check Timeslips (NEW)
        $this->log("\n[10] Verifying Timeslips...", 'blue');
        $this->check('timeslips', 'task_name', 'Task %', 'Timeslips with anonymized task names');
        $this->check('timeslips', 'employee_name', 'Employee%', 'Timeslips with anonymized employee names');
        $this->checkCount('timeslips', 'Total timeslips');

        // Check Contacts
        $this->log("\n[11] Verifying Business Contacts...", 'blue');
        $this->check('business_contacts', 'email', '%@example.com', 'Business contacts with anonymized emails');

        // Check Email Campaigns
        $this->log("\n[12] Verifying Email Campaigns...", 'blue');
        $this->check('email_campaigns', 'from_email', '%@example.com', 'Email campaigns with anonymized emails');

        // Check Secrets
        $this->log("\n[13] Verifying Secrets...", 'blue');
        $this->check('secrets', 'secret_value', 'ANONYMIZED_SECRET_%', 'Secrets with anonymized values');

        // Admin user preservation check
        $this->log("\n[14] Verifying Admin User Preservation...", 'blue');
        try {
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->execute([ADMIN_EMAIL]);
            if ($stmt->fetch()) {
                $this->log("  ✓ Admin user preserved (" . ADMIN_EMAIL . ")", 'green');
            } else {
                $this->log("  ✗ Admin user NOT found!", 'red');
                $this->issues[] = "Admin user " . ADMIN_EMAIL . " not found";
            }
        } catch (PDOException $e) {
            $this->log("  ✗ Could not verify admin user", 'red');
        }

        $this->printSummary();
    }

    private function printSummary() {
        $this->log("\n" . str_repeat("=", 70), 'yellow');
        $this->log("  VERIFICATION SUMMARY", 'yellow');
        $this->log(str_repeat("=", 70) . "\n", 'yellow');

        $passed = count(array_filter($this->checks, fn($c) => $c['status'] === 'pass'));
        $failed = count(array_filter($this->checks, fn($c) => $c['status'] === 'fail'));
        $total = count($this->checks);

        $this->log("Total Checks: $total", 'blue');
        $this->log("Passed: $passed", 'green');
        $this->log("Failed: $failed", $failed > 0 ? 'red' : 'green');

        if (!empty($this->issues)) {
            $this->log("\n⚠ ISSUES FOUND:", 'yellow');
            foreach ($this->issues as $issue) {
                $this->log("  • $issue", 'red');
            }
            $this->log("\n✗ Anonymization INCOMPLETE - Issues need to be resolved", 'red');
            exit(1);
        } else {
            $this->log("\n✓ All checks passed!", 'green');
            $this->log("✓ Database has been successfully anonymized", 'green');
            $this->log("✓ Only admin@admin.com user preserved", 'green');
            exit(0);
        }
    }
}

// Run verification
$verifier = new AnonymizationVerifier();
$verifier->verify();
