<?php

/**
 * Generate Test Domain Records using MySQLi
 * Creates 3000 test domain records for pagination testing
 */

echo "=== Domain Test Data Generator ===\n\n";

// Database connection
$mysqli = new mysqli('webimpetus-db', 'wsl_dev', 'CHANGE_ME', 'myworkstation_dev');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error . "\n");
}

echo "✓ Connected to database\n";

// Get Flights For Me UK Ltd business UUID
$result = $mysqli->query("SELECT uuid, name FROM businesses WHERE name = 'Flights For Me UK Ltd' LIMIT 1");
$business = $result ? $result->fetch_assoc() : null;
$businessUuid = $business['uuid'] ?? null;

if (!$businessUuid) {
    die("✗ Error: Could not find 'Flights For Me UK Ltd' business\n");
}

echo "✓ Business: {$business['name']}\n";
echo "✓ Business UUID: {$businessUuid}\n";

// Get sample customer and service for this business
$result = $mysqli->query("SELECT uuid FROM customers WHERE uuid_business_id = '{$businessUuid}' LIMIT 1");
$customer = $result ? $result->fetch_assoc() : null;
$customerUuid = $customer['uuid'] ?? null;

$result = $mysqli->query("SELECT uuid FROM services LIMIT 1");
$service = $result ? $result->fetch_assoc() : null;
$serviceUuid = $service['uuid'] ?? null;

echo "✓ Customer UUID: " . ($customerUuid ?? 'Will be generated') . "\n";
echo "✓ Service UUID: " . ($serviceUuid ?? 'NULL') . "\n\n";

// Domain components
$prefixes = ['web', 'app', 'api', 'dev', 'test', 'staging', 'prod', 'demo', 'portal', 'admin',
             'shop', 'store', 'blog', 'news', 'media', 'cloud', 'data', 'tech', 'digital'];

$extensions = ['com', 'net', 'org', 'io', 'co', 'app', 'dev', 'tech', 'cloud'];

$pathTypes = ['prefix', 'exact', 'regex'];
$paths = ['api', 'app', 'web', 'portal', 'admin', 'v1', 'v2', 'public'];

echo "Generating 3000 test domains...\n\n";

$mysqli->begin_transaction();

$created = 0;
$errors = 0;

for ($i = 1; $i <= 3000; $i++) {
    // Generate domain name
    $prefix = $prefixes[array_rand($prefixes)];
    $number = rand(1000, 9999);
    $ext = $extensions[array_rand($extensions)];
    $domainName = "test-{$prefix}-{$number}.{$ext}";

    // Generate UUID
    $domainUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );

    // Random values
    $pathType = $pathTypes[array_rand($pathTypes)];
    $path = '/' . $paths[array_rand($paths)];
    $serviceName = 'service-' . rand(1, 100);
    $servicePort = rand(3000, 9999);
    $sid = $serviceUuid ? "'{$mysqli->real_escape_string($serviceUuid)}'" : 'NULL';
    $custUuid = $customerUuid ? "'{$mysqli->real_escape_string($customerUuid)}'" : "'cust-{$i}'";
    $notes = $mysqli->real_escape_string("Test domain #{$i} - Generated for pagination testing");

    // Insert domain
    $sql = "INSERT INTO domains (
        uuid, customer_uuid, sid, name, notes, uuid_business_id,
        domain_path, domain_path_type, domain_service_name, domain_service_port
    ) VALUES (
        '{$domainUuid}', {$custUuid}, {$sid}, '{$domainName}', '{$notes}', '{$businessUuid}',
        '{$path}', '{$pathType}', '{$serviceName}', {$servicePort}
    )";

    if ($mysqli->query($sql)) {
        $created++;

        // Insert service association
        if ($serviceUuid) {
            $assocUuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            $mysqli->query("INSERT INTO service__domains (uuid, service_uuid, domain_uuid)
                           VALUES ('{$assocUuid}', '{$serviceUuid}', '{$domainUuid}')");
        }
    } else {
        $errors++;
    }

    // Progress indicator
    if ($i % 100 == 0) {
        echo "  Created {$i} domains...\n";
    }
}

$mysqli->commit();

echo "\n✓ Successfully created {$created} test domain records!\n";
if ($errors > 0) {
    echo "✗ {$errors} errors occurred\n";
}

// Show summary
$result = $mysqli->query("SELECT COUNT(*) as cnt FROM domains");
$total = $result->fetch_assoc();

$result = $mysqli->query("SELECT COUNT(*) as cnt FROM domains WHERE name LIKE 'test-%'");
$testCount = $result->fetch_assoc();

echo "\n=== Summary ===\n";
echo "Total domains in database: {$total['cnt']}\n";
echo "Test domains created: {$testCount['cnt']}\n\n";

// Show samples
echo "=== Sample Test Domains ===\n";
$result = $mysqli->query("
    SELECT id, name, domain_path_type, domain_service_port
    FROM domains
    WHERE name LIKE 'test-%'
    ORDER BY id DESC
    LIMIT 5
");

while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']} | {$row['name']} | Type: {$row['domain_path_type']} | Port: {$row['domain_service_port']}\n";
}

echo "\n✓ Done! Test at: http://localhost:8080/domains\n";

$mysqli->close();
