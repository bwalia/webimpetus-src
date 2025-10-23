#!/usr/bin/env php
<?php

// Standalone SQL runner using mysqli
$host = getenv('DB_HOST') ?: 'workerra-ci-db';
$db = getenv('DB_DATABASE') ?: 'myworkstation_dev';
$user = getenv('DB_USERNAME') ?: 'myworkstation_dev';
$pass = getenv('DB_PASSWORD') ?: 'myworkstation_dev';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("✗ Database connection failed: " . $conn->connect_error . "\n");
}

echo "✓ Connected to database\n\n";

// Read and execute each SQL file
$files = [
    'create_project_jobs_table.sql',
    'create_project_job_phases_table.sql',
    'create_project_job_scheduler_table.sql',
    'extend_tasks_and_timesheets_tables.sql'
];

foreach ($files as $file) {
    echo "Running $file...\n";
    $sql = file_get_contents(__DIR__ . '/' . $file);

    // Split by semicolons for multi-statement files
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            if ($conn->query($statement)) {
                echo "  ✓ Statement executed\n";
            } else {
                $error = $conn->error;
                if (strpos($error, 'Duplicate column') !== false ||
                    strpos($error, 'already exists') !== false) {
                    echo "  ⊙ Already exists (skipping)\n";
                } else {
                    echo "  ✗ Error: " . $error . "\n";
                }
            }
        }
    }
    echo "\n";
}

echo "✅ Migration completed!\n";
$conn->close();
