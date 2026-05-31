<?php

declare(strict_types=1);

$driver = $_ENV['DB_CONNECTION'] ?? 'mysql';

if ($driver === 'pgsql') {
    return "
        CREATE TABLE IF NOT EXISTS session_suppliers (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            session_id UUID NOT NULL,
            supplier_id UUID NOT NULL,
            readiness_notes TEXT DEFAULT NULL,
            CONSTRAINT fk_ss_session FOREIGN KEY (session_id) REFERENCES evaluation_sessions(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_ss_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE,
            CONSTRAINT uk_session_supplier UNIQUE (session_id, supplier_id)
        )
    ";
}

return "
    CREATE TABLE IF NOT EXISTS session_suppliers (
        id CHAR(36) PRIMARY KEY,
        session_id CHAR(36) NOT NULL,
        supplier_id CHAR(36) NOT NULL,
        readiness_notes TEXT DEFAULT NULL,
        CONSTRAINT fk_ss_session FOREIGN KEY (session_id) REFERENCES evaluation_sessions(id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT fk_ss_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE,
        UNIQUE KEY uk_session_supplier (session_id, supplier_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
