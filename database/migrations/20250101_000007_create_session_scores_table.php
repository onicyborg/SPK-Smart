<?php

declare(strict_types=1);

$driver = $_ENV['DB_CONNECTION'] ?? 'mysql';

if ($driver === 'pgsql') {
    return "
        CREATE TABLE IF NOT EXISTS session_scores (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            session_id UUID NOT NULL,
            supplier_id UUID NOT NULL,
            criteria_id UUID NOT NULL,
            raw_score DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            CONSTRAINT fk_ssc_session FOREIGN KEY (session_id) REFERENCES evaluation_sessions(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_ssc_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE,
            CONSTRAINT fk_ssc_criteria FOREIGN KEY (criteria_id) REFERENCES criteria(id) ON DELETE RESTRICT ON UPDATE CASCADE,
            CONSTRAINT uk_session_supplier_criteria UNIQUE (session_id, supplier_id, criteria_id)
        )
    ";
}

return "
    CREATE TABLE IF NOT EXISTS session_scores (
        id CHAR(36) PRIMARY KEY,
        session_id CHAR(36) NOT NULL,
        supplier_id CHAR(36) NOT NULL,
        criteria_id CHAR(36) NOT NULL,
        raw_score DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        CONSTRAINT fk_ssc_session FOREIGN KEY (session_id) REFERENCES evaluation_sessions(id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT fk_ssc_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE,
        CONSTRAINT fk_ssc_criteria FOREIGN KEY (criteria_id) REFERENCES criteria(id) ON DELETE RESTRICT ON UPDATE CASCADE,
        UNIQUE KEY uk_session_supplier_criteria (session_id, supplier_id, criteria_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
