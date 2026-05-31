<?php

declare(strict_types=1);

$driver = $_ENV['DB_CONNECTION'] ?? 'mysql';

if ($driver === 'pgsql') {
    return "
        CREATE TABLE IF NOT EXISTS criteria (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            criteria_code VARCHAR(50) NOT NULL UNIQUE,
            criteria_name VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            type VARCHAR(50) NOT NULL,
            created_by UUID NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_criteria_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
        )
    ";
}

return "
    CREATE TABLE IF NOT EXISTS criteria (
        id CHAR(36) PRIMARY KEY,
        criteria_code VARCHAR(50) NOT NULL UNIQUE,
        criteria_name VARCHAR(255) NOT NULL,
        description TEXT DEFAULT NULL,
        type VARCHAR(50) NOT NULL,
        created_by CHAR(36) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_criteria_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
