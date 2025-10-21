<?php
$host = 'workerra-ci-db';
$dbname = 'myworkstation_dev';
$username = 'workerra-ci-dev';
$password = 'CHANGE_ME';

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Connected successfully\n";

// Test single insert
$sql = "INSERT INTO incidents (
    uuid, uuid_business_id, title, description, incident_number,
    priority, status, category, assigned_to, reporter_id, customer_id,
    reported_date, due_date, impact, urgency, related_kb_id, tags, created_at
) VALUES (
    'TEST-001', 'BUS-001', 'Test Incident', 'Test Description', 'INC-000001',
    'high', 'new', 'Hardware', 1, 1, 1,
    NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 'medium', 'high', 1, 'test,hardware', NOW()
)";

if ($mysqli->query($sql)) {
    echo "Test record inserted successfully\n";
} else {
    echo "Error: " . $mysqli->error . "\n";
}

$mysqli->close();
