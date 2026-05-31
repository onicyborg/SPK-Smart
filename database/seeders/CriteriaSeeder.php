<?php

declare(strict_types=1);

namespace Database\Seeders;

use Core\Database;
use Core\Seeder;
use Core\Uuid;

class CriteriaSeeder extends Seeder
{
    protected int $priority = 20;

    public function run(): void
    {
        $db = Database::connection();

        $existing = $db->query("SELECT COUNT(*) FROM criteria")->fetchColumn();

        if ((int) $existing > 0) {
            echo "  CriteriaSeeder: Criteria already exist, skipping.\n";
            return;
        }

        $adminId = $db->query("SELECT id FROM users WHERE username = 'admin' LIMIT 1")->fetchColumn();

        if (!$adminId) {
            echo "  CriteriaSeeder: Admin user not found, skipping.\n";
            return;
        }

        $criteria = [
            ['C01', 'Harga Ban', 'Biaya atau harga penawaran per unit ban pesawat dari supplier dalam rupiah (Cost)', 'cost'],
            ['C02', 'Kualitas / Ketahanan Material', 'Tingkat kualitas dan ketahanan material ban terhadap tekanan, suhu, dan gesekan landasan (Benefit)', 'benefit'],
            ['C03', 'Waktu Pengiriman', 'Lama waktu yang dibutuhkan supplier untuk mengirimkan pesanan ke lokasi bandara (Cost)', 'cost'],
            ['C04', 'Garansi & Layanan Purna Jual', 'Cakupan garansi produk dan ketersediaan layanan purna jual (after-sales service) (Benefit)', 'benefit'],
        ];

        $stmt = $db->prepare("
            INSERT INTO criteria (id, criteria_code, criteria_name, description, type, created_by, created_at, updated_at)
            VALUES (:id, :code, :name, :desc, :type, :created_by, NOW(), NOW())
        ");

        foreach ($criteria as [$code, $name, $desc, $type]) {
            $stmt->execute([
                ':id'         => Uuid::generate(),
                ':code'       => $code,
                ':name'       => $name,
                ':desc'       => $desc,
                ':type'       => $type,
                ':created_by' => $adminId,
            ]);
        }

        echo "  CriteriaSeeder: 4 criteria inserted.\n";
    }
}
