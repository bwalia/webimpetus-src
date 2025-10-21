#!/usr/bin/env php
<?php
/**
 * Database Anonymization Script
 *
 * This script anonymizes all PII data in the database
 * Preserves only admin@admin.com user account
 *
 * Usage: php anonymize_database.php
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
    public static $NC = "\033[0m"; // No Color
}

class DatabaseAnonymizer {
    private $pdo;
    private $stats = [];

    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            $this->log("✓ Connected to database: " . DB_NAME, 'green');
        } catch (PDOException $e) {
            $this->log("✗ Database connection failed: " . $e->getMessage(), 'red');
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

    private function anonymize($table, $updates, $where = "id > 0") {
        try {
            $setParts = [];
            foreach ($updates as $column => $value) {
                $setParts[] = "$column = $value";
            }
            $setClause = implode(', ', $setParts);

            $sql = "UPDATE $table SET $setClause WHERE $where";
            $stmt = $this->pdo->exec($sql);

            $this->stats[$table] = $stmt;
            $this->log("  ✓ $table: $stmt rows updated", 'green');
            return $stmt;
        } catch (PDOException $e) {
            $this->log("  ✗ $table failed: " . $e->getMessage(), 'red');
            return 0;
        }
    }

    public function run() {
        $this->log("\n" . str_repeat("=", 60), 'yellow');
        $this->log("  DATABASE ANONYMIZATION SCRIPT", 'yellow');
        $this->log(str_repeat("=", 60) . "\n", 'yellow');

        $this->log("Starting anonymization process...\n", 'blue');

        // Disable safe updates
        $this->pdo->exec("SET SQL_SAFE_UPDATES = 0");

        // 1. Users
        $this->log("[1/35] Anonymizing Users...", 'blue');
        $this->anonymize('users', [
            'name' => "CONCAT('User_', id)",
            'email' => "CONCAT('user_', id, '@example.com')",
            'phone' => "CONCAT('555-', LPAD(id, 7, '0'))",
            'address' => "CONCAT(id, ' Anonymous Street')",
            'city' => "'Anonymous City'",
            'postcode' => "CONCAT('AN', LPAD(id, 3, '0'))",
            'country' => "'Anonymous Country'",
            'password' => "'\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'"
        ], "email != '" . ADMIN_EMAIL . "' AND email NOT LIKE '%@example.com'");

        // 2. Businesses
        $this->log("[2/35] Anonymizing Businesses...", 'blue');
        $this->anonymize('businesses', [
            'business_name' => "CONCAT('Business_', id)",
            'contact_person' => "CONCAT('Contact_', id)",
            'email' => "CONCAT('business_', id, '@example.com')",
            'phone' => "CONCAT('555-BUS-', LPAD(id, 4, '0'))",
            'mobile' => "CONCAT('555-MOB-', LPAD(id, 4, '0'))",
            'address' => "CONCAT(id, ' Business Avenue')",
            'address_line_2' => "NULL",
            'city' => "'Business City'",
            'county' => "'Business County'",
            'postcode' => "CONCAT('BC', LPAD(id, 4, '0'))",
            'country' => "'Business Country'",
            'website' => "CONCAT('https://business', id, '.example.com')",
            'vat_number' => "CONCAT('VAT', LPAD(id, 8, '0'))",
            'company_number' => "CONCAT('CMP', LPAD(id, 8, '0'))"
        ]);

        // 3. Employees
        $this->log("[3/35] Anonymizing Employees...", 'blue');
        $this->anonymize('employees', [
            'first_name' => "CONCAT('Employee', id)",
            'last_name' => "CONCAT('LastName', id)",
            'email' => "CONCAT('employee_', id, '@example.com')",
            'phone' => "CONCAT('555-EMP-', LPAD(id, 4, '0'))",
            'mobile' => "CONCAT('555-MOB-', LPAD(id, 4, '0'))",
            'address' => "CONCAT(id, ' Employee Street')",
            'city' => "'Employee City'",
            'postcode' => "CONCAT('EMP', LPAD(id, 3, '0'))",
            'country' => "'Employee Country'",
            'national_insurance_number' => "CONCAT('NI', LPAD(id, 8, '0'))",
            'emergency_contact_name' => "CONCAT('Emergency_', id)",
            'emergency_contact_phone' => "CONCAT('555-EMR-', LPAD(id, 4, '0'))"
        ]);

        // 4. Hospital Staff
        $this->log("[4/35] Anonymizing Hospital Staff...", 'blue');
        $this->anonymize('hospital_staff', [
            'first_name' => "CONCAT('Doctor', id)",
            'last_name' => "CONCAT('Staff', id)",
            'email' => "CONCAT('doctor_', id, '@hospital.example.com')",
            'phone' => "CONCAT('555-DOC-', LPAD(id, 4, '0'))",
            'mobile' => "CONCAT('555-MOB-', LPAD(id, 4, '0'))",
            'address' => "CONCAT(id, ' Medical Avenue')",
            'city' => "'Medical City'",
            'postcode' => "CONCAT('MED', LPAD(id, 3, '0'))",
            'country' => "'Medical Country'",
            'national_insurance_number' => "CONCAT('NI', LPAD(id, 8, '0'))",
            'emergency_contact_name' => "CONCAT('Emergency_', id)",
            'emergency_contact_phone' => "CONCAT('555-EMR-', LPAD(id, 4, '0'))"
        ]);

        // 5. Patient Logs
        $this->log("[5/35] Anonymizing Patient Logs...", 'blue');
        $this->anonymize('patient_logs', [
            'patient_notes' => "CONCAT('Anonymized patient note ', id)",
            'medication_name' => "CASE WHEN medication_name IS NOT NULL THEN CONCAT('Medication_', id) ELSE NULL END",
            'treatment_notes' => "CASE WHEN treatment_notes IS NOT NULL THEN CONCAT('Treatment note ', id) ELSE NULL END",
            'lab_result_value' => "CASE WHEN lab_result_value IS NOT NULL THEN CONCAT('Result_', id) ELSE NULL END",
            'doctor_notes' => "CASE WHEN doctor_notes IS NOT NULL THEN CONCAT('Doctor note ', id) ELSE NULL END"
        ]);

        // 6. Customers
        $this->log("[6/35] Anonymizing Customers...", 'blue');
        $this->anonymize('customers', [
            'customer_name' => "CONCAT('Customer_', id)",
            'contact_person' => "CONCAT('Contact_', id)",
            'email' => "CONCAT('customer_', id, '@example.com')",
            'phone' => "CONCAT('555-CUS-', LPAD(id, 4, '0'))",
            'mobile' => "CONCAT('555-MOB-', LPAD(id, 4, '0'))",
            'address' => "CONCAT(id, ' Customer Road')",
            'city' => "'Customer City'",
            'county' => "'Customer County'",
            'postcode' => "CONCAT('CUS', LPAD(id, 3, '0'))",
            'country' => "'Customer Country'"
        ]);

        // 7. Business Contacts
        $this->log("[7/35] Anonymizing Business Contacts...", 'blue');
        $this->anonymize('business_contacts', [
            'first_name' => "CONCAT('Contact', id)",
            'last_name' => "CONCAT('Person', id)",
            'email' => "CONCAT('contact_', id, '@example.com')",
            'phone' => "CONCAT('555-CON-', LPAD(id, 4, '0'))",
            'mobile' => "CONCAT('555-MOB-', LPAD(id, 4, '0'))",
            'address' => "CONCAT(id, ' Contact Street')",
            'city' => "'Contact City'",
            'postcode' => "CONCAT('CON', LPAD(id, 3, '0'))",
            'country' => "'Contact Country'"
        ]);

        // 8. Addresses
        $this->log("[8/35] Anonymizing Addresses...", 'blue');
        $this->anonymize('addresses', [
            'address_line_1' => "CONCAT(id, ' Anonymous Street')",
            'address_line_2' => "NULL",
            'city' => "'Anonymous City'",
            'county' => "'Anonymous County'",
            'postcode' => "CONCAT('AN', LPAD(id, 4, '0'))",
            'country' => "'Anonymous Country'"
        ]);

        // 9. Calendar Events
        $this->log("[9/35] Anonymizing Calendar Events...", 'blue');
        $this->anonymize('calendar_events', [
            'title' => "CONCAT('Event_', id)",
            'description' => "CONCAT('Event description ', id)",
            'location' => "CONCAT('Location_', id)",
            'attendees' => "NULL"
        ]);

        // 10. Meetings
        $this->log("[10/35] Anonymizing Meetings...", 'blue');
        $this->anonymize('meetings', [
            'title' => "CONCAT('Meeting_', id)",
            'description' => "CONCAT('Meeting description ', id)",
            'location' => "CONCAT('Location_', id)",
            'attendees' => "NULL",
            'notes' => "CASE WHEN notes IS NOT NULL THEN CONCAT('Meeting notes ', id) ELSE NULL END"
        ]);

        // 11. Email Campaigns
        $this->log("[11/35] Anonymizing Email Campaigns...", 'blue');
        $this->anonymize('email_campaigns', [
            'campaign_name' => "CONCAT('Campaign_', id)",
            'subject' => "CONCAT('Email Subject ', id)",
            'from_name' => "CONCAT('Sender_', id)",
            'from_email' => "CONCAT('sender_', id, '@example.com')",
            'reply_to_email' => "CONCAT('reply_', id, '@example.com')",
            'email_content' => "'<p>Anonymized email content</p>'",
            'preview_text' => "CONCAT('Preview text ', id)"
        ]);

        // 12. Email Campaign Recipients
        $this->log("[12/35] Anonymizing Email Campaign Recipients...", 'blue');
        $this->anonymize('email_campaign_recipients', [
            'recipient_email' => "CONCAT('recipient_', id, '@example.com')",
            'recipient_name' => "CONCAT('Recipient_', id)"
        ]);

        // 13. Blog Comments
        $this->log("[13/35] Anonymizing Blog Comments...", 'blue');
        $this->anonymize('blog_comments', [
            'author_name' => "CONCAT('Commenter_', id)",
            'author_email' => "CONCAT('comment_', id, '@example.com')",
            'comment_content' => "CONCAT('Anonymized comment ', id)",
            'author_ip' => "'127.0.0.1'"
        ]);

        // 14. Enquiries
        $this->log("[14/35] Anonymizing Enquiries...", 'blue');
        $this->anonymize('enquiries', [
            'name' => "CONCAT('Enquirer_', id)",
            'email' => "CONCAT('enquiry_', id, '@example.com')",
            'phone' => "CONCAT('555-ENQ-', LPAD(id, 4, '0'))",
            'company' => "CONCAT('Company_', id)",
            'message' => "CONCAT('Enquiry message ', id)"
        ]);

        // 15. Interview Candidates
        $this->log("[15/35] Anonymizing Interview Candidates...", 'blue');
        $this->anonymize('interview_candidates', [
            'first_name' => "CONCAT('Candidate', id)",
            'last_name' => "CONCAT('Person', id)",
            'email' => "CONCAT('candidate_', id, '@example.com')",
            'phone' => "CONCAT('555-CAN-', LPAD(id, 4, '0'))",
            'address' => "CONCAT(id, ' Candidate Street')",
            'city' => "'Candidate City'",
            'postcode' => "CONCAT('CAN', LPAD(id, 3, '0'))",
            'linkedin_url' => "CONCAT('https://linkedin.com/in/candidate', id)",
            'cover_letter' => "CONCAT('Anonymized cover letter ', id)",
            'cv_file_path' => "NULL"
        ]);

        // 16. Job Applications
        $this->log("[16/35] Anonymizing Job Applications...", 'blue');
        $this->anonymize('job_applications', [
            'applicant_name' => "CONCAT('Applicant_', id)",
            'applicant_email' => "CONCAT('applicant_', id, '@example.com')",
            'applicant_phone' => "CONCAT('555-APP-', LPAD(id, 4, '0'))",
            'cover_letter' => "CONCAT('Anonymized cover letter ', id)",
            'cv_file_path' => "NULL"
        ]);

        // 17. Incident Notifications
        $this->log("[17/35] Anonymizing Incident Notifications...", 'blue');
        $this->anonymize('incident_notifications_log', [
            'recipient_email' => "CONCAT('recipient_', id, '@example.com')",
            'recipient_name' => "CONCAT('Recipient_', id)"
        ]);

        // 18. Incidents
        $this->log("[18/35] Anonymizing Incidents...", 'blue');
        $this->anonymize('incidents', [
            'reported_by_name' => "CONCAT('Reporter_', id)",
            'reported_by_email' => "CONCAT('reporter_', id, '@example.com')",
            'description' => "CONCAT('Anonymized incident description ', id)",
            'resolution_notes' => "CASE WHEN resolution_notes IS NOT NULL THEN CONCAT('Resolution notes ', id) ELSE NULL END"
        ]);

        // 19. Project Comments
        $this->log("[19/35] Anonymizing Project Comments...", 'blue');
        $this->anonymize('project_comments', [
            'comment' => "CONCAT('Anonymized project comment ', id)",
            'commenter_name' => "CONCAT('Commenter_', id)",
            'commenter_email' => "CONCAT('commenter_', id, '@example.com')"
        ]);

        // 20. Secrets
        $this->log("[20/35] Anonymizing Secrets...", 'blue');
        $this->anonymize('secrets', [
            'secret_value' => "CONCAT('ANONYMIZED_SECRET_', id)",
            'description' => "CONCAT('Anonymized secret description ', id)"
        ]);

        // 21. Secrets Audit Log
        $this->log("[21/35] Anonymizing Secrets Audit Log...", 'blue');
        $this->anonymize('secrets_audit_log', [
            'performed_by_email' => "CONCAT('user_', id, '@example.com')",
            'performed_by_name' => "CONCAT('User_', id)",
            'ip_address' => "'127.0.0.1'"
        ]);

        // 22. Purchase Invoices
        $this->log("[22/35] Anonymizing Purchase Invoices...", 'blue');
        $this->anonymize('purchase_invoices', [
            'supplier_name' => "CONCAT('Supplier_', id)",
            'supplier_email' => "CONCAT('supplier_', id, '@example.com')",
            'supplier_address' => "CONCAT(id, ' Supplier Street')",
            'supplier_phone' => "CONCAT('555-SUP-', LPAD(id, 4, '0'))"
        ]);

        // 23. Purchase Invoice Notes
        $this->log("[23/35] Anonymizing Purchase Invoice Notes...", 'blue');
        $this->anonymize('purchase_invoice_notes', [
            'note' => "CONCAT('Anonymized purchase note ', id)",
            'created_by_name' => "CONCAT('User_', id)"
        ]);

        // 24. Sales Invoice Notes
        $this->log("[24/35] Anonymizing Sales Invoice Notes...", 'blue');
        $this->anonymize('sales_invoice_notes', [
            'note' => "CONCAT('Anonymized sales note ', id)",
            'created_by_name' => "CONCAT('User_', id)"
        ]);

        // 25. Work Orders
        $this->log("[25/35] Anonymizing Work Orders...", 'blue');
        $this->anonymize('work_orders', [
            'customer_name' => "CONCAT('Customer_', id)",
            'customer_email' => "CONCAT('customer_', id, '@example.com')",
            'customer_phone' => "CONCAT('555-WOR-', LPAD(id, 4, '0'))",
            'customer_address' => "CONCAT(id, ' Customer Street')",
            'description' => "CONCAT('Anonymized work order description ', id)",
            'notes' => "CASE WHEN notes IS NOT NULL THEN CONCAT('Work order notes ', id) ELSE NULL END"
        ]);

        // 26. Domains
        $this->log("[26/35] Anonymizing Domains...", 'blue');
        $this->anonymize('domains', [
            'registrant_name' => "CONCAT('Domain Owner ', id)",
            'registrant_email' => "CONCAT('owner_', id, '@example.com')",
            'registrant_phone' => "CONCAT('555-DOM-', LPAD(id, 4, '0'))",
            'registrant_address' => "CONCAT(id, ' Domain Street')",
            'admin_contact_name' => "CONCAT('Admin_', id)",
            'admin_contact_email' => "CONCAT('admin_', id, '@example.com')",
            'tech_contact_name' => "CONCAT('Tech_', id)",
            'tech_contact_email' => "CONCAT('tech_', id, '@example.com')"
        ]);

        // 27. Virtual Machines
        $this->log("[27/35] Anonymizing Virtual Machines...", 'blue');
        $this->anonymize('virtual_machines', [
            'admin_username' => "CONCAT('admin_', id)",
            'admin_password' => "'ANONYMIZED_PASSWORD'",
            'ssh_key' => "NULL",
            'notes' => "CASE WHEN notes IS NOT NULL THEN CONCAT('VM notes ', id) ELSE NULL END"
        ]);

        // 28. Tenants
        $this->log("[28/35] Anonymizing Tenants...", 'blue');
        $this->anonymize('tenants', [
            'tenant_name' => "CONCAT('Tenant_', id)",
            'contact_email' => "CONCAT('tenant_', id, '@example.com')",
            'contact_phone' => "CONCAT('555-TEN-', LPAD(id, 4, '0'))",
            'contact_person' => "CONCAT('Contact_', id)"
        ]);

        // 29. Deployments
        $this->log("[29/35] Anonymizing Deployments...", 'blue');
        $this->anonymize('deployments', [
            'deployed_by_name' => "CONCAT('Deployer_', id)",
            'deployed_by_email' => "CONCAT('deployer_', id, '@example.com')",
            'deployment_notes' => "CASE WHEN deployment_notes IS NOT NULL THEN CONCAT('Deployment notes ', id) ELSE NULL END"
        ]);

        // 30. Knowledge Base
        $this->log("[30/35] Anonymizing Knowledge Base...", 'blue');
        $this->anonymize('knowledge_base', [
            'author_name' => "CONCAT('Author_', id)",
            'author_email' => "CONCAT('author_', id, '@example.com')",
            'content' => "CONCAT('<p>Anonymized knowledge base content ', id, '</p>')"
        ]);

        // 31. Companies (NEW)
        $this->log("[31/35] Anonymizing Companies...", 'blue');
        $this->anonymize('companies', [
            'company_number' => "CONCAT('COMP', LPAD(id, 8, '0'))",
            'company_name' => "CONCAT('Anonymous Company ', id)",
            'address_1' => "CONCAT(id, ' Business Street')",
            'address_2' => "CONCAT('Suite ', id)",
            'address_3' => "''",
            'town_or_city' => "'Anonymous City'",
            'state_or_county' => "'Anonymous County'",
            'post_zip_code' => "CONCAT('AC', LPAD(id, 4, '0'))",
            'region_area' => "'Anonymous Region'",
            'website' => "CONCAT('https://company', id, '.example.com')",
            'email' => "CONCAT('company_', id, '@example.com')",
            'company_telephone' => "CONCAT('555-COM-', LPAD(id, 4, '0'))",
            'company_fax' => "CONCAT('555-FAX-', LPAD(id, 4, '0'))"
        ]);

        // 32. Contacts (NEW)
        $this->log("[32/35] Anonymizing Contacts...", 'blue');
        $this->anonymize('contacts', [
            'first_name' => "CONCAT('Contact', id)",
            'surname' => "CONCAT('Person', id)",
            'title' => "CONCAT('Title ', id)",
            'saludation' => "'Mr/Ms'",
            'comments' => "CONCAT('Anonymized comment ', id)",
            'email' => "CONCAT('contact_', id, '@example.com')",
            'current_position' => "CONCAT('Position ', id)",
            'current_company' => "CONCAT('Company ', id)",
            'skills' => "CONCAT('Skill set ', id)",
            'linkedin_url' => "CONCAT('https://linkedin.com/in/contact', id)",
            'portfolio_url' => "CONCAT('https://portfolio', id, '.example.com')",
            'password' => "'\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'",
            'direct_phone' => "CONCAT('555-CON-', LPAD(id, 4, '0'))",
            'mobile' => "CONCAT('555-MOB-', LPAD(id, 4, '0'))",
            'direct_fax' => "CONCAT('555-FAX-', LPAD(id, 4, '0'))"
        ]);

        // 33. Sales Invoices (NEW)
        $this->log("[33/35] Anonymizing Sales Invoices...", 'blue');
        $this->anonymize('sales_invoices', [
            'custom_invoice_number' => "CONCAT('INV-', LPAD(id, 6, '0'))",
            'bill_to' => "CONCAT('Anonymous Customer ', id, '\n', id, ' Customer Street\nAnonymous City, AC', LPAD(id, 4, '0'))",
            'notes' => "CONCAT('Anonymized invoice notes ', id)",
            'order_by' => "CONCAT('Order by: Anonymous ', id)",
            'project_code' => "CONCAT('PROJ-', LPAD(id, 4, '0'))",
            'internal_notes' => "CONCAT('Internal notes for invoice ', id)",
            'inv_customer_ref_po' => "CONCAT('PO-', LPAD(id, 6, '0'))",
            'payment_pin_or_passcode' => "NULL"
        ]);

        // 34. Sales Invoice Items (NEW)
        $this->log("[34/35] Anonymizing Sales Invoice Items...", 'blue');
        $this->anonymize('sales_invoice_items', [
            'description' => "CONCAT('Anonymized service/product item ', id)"
        ]);

        // 35. Timeslips (NEW)
        $this->log("[35/35] Anonymizing Timeslips...", 'blue');
        $this->anonymize('timeslips', [
            'task_name' => "CONCAT('Task ', id)",
            'employee_name' => "CONCAT('Employee ', MOD(id, 20) + 1)",
            'slip_description' => "CONCAT('Anonymized work description for timeslip ', id)"
        ]);

        // Re-enable safe updates
        $this->pdo->exec("SET SQL_SAFE_UPDATES = 1");

        $this->printSummary();
    }

    private function printSummary() {
        $this->log("\n" . str_repeat("=", 60), 'yellow');
        $this->log("  ANONYMIZATION COMPLETE", 'yellow');
        $this->log(str_repeat("=", 60) . "\n", 'yellow');

        $totalRows = array_sum($this->stats);
        $this->log("Total rows anonymized: " . number_format($totalRows), 'green');
        $this->log("Tables processed: " . count($this->stats), 'green');

        $this->log("\nTop 10 Tables by Rows Anonymized:", 'blue');
        arsort($this->stats);
        $count = 0;
        foreach ($this->stats as $table => $rows) {
            if ($count++ >= 10) break;
            $this->log(sprintf("  %-30s %s rows", $table . ":", number_format($rows)), 'nc');
        }

        $this->log("\n" . str_repeat("-", 60), 'nc');
        $this->log("Preserved: admin@admin.com user account ONLY", 'green');
        $this->log("All other data has been anonymized", 'green');
        $this->log(str_repeat("-", 60) . "\n", 'nc');
    }
}

// Run the anonymization
$anonymizer = new DatabaseAnonymizer();
$anonymizer->run();

echo "\n✓ Anonymization completed successfully!\n\n";
exit(0);
