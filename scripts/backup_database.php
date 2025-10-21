#!/usr/bin/env php
<?php
/**
 * Database Backup Script
 *
 * Creates a backup of the database before anonymization
 *
 * Usage: php backup_database.php
 *
 * Date: 2025-10-12
 */

// Configuration
define('DB_CONTAINER', 'workerra-ci-db');
define('DB_NAME', 'myworkstation_dev');
define('DB_USER', 'wsl_dev');
define('DB_PASS', 'CHANGE_ME');
define('BACKUP_DIR', '/home/bwalia/workerra-ci/backups');

// Colors for CLI output
class Colors {
    public static $GREEN = "\033[0;32m";
    public static $RED = "\033[0;31m";
    public static $YELLOW = "\033[1;33m";
    public static $BLUE = "\033[0;34m";
    public static $NC = "\033[0m";
}

function log_message($message, $color = 'nc') {
    $colors = [
        'green' => Colors::$GREEN,
        'red' => Colors::$RED,
        'yellow' => Colors::$YELLOW,
        'blue' => Colors::$BLUE,
        'nc' => Colors::$NC
    ];

    echo $colors[$color] . $message . Colors::$NC . PHP_EOL;
}

function human_filesize($bytes, $decimals = 2) {
    $size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

// Main backup process
log_message("\n" . str_repeat("=", 60), 'yellow');
log_message("  DATABASE BACKUP BEFORE ANONYMIZATION", 'yellow');
log_message(str_repeat("=", 60) . "\n", 'yellow');

// Create backup directory if it doesn't exist
if (!is_dir(BACKUP_DIR)) {
    mkdir(BACKUP_DIR, 0755, true);
    log_message("✓ Created backup directory: " . BACKUP_DIR, 'green');
}

// Generate backup filename
$timestamp = date('Ymd_His');
$backupFile = BACKUP_DIR . '/' . DB_NAME . '_before_anonymization_' . $timestamp . '.sql';
$compressedFile = $backupFile . '.gz';

log_message("[1/3] Creating database backup...", 'blue');
log_message("  Database: " . DB_NAME, 'nc');
log_message("  File: " . basename($backupFile), 'nc');

// Build mysqldump command
$command = sprintf(
    'docker exec %s mariadb-dump -u %s -p%s --single-transaction --routines --triggers --events --hex-blob %s > %s 2>&1',
    DB_CONTAINER,
    DB_USER,
    DB_PASS,
    DB_NAME,
    escapeshellarg($backupFile)
);

// Execute backup
exec($command, $output, $returnCode);

if ($returnCode !== 0) {
    log_message("\n✗ Backup failed!", 'red');
    log_message("Error output:", 'red');
    foreach ($output as $line) {
        log_message("  " . $line, 'red');
    }
    exit(1);
}

// Check if file was created
if (!file_exists($backupFile)) {
    log_message("\n✗ Backup file was not created!", 'red');
    exit(1);
}

$fileSize = filesize($backupFile);
log_message("✓ Backup created successfully (" . human_filesize($fileSize) . ")", 'green');

// Compress backup
log_message("\n[2/3] Compressing backup...", 'blue');
exec("gzip " . escapeshellarg($backupFile), $gzipOutput, $gzipReturn);

if ($gzipReturn === 0 && file_exists($compressedFile)) {
    $compressedSize = filesize($compressedFile);
    $compression = round((1 - ($compressedSize / $fileSize)) * 100, 1);
    log_message("✓ Backup compressed successfully", 'green');
    log_message("  Original size: " . human_filesize($fileSize), 'nc');
    log_message("  Compressed size: " . human_filesize($compressedSize), 'nc');
    log_message("  Compression: " . $compression . "%", 'nc');
} else {
    log_message("⚠ Compression failed, backup saved uncompressed", 'yellow');
    $compressedFile = $backupFile;
}

// Verification
log_message("\n[3/3] Verifying backup...", 'blue');
$fileContent = shell_exec("zcat " . escapeshellarg($compressedFile) . " | head -20");
if (strpos($fileContent, 'MySQL dump') !== false || strpos($fileContent, 'MariaDB dump') !== false) {
    log_message("✓ Backup verified successfully", 'green');
} else {
    log_message("⚠ Could not verify backup integrity", 'yellow');
}

// Print summary
log_message("\n" . str_repeat("=", 60), 'yellow');
log_message("  BACKUP COMPLETE", 'yellow');
log_message(str_repeat("=", 60) . "\n", 'yellow');

log_message("Backup Details:", 'green');
log_message("  • File: " . $compressedFile, 'nc');
log_message("  • Size: " . human_filesize(filesize($compressedFile)), 'nc');
log_message("  • Database: " . DB_NAME, 'nc');
log_message("  • Timestamp: " . date('Y-m-d H:i:s'), 'nc');

log_message("\n" . str_repeat("-", 60), 'nc');
log_message("Next Steps:", 'blue');
log_message("  1. Run: php anonymize_database.php", 'nc');
log_message("  2. Verify anonymization", 'nc');
log_message("\nTo restore from backup:", 'yellow');
log_message("  gunzip " . basename($compressedFile), 'nc');
log_message("  docker exec -i " . DB_CONTAINER . " mariadb -u " . DB_USER . " -p'" . DB_PASS . "' " . DB_NAME . " < " . str_replace('.gz', '', $compressedFile), 'nc');
log_message(str_repeat("-", 60) . "\n", 'nc');

exit(0);
