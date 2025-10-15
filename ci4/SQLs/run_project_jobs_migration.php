#!/usr/bin/env php
<?php

// Simple migration script to create project_jobs tables
require __DIR__ . '/../vendor/autoload.php';

$db = \Config\Database::connect();

echo "Creating project_jobs table...\n";
$sql1 = file_get_contents(__DIR__ . '/create_project_jobs_table.sql');
if ($db->query($sql1)) {
    echo "✓ project_jobs table created successfully\n";
} else {
    echo "✗ Error creating project_jobs table: " . $db->error()['message'] . "\n";
}

echo "\nCreating project_job_phases table...\n";
$sql2 = file_get_contents(__DIR__ . '/create_project_job_phases_table.sql');
if ($db->query($sql2)) {
    echo "✓ project_job_phases table created successfully\n";
} else {
    echo "✗ Error creating project_job_phases table: " . $db->error()['message'] . "\n";
}

echo "\nCreating project_job_scheduler table...\n";
$sql3 = file_get_contents(__DIR__ . '/create_project_job_scheduler_table.sql');
if ($db->query($sql3)) {
    echo "✓ project_job_scheduler table created successfully\n";
} else {
    echo "✗ Error creating project_job_scheduler table: " . $db->error()['message'] . "\n";
}

echo "\nExtending tasks and timesheets tables...\n";
$sql4 = file_get_contents(__DIR__ . '/extend_tasks_and_timesheets_tables.sql');
$statements = explode(';', $sql4);
foreach ($statements as $statement) {
    $statement = trim($statement);
    if (!empty($statement)) {
        if ($db->query($statement)) {
            echo "✓ Statement executed successfully\n";
        } else {
            echo "✗ Error: " . $db->error()['message'] . "\n";
        }
    }
}

echo "\n✅ Migration completed!\n";
