<?php

/**
 * Database Setup Script
 *
 * Run once from CLI or browser (protected) to create all tables:
 *   php db/setup.php
 *
 * This script reads schema.sql and executes each statement via PDO.
 * It also runs migration steps for existing databases (e.g. adding Ban_Until).
 */

define('BASE_PATH', dirname(__DIR__));

$cfg = require BASE_PATH . '/config/database.php';

// Connect WITHOUT specifying a database so we can create it
$dsn = sprintf(
    'mysql:host=%s;charset=%s',
    $cfg['host'],
    $cfg['charset']
);

try {
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

$sql = file_get_contents(__DIR__ . '/schema.sql');

// Split on semicolons (skip empty statements)
$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    fn($s) => $s !== ''
);

$success = 0;
$errors  = 0;

foreach ($statements as $statement) {
    try {
        $pdo->exec($statement);
        $success++;
        echo "OK: " . substr($statement, 0, 60) . "…\n";
    } catch (PDOException $e) {
        $errors++;
        echo "ERR: " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "Done. {$success} statements OK, {$errors} errors.\n";

// ── Migration: Add Ban_Until column to existing Customer table ────────────────
echo "\nRunning migrations for existing databases...\n";

try {
    $pdo->exec("USE shopzone");

    // Check if Ban_Until column already exists
    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = 'shopzone'
           AND TABLE_NAME   = 'Customer'
           AND COLUMN_NAME  = 'Ban_Until'"
    );
    $exists = (int) $stmt->fetchColumn();

    if (!$exists) {
        $pdo->exec(
            "ALTER TABLE Customer
             ADD COLUMN Ban_Until DATETIME DEFAULT NULL
             AFTER Account_Status"
        );
        echo "Migration OK: Added Ban_Until column to Customer table.\n";
    } else {
        echo "Migration SKIP: Ban_Until column already exists.\n";
    }
} catch (PDOException $e) {
    echo "Migration ERR: " . $e->getMessage() . "\n";
}

echo "\nSetup complete.\n";
