<?php

declare(strict_types=1);

require_once __DIR__ . '/core/Autoload.php';

use Core\Env;
use Core\Database;
use Core\Seeder;

Env::load(__DIR__ . '/.env');

$seedersDir = __DIR__ . '/database/seeders';
$seederFiles = glob($seedersDir . '/*.php');

if (empty($seederFiles)) {
    echo "No seeder files found.\n";
    exit(0);
}

$target = $argv[1] ?? null;

$seeders = [];
foreach ($seederFiles as $file) {
    $className = 'Database\\Seeders\\' . basename($file, '.php');

    if ($target !== null && basename($file, '.php') !== $target) {
        continue;
    }

    if (!class_exists($className)) {
        echo "  [SKIP] Class {$className} not found.\n";
        continue;
    }

    $seeder = new $className();

    if (!($seeder instanceof Seeder)) {
        echo "  [SKIP] {$className} does not extend Core\\Seeder.\n";
        continue;
    }

    $seeders[] = $seeder;
}

if (empty($seeders)) {
    echo $target !== null ? "Seeder '{$target}' not found.\n" : "No valid seeders found.\n";
    exit(0);
}

usort($seeders, fn(Seeder $a, Seeder $b) => $a->getPriority() <=> $b->getPriority());

echo "Running seeders...\n\n";

foreach ($seeders as $seeder) {
    $className = get_class($seeder);
    echo "Seeding: {$className}\n";

    try {
        $seeder->run();
    } catch (\Throwable $e) {
        echo "  [ERROR] " . $e->getMessage() . "\n";
        break;
    }
}

echo "\nSeeding completed.\n";
