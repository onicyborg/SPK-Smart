<?php

declare(strict_types=1);

require_once __DIR__ . '/core/Autoload.php';

use Core\Env;
use Core\Database;

Env::load(__DIR__ . '/.env');

$db = Database::connection();

$connection = $_ENV['DB_CONNECTION'] ?? 'mysql';

echo "Resetting database...\n";

if ($connection === 'pgsql') {
    $tables = [
        'session_scores',
        'session_suppliers',
        'session_criteria',
        'evaluation_sessions',
        'suppliers',
        'criteria',
        'users',
        'migrations',
    ];

    foreach ($tables as $table) {
        $db->exec("DROP TABLE IF EXISTS {$table} CASCADE");
        echo "  Dropped table: {$table}\n";
    }
} else {
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");

    $tables = [
        'session_scores',
        'session_suppliers',
        'session_criteria',
        'evaluation_sessions',
        'suppliers',
        'criteria',
        'users',
        'migrations',
    ];

    foreach ($tables as $table) {
        $db->exec("DROP TABLE IF EXISTS {$table}");
        echo "  Dropped table: {$table}\n";
    }

    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
}

echo "\nDatabase reset completed.\n";
