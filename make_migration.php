<?php

declare(strict_types=1);

if ($argc < 2) {
    echo "Usage: php make_migration.php <migration_name>\n";
    echo "Example: php make_migration.php create_users_table\n";
    exit(1);
}

$migrationName = strtolower(trim($argv[1]));
$migrationName = preg_replace('/[^a-z0-9_]+/', '_', $migrationName);
$migrationName = trim($migrationName, '_');

if ($migrationName === '') {
    echo "Error: Invalid migration name.\n";
    exit(1);
}

$timestamp = date('Ymd_His');
$filename = "{$timestamp}_{$migrationName}.php";

$migrationsDir = __DIR__ . '/database/migrations';

if (!is_dir($migrationsDir)) {
    mkdir($migrationsDir, 0755, true);
}

$filepath = $migrationsDir . '/' . $filename;

$template = <<<PHP
<?php

declare(strict_types=1);

\$driver = \$_ENV['DB_CONNECTION'] ?? 'mysql';

if (\$driver === 'pgsql') {
    return "
        -- Write your PostgreSQL SQL here (use SERIAL PRIMARY KEY, omit ENGINE/CHARSET)
    ";
}

return "
    -- Write your MySQL SQL here (use INT AUTO_INCREMENT PRIMARY KEY, ENGINE=InnoDB, CHARSET=utf8mb4)
";
PHP;

file_put_contents($filepath, $template);

echo "Migration created: {$filename}\n";
