<?php

declare(strict_types=1);

class Autoload
{
    private static array $prefixes = [];

    public static function register(): void
    {
        spl_autoload_register([self::class, 'loadClass']);
    }

    public static function addNamespace(string $prefix, string $baseDir): void
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        self::$prefixes[$prefix] = $baseDir;
    }

    private static function loadClass(string $class): void
    {
        $class = ltrim($class, '\\');

        foreach (self::$prefixes as $prefix => $baseDir) {
            if (str_starts_with($class, $prefix)) {
                $relativeClass = substr($class, strlen($prefix));
                $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    }
}

Autoload::addNamespace('App', __DIR__ . '/../app');
Autoload::addNamespace('Core', __DIR__ . '/../core');
Autoload::addNamespace('Database\\Seeders', __DIR__ . '/../database/seeders');
Autoload::register();

require_once __DIR__ . '/helpers.php';
