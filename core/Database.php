<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance === null) {
            $connection = $_ENV['DB_CONNECTION'] ?? 'mysql';
            $host       = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port       = $_ENV['DB_PORT'] ?? '3306';
            $dbName     = $_ENV['DB_NAME'] ?? 'test';
            $user       = $_ENV['DB_USER'] ?? 'root';
            $pass       = $_ENV['DB_PASS'] ?? '';

            $dsn = match ($connection) {
                'mysql' => "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4",
                'pgsql' => "pgsql:host={$host};port={$port};dbname={$dbName};options='--client_encoding=UTF8'",
                default => throw new RuntimeException("Unsupported database connection: {$connection}"),
            };

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                throw new PDOException('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }
}
