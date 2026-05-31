<?php

declare(strict_types=1);

require_once __DIR__ . '/core/Autoload.php';

use Core\Env;
use Core\Database;

Env::load(__DIR__ . '/.env');

$db = Database::connection();

$connection = $_ENV['DB_CONNECTION'] ?? 'mysql';

if ($connection === 'pgsql') {
    $db->exec("CREATE EXTENSION IF NOT EXISTS pgcrypto");
    $db->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            migration_name VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
} else {
    $db->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id CHAR(36) PRIMARY KEY,
            migration_name VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

$migrationsDir = __DIR__ . '/database/migrations';
$migrationFiles = glob($migrationsDir . '/*.php');

if (empty($migrationFiles)) {
    echo "No migration files found.\n";
    exit(0);
}

sort($migrationFiles);

$stmt = $db->query("SELECT migration_name FROM migrations");
$executed = $stmt->fetchAll(PDO::FETCH_COLUMN);

$newMigrations = [];

foreach ($migrationFiles as $file) {
    $migrationName = basename($file);
    if (!in_array($migrationName, $executed, true)) {
        $newMigrations[] = $file;
    }
}

if (empty($newMigrations)) {
    echo "Nothing to migrate.\n";
    exit(0);
}

$insertStmt = $db->prepare("INSERT INTO migrations (migration_name) VALUES (:name)");

foreach ($newMigrations as $file) {
    $migrationName = basename($file);

    $sql = require $file;

    echo "Migrating: {$migrationName}\n";

    try {
        $db->exec($sql);
        $insertStmt->execute([':name' => $migrationName]);

        echo "  OK\n";
    } catch (\Throwable $e) {
        echo "\n[ERROR] Migration failed on file: {$migrationName}\n";
        echo "Detail: " . $e->getMessage() . "\n";
        break;
    }
}

echo "\nAll migrations completed.\n";
