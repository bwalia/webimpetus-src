<?php
/**
 * Script to insert 1000 sample incidents and knowledge base records
 */

// Database configuration
$host = 'workerra-ci-db';
$dbname = 'myworkstation_dev';
$username = 'workerra-ci-dev';
$password = 'CHANGE_ME';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully\n";

    // Sample data arrays
    $priorities = ['low', 'medium', 'high', 'critical'];
    $statuses = ['new', 'assigned', 'in_progress', 'pending', 'resolved', 'closed'];
    $impacts = ['low', 'medium', 'high', 'critical'];
    $urgencies = ['low', 'medium', 'high', 'critical'];
    $kbStatuses = ['draft', 'published', 'archived'];
    $kbVisibilities = ['public', 'internal', 'private'];

    $incidentCategories = [
        'Hardware', 'Software', 'Network', 'Security', 'Access', 'Email',
        'Database', 'Application', 'Performance', 'Backup', 'Recovery',
        'User Account', 'Printer', 'Mobile Device', 'VPN'
    ];

    $kbCategories = [
        'Troubleshooting', 'How-To', 'Best Practices', 'Security',
        'Installation', 'Configuration', 'FAQ', 'Policy', 'Procedure',
        'Tutorial', 'Quick Start', 'Reference', 'Integration'
    ];

    $incidentTitles = [
        'Unable to access email',
        'System running slow',
        'Network connectivity issues',
        'Application crash on startup',
        'Password reset request',
        'Printer not working',
        'VPN connection failed',
        'Database timeout errors',
        'Software installation required',
        'File sharing permissions issue',
        'Browser compatibility problem',
        'Mobile app sync issue',
        'Server downtime reported',
        'Security alert triggered',
        'Backup job failed',
        'Disk space warning',
        'Login authentication error',
        'API integration failure',
        'Performance degradation',
        'Certificate expiration notice'
    ];

    $kbTitles = [
        'How to reset your password',
        'Troubleshooting network connectivity',
        'Email configuration guide',
        'VPN setup instructions',
        'Security best practices',
        'Database backup procedures',
        'Software installation steps',
        'Mobile device management',
        'Printer setup and configuration',
        'File sharing permissions guide',
        'Application performance optimization',
        'Browser compatibility solutions',
        'API integration tutorial',
        'Disaster recovery procedures',
        'User account management',
        'System maintenance checklist',
        'Security incident response',
        'Data encryption guidelines',
        'Backup and restore process',
        'Network troubleshooting guide'
    ];

    $descriptions = [
        'User reported experiencing intermittent issues with system functionality.',
        'Critical issue affecting multiple users in the organization.',
        'Minor inconvenience that requires attention when time permits.',
        'System behaving unexpectedly and requires immediate investigation.',
        'Standard request following established protocols and procedures.',
        'Issue identified during routine monitoring and maintenance.',
        'Emergency situation requiring immediate response and resolution.',
        'Follow-up required from previous incident to ensure complete resolution.',
        'User unable to complete essential business tasks.',
        'Potential security concern that needs verification and action.'
    ];

    $resolutionNotes = [
        'Issue resolved by restarting the service and clearing cache.',
        'Problem fixed after updating to the latest version.',
        'Resolved by resetting user credentials and permissions.',
        'Fixed configuration settings and tested functionality.',
        'Escalated to vendor support and received patch.',
        'Implemented workaround while permanent fix is in development.',
        'User error - provided training and documentation.',
        'Hardware replaced and system functioning normally.',
        'Network route updated and connectivity restored.',
        'Security patch applied and system verified secure.'
    ];

    $kbContent = [
        'This article provides step-by-step instructions to resolve common issues. Follow the procedures carefully and contact support if problems persist.',
        'Best practices and recommendations based on industry standards and organizational experience. Regular review and updates ensure continued relevance.',
        'Detailed troubleshooting guide covering various scenarios. Use the flowchart to identify the most likely cause of the issue.',
        'Quick reference guide for frequently performed tasks. Keep this document handy for efficient problem resolution.',
        'Comprehensive tutorial covering all aspects of the topic. Includes screenshots and examples for clarity.',
        'Security guidelines and compliance requirements. Mandatory reading for all staff members.',
        'Standard operating procedure approved by management. Deviation requires documented approval.',
        'Technical reference documentation for advanced users. Assumes familiarity with underlying concepts.',
        'Getting started guide for new users. Covers basic functionality and common workflows.',
        'Integration guide with third-party systems. Includes API endpoints and authentication methods.'
    ];

    echo "Starting to insert incidents...\n";

    // Insert 1000 incidents
    $incidentStmt = $pdo->prepare("
        INSERT INTO incidents (
            uuid, uuid_business_id, title, description, incident_number,
            priority, status, category, assigned_to, reporter_id, customer_id,
            reported_date, due_date, resolved_date, resolution_notes,
            impact, urgency, related_kb_id, tags, created_at
        ) VALUES (
            :uuid, :uuid_business_id, :title, :description, :incident_number,
            :priority, :status, :category, :assigned_to, :reporter_id, :customer_id,
            :reported_date, :due_date, :resolved_date, :resolution_notes,
            :impact, :urgency, :related_kb_id, :tags, :created_at
        )
    ");

    for ($i = 1; $i <= 1000; $i++) {
        $uuid = sprintf('INC-%s-%04d', date('Ymd'), $i);
        $incidentNumber = sprintf('INC-%06d', $i);
        $priority = $priorities[array_rand($priorities)];
        $status = $statuses[array_rand($statuses)];
        $impact = $impacts[array_rand($impacts)];
        $urgency = $urgencies[array_rand($urgencies)];
        $category = $incidentCategories[array_rand($incidentCategories)];

        $reportedDate = date('Y-m-d H:i:s', strtotime('-' . rand(1, 90) . ' days'));
        $dueDate = date('Y-m-d H:i:s', strtotime($reportedDate . ' +' . rand(1, 30) . ' days'));

        $resolvedDate = null;
        $resolutionNote = null;
        if (in_array($status, ['resolved', 'closed'])) {
            $resolvedDate = date('Y-m-d H:i:s', strtotime($reportedDate . ' +' . rand(1, 14) . ' days'));
            $resolutionNote = $resolutionNotes[array_rand($resolutionNotes)];
        }

        $tags = implode(',', array_slice($incidentCategories, rand(0, 5), rand(2, 4)));

        $incidentStmt->execute([
            ':uuid' => $uuid,
            ':uuid_business_id' => 'BUS-' . str_pad(rand(1, 10), 3, '0', STR_PAD_LEFT),
            ':title' => $incidentTitles[array_rand($incidentTitles)],
            ':description' => $descriptions[array_rand($descriptions)] . ' ' . $descriptions[array_rand($descriptions)],
            ':incident_number' => $incidentNumber,
            ':priority' => $priority,
            ':status' => $status,
            ':category' => $category,
            ':assigned_to' => rand(1, 20),
            ':reporter_id' => rand(1, 50),
            ':customer_id' => rand(1, 100),
            ':reported_date' => $reportedDate,
            ':due_date' => $dueDate,
            ':resolved_date' => $resolvedDate,
            ':resolution_notes' => $resolutionNote,
            ':impact' => $impact,
            ':urgency' => $urgency,
            ':related_kb_id' => rand(1, 50),
            ':tags' => $tags,
            ':created_at' => $reportedDate
        ]);

        if ($i % 100 == 0) {
            echo "Inserted $i incidents...\n";
        }
    }

    echo "Successfully inserted 1000 incidents!\n\n";
    echo "Starting to insert knowledge base articles...\n";

    // Insert 1000 knowledge base articles
    $kbStmt = $pdo->prepare("
        INSERT INTO knowledge_base (
            uuid, uuid_business_id, title, content, article_number,
            category, keywords, status, author_id, visibility,
            helpful_count, view_count, tags, published_date, created_at
        ) VALUES (
            :uuid, :uuid_business_id, :title, :content, :article_number,
            :category, :keywords, :status, :author_id, :visibility,
            :helpful_count, :view_count, :tags, :published_date, :created_at
        )
    ");

    for ($i = 1; $i <= 1000; $i++) {
        $uuid = sprintf('KB-%s-%04d', date('Ymd'), $i);
        $articleNumber = sprintf('KB-%06d', $i);
        $kbStatus = $kbStatuses[array_rand($kbStatuses)];
        $visibility = $kbVisibilities[array_rand($kbVisibilities)];
        $category = $kbCategories[array_rand($kbCategories)];

        $createdDate = date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days'));

        $publishedDate = null;
        if ($kbStatus === 'published') {
            $publishedDate = date('Y-m-d H:i:s', strtotime($createdDate . ' +' . rand(1, 7) . ' days'));
        }

        $keywords = implode(',', array_slice($kbCategories, rand(0, 5), rand(3, 5)));
        $tags = implode(',', array_slice($incidentCategories, rand(0, 5), rand(2, 4)));

        $kbStmt->execute([
            ':uuid' => $uuid,
            ':uuid_business_id' => 'BUS-' . str_pad(rand(1, 10), 3, '0', STR_PAD_LEFT),
            ':title' => $kbTitles[array_rand($kbTitles)],
            ':content' => $kbContent[array_rand($kbContent)] . "\n\n" . $kbContent[array_rand($kbContent)],
            ':article_number' => $articleNumber,
            ':category' => $category,
            ':keywords' => $keywords,
            ':status' => $kbStatus,
            ':author_id' => rand(1, 20),
            ':visibility' => $visibility,
            ':helpful_count' => rand(0, 500),
            ':view_count' => rand(0, 5000),
            ':tags' => $tags,
            ':published_date' => $publishedDate,
            ':created_at' => $createdDate
        ]);

        if ($i % 100 == 0) {
            echo "Inserted $i knowledge base articles...\n";
        }
    }

    echo "Successfully inserted 1000 knowledge base articles!\n\n";

    // Summary
    $incidentCount = $pdo->query("SELECT COUNT(*) FROM incidents")->fetchColumn();
    $kbCount = $pdo->query("SELECT COUNT(*) FROM knowledge_base")->fetchColumn();

    echo "=== Summary ===\n";
    echo "Total incidents in database: $incidentCount\n";
    echo "Total knowledge base articles in database: $kbCount\n";
    echo "\nData insertion completed successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
