<?php

declare(strict_types=1);

$driver = $_ENV['DB_CONNECTION'] ?? 'mysql';

if ($driver === 'pgsql') {
    return "
        CREATE TABLE IF NOT EXISTS suppliers (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            supplier_code VARCHAR(50) NOT NULL UNIQUE,
            company_name VARCHAR(255) NOT NULL,
            pic_name VARCHAR(255) NOT NULL,
            pic_email VARCHAR(255) NOT NULL,
            pic_phone VARCHAR(20) DEFAULT NULL,
            tax_id VARCHAR(100) DEFAULT NULL,
            full_address TEXT DEFAULT NULL,
            tire_category VARCHAR(100) DEFAULT NULL,
            joined_date DATE DEFAULT NULL,
            created_by UUID NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_suppliers_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
        )
    ";
}

return "
    CREATE TABLE IF NOT EXISTS suppliers (
        id CHAR(36) PRIMARY KEY,
        supplier_code VARCHAR(50) NOT NULL UNIQUE,
        company_name VARCHAR(255) NOT NULL,
        pic_name VARCHAR(255) NOT NULL,
        pic_email VARCHAR(255) NOT NULL,
        pic_phone VARCHAR(20) DEFAULT NULL,
        tax_id VARCHAR(100) DEFAULT NULL,
        full_address TEXT DEFAULT NULL,
        tire_category VARCHAR(100) DEFAULT NULL,
        joined_date DATE DEFAULT NULL,
        created_by CHAR(36) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_suppliers_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
