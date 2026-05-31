<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Model;
use Core\Database;

class DashboardController
{
    public function index(): void
    {
        $pdo = Database::connection();

        $totalSuppliers    = (int) $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
        $totalCriteria     = (int) $pdo->query("SELECT COUNT(*) FROM criteria")->fetchColumn();
        $totalSessions     = (int) $pdo->query("SELECT COUNT(*) FROM evaluation_sessions")->fetchColumn();
        $completedSessions = (int) $pdo->query("SELECT COUNT(*) FROM evaluation_sessions WHERE status = 'completed'")->fetchColumn();

        $db = new Model();

        $recentSessions = $db->table('evaluation_sessions')
            ->select('*')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();

        $recentSuppliers = $db->table('suppliers')
            ->select('*')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();

        require_once __DIR__ . '/../../views/dashboard/index.php';
    }
}
