<?php

/**
 * Generate Test Domain Records
 *
 * This script creates 3000 test domain records for testing pagination
 * and performance of the domains module.
 *
 * Usage: php generate_test_domains.php
 */

// Database configuration
$host = 'workerra-ci-db';
$dbname = 'myworkstation_dev';
$username = 'workerra-ci-dev';
$password = 'CHANGE_ME';

// Use mysqli instead of PDO
$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error . "\n");
}

echo "Connected to database successfully.\n";

try {

    // Get sample customer and service UUIDs
    $customerStmt = $pdo->query("SELECT uuid FROM customers LIMIT 1");
    $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);
    $customerUuid = $customer ? $customer['uuid'] : null;

    $serviceStmt = $pdo->query("SELECT uuid FROM services LIMIT 1");
    $service = $serviceStmt->fetch(PDO::FETCH_ASSOC);
    $serviceUuid = $service ? $service['uuid'] : null;

    echo "Customer UUID: " . ($customerUuid ?? 'NULL') . "\n";
    echo "Service UUID: " . ($serviceUuid ?? 'NULL') . "\n\n";

    // Domain name components
    $prefixes = ['web', 'app', 'api', 'dev', 'test', 'staging', 'prod', 'demo', 'portal', 'admin',
                 'shop', 'store', 'blog', 'news', 'media', 'cloud', 'data', 'tech', 'digital', 'online',
                 'mobile', 'secure', 'fast', 'smart', 'pro', 'biz', 'hub', 'net', 'link', 'site',
                 'page', 'host', 'code', 'soft', 'info', 'corp', 'global', 'local', 'myapp', 'mysite',
                 'webapp', 'platform', 'service', 'system', 'server', 'client'];

    $extensions = ['com', 'net', 'org', 'io', 'co', 'app', 'dev', 'tech', 'cloud', 'digital',
                   'online', 'site', 'store', 'shop', 'biz', 'info', 'uk', 'us', 'ca', 'au'];

    $pathTypes = ['prefix', 'exact', 'regex'];
    $paths = ['api', 'app', 'web', 'portal', 'admin', 'v1', 'v2', 'public', 'private', 'internal'];

    // Prepare insert statements
    $domainStmt = $pdo->prepare("
        INSERT INTO domains (
            uuid, customer_uuid, sid, name, notes, uuid_business_id,
            domain_path, domain_path_type, domain_service_name, domain_service_port
        ) VALUES (
            :uuid, :customer_uuid, :sid, :name, :notes, NULL,
            :domain_path, :domain_path_type, :domain_service_name, :domain_service_port
        )
    ");

    $serviceAssocStmt = null;
    if ($serviceUuid) {
        $serviceAssocStmt = $pdo->prepare("
            INSERT INTO service__domains (uuid, service_uuid, domain_uuid)
            VALUES (:uuid, :service_uuid, :domain_uuid)
        ");
    }

    $pdo->beginTransaction();

    $created = 0;
    $batchSize = 100;

    echo "Generating 3000 test domain records...\n";

    for ($i = 1; $i <= 3000; $i++) {
        // Generate unique domain name
        $prefix = $prefixes[array_rand($prefixes)];
        $number = rand(1000, 9999);
        $ext = $extensions[array_rand($extensions)];
        $domainName = "test-{$prefix}-{$number}.{$ext}";

        // Generate UUID (simple version)
        $domainUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        // Random values
        $pathType = $pathTypes[array_rand($pathTypes)];
        $path = '/' . $paths[array_rand($paths)];
        $serviceName = 'service-' . rand(1, 100);
        $servicePort = rand(3000, 9999);

        // Insert domain
        $domainStmt->execute([
            ':uuid' => $domainUuid,
            ':customer_uuid' => $customerUuid ?? "cust-{$i}",
            ':sid' => $serviceUuid ? json_encode([$serviceUuid]) : null,
            ':name' => $domainName,
            ':notes' => "Test domain #{$i} - Generated for testing pagination and performance",
            ':domain_path' => $path,
            ':domain_path_type' => $pathType,
            ':domain_service_name' => $serviceName,
            ':domain_service_port' => $servicePort
        ]);

        // Insert service association
        if ($serviceAssocStmt && $serviceUuid) {
            $assocUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            $serviceAssocStmt->execute([
                ':uuid' => $assocUuid,
                ':service_uuid' => $serviceUuid,
                ':domain_uuid' => $domainUuid
            ]);
        }

        $created++;

        // Commit every batch
        if ($i % $batchSize == 0) {
            $pdo->commit();
            $pdo->beginTransaction();
            echo "Created {$i} domains...\n";
        }
    }

    $pdo->commit();

    echo "\n✓ Successfully created {$created} test domain records!\n\n";

    // Show summary
    $stats = $pdo->query("
        SELECT
            COUNT(*) as total_domains,
            COUNT(DISTINCT name) as unique_names,
            MIN(id) as first_id,
            MAX(id) as last_id
        FROM domains
    ")->fetch(PDO::FETCH_ASSOC);

    echo "=== Database Summary ===\n";
    echo "Total domains: {$stats['total_domains']}\n";
    echo "Unique names: {$stats['unique_names']}\n";
    echo "ID range: {$stats['first_id']} - {$stats['last_id']}\n\n";

    // Show test domains count
    $testCount = $pdo->query("SELECT COUNT(*) as cnt FROM domains WHERE name LIKE 'test-%'")->fetch(PDO::FETCH_ASSOC);
    echo "Test domains created: {$testCount['cnt']}\n\n";

    // Show sample records
    echo "=== Sample Test Domains ===\n";
    $samples = $pdo->query("
        SELECT id, name, domain_path_type, domain_service_port
        FROM domains
        WHERE name LIKE 'test-%'
        ORDER BY id DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($samples as $sample) {
        echo "ID: {$sample['id']} | {$sample['name']} | Type: {$sample['domain_path_type']} | Port: {$sample['domain_service_port']}\n";
    }

    echo "\n✓ Test data generation complete!\n";
    echo "You can now test pagination at: http://localhost:8080/domains\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    exit(1);
}
