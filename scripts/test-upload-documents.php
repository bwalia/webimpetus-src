<?php
/**
 * Test Script: Upload Sample Documents to MinIO via API
 *
 * This script demonstrates uploading sample files using the Documents API Client
 */

require_once __DIR__ . '/DocumentsApiClient.php';

// Configuration
$BASE_URL = 'http://localhost:5500';
$BUSINESS_UUID = '0f6c4e64-9b50-5e11-a7d1-1923b7aef282'; // Work Bench Ltd UK
// $BUSINESS_UUID = '329e0405-b544-5051-8d37-d0143e9c8829'; // EuropaTech BE (alternative)

$SAMPLE_FILES_DIR = __DIR__ . '/sample-files';

// Initialize API client
$client = new DocumentsApiClient($BASE_URL);

echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║      Documents API Upload Test - MinIO Integration          ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

echo "Configuration:\n";
echo "  Base URL: $BASE_URL\n";
echo "  Business: $BUSINESS_UUID\n";
echo "  Sample Files: $SAMPLE_FILES_DIR\n\n";

// Test 1: Upload sample image
echo "═══════════════════════════════════════════════════════════════\n";
echo "Test 1: Uploading Sample Image (JPG)\n";
echo "═══════════════════════════════════════════════════════════════\n";

$imagePath = $SAMPLE_FILES_DIR . '/sample-image.jpg';
if (file_exists($imagePath)) {
    echo "File: $imagePath\n";
    echo "Size: " . filesize($imagePath) . " bytes\n";
    echo "Uploading...\n\n";

    $result = $client->uploadDocument(
        $imagePath,
        $BUSINESS_UUID,
        'Sample Landscape Image',
        'Beautiful landscape photo uploaded via API to test MinIO integration',
        [
            'document_date' => date('Y-m-d'),
            'metadata' => json_encode(['source' => 'API test', 'type' => 'image'])
        ]
    );

    if ($result['success']) {
        echo "✅ SUCCESS!\n";
        echo "  HTTP Code: " . $result['http_code'] . "\n";
        echo "  Document UUID: " . ($result['document_uuid'] ?? 'N/A') . "\n";
        echo "  MinIO URL: " . ($result['minio_url'] ?? 'N/A') . "\n";
        echo "  Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "❌ FAILED!\n";
        echo "  Error: " . ($result['error'] ?? 'Unknown error') . "\n";
        echo "  HTTP Code: " . ($result['http_code'] ?? 'N/A') . "\n";
    }
} else {
    echo "❌ File not found: $imagePath\n";
}

echo "\n";

// Test 2: Upload sample PDF
echo "═══════════════════════════════════════════════════════════════\n";
echo "Test 2: Uploading Sample PDF\n";
echo "═══════════════════════════════════════════════════════════════\n";

$pdfPath = $SAMPLE_FILES_DIR . '/sample-pdf.pdf';
if (file_exists($pdfPath)) {
    echo "File: $pdfPath\n";
    echo "Size: " . filesize($pdfPath) . " bytes\n";
    echo "Uploading...\n\n";

    $result = $client->uploadDocument(
        $pdfPath,
        $BUSINESS_UUID,
        'Sample PDF Document',
        'Test PDF document uploaded via API to verify MinIO storage',
        [
            'document_date' => date('Y-m-d'),
            'billing_status' => 'unbilled',
            'metadata' => json_encode(['source' => 'API test', 'type' => 'pdf', 'pages' => 1])
        ]
    );

    if ($result['success']) {
        echo "✅ SUCCESS!\n";
        echo "  HTTP Code: " . $result['http_code'] . "\n";
        echo "  Document UUID: " . ($result['document_uuid'] ?? 'N/A') . "\n";
        echo "  MinIO URL: " . ($result['minio_url'] ?? 'N/A') . "\n";
        echo "  Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "❌ FAILED!\n";
        echo "  Error: " . ($result['error'] ?? 'Unknown error') . "\n";
        echo "  HTTP Code: " . ($result['http_code'] ?? 'N/A') . "\n";
    }
} else {
    echo "❌ File not found: $pdfPath\n";
}

echo "\n";

// Test 3: Get documents list
echo "═══════════════════════════════════════════════════════════════\n";
echo "Test 3: Retrieving Documents List\n";
echo "═══════════════════════════════════════════════════════════════\n";

$documents = $client->getDocuments($BUSINESS_UUID, ['perPage' => 5]);

if (isset($documents['data'])) {
    echo "✅ Retrieved " . count($documents['data']) . " documents\n";
    echo "Total: " . ($documents['total'] ?? 0) . "\n\n";

    foreach ($documents['data'] as $index => $doc) {
        echo "Document " . ($index + 1) . ":\n";
        echo "  UUID: " . ($doc['uuid'] ?? 'N/A') . "\n";
        echo "  Name: " . ($doc['name'] ?? 'N/A') . "\n";
        echo "  File: " . ($doc['file'] ?? 'N/A') . "\n";
        echo "  Created: " . ($doc['created_at'] ?? 'N/A') . "\n";
        echo "\n";
    }
} else {
    echo "❌ Failed to retrieve documents\n";
    echo "Response: " . json_encode($documents, JSON_PRETTY_PRINT) . "\n";
}

echo "\n";

// Summary
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║                        Test Summary                           ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

echo "✅ Tests completed!\n\n";

echo "Next Steps:\n";
echo "  1. Verify files in MinIO Console: http://localhost:9001\n";
echo "  2. Login: minioadmin / minioadmin123\n";
echo "  3. Navigate to: Buckets → workerra-ci → dev/documents/\n";
echo "  4. Check database records:\n";
echo "     SELECT uuid, name, file, file_url FROM documents ORDER BY created_at DESC LIMIT 5;\n\n";

echo "MinIO Storage Path:\n";
echo "  - Host: /home/bwalia/workerra-ci/minio-data/workerra-ci/dev/documents/\n";
echo "  - MinIO: http://minio:9000/workerra-ci/dev/documents/\n\n";
