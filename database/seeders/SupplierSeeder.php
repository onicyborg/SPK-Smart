<?php

declare(strict_types=1);

namespace Database\Seeders;

use Core\Database;
use Core\Seeder;
use Core\Uuid;

class SupplierSeeder extends Seeder
{
    protected int $priority = 30;

    public function run(): void
    {
        $db = Database::connection();

        $existing = $db->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();

        if ((int) $existing > 0) {
            echo "  SupplierSeeder: Suppliers already exist, skipping.\n";
            return;
        }

        $adminId = $db->query("SELECT id FROM users WHERE username = 'admin' LIMIT 1")->fetchColumn();

        if (!$adminId) {
            echo "  SupplierSeeder: Admin user not found, skipping.\n";
            return;
        }

        $suppliers = [
            [
                'supplier_code' => 'SUP-001',
                'company_name'  => 'PT Michelin Indonesia Aviation',
                'pic_name'      => 'Budi Santoso',
                'pic_email'     => 'budi.santoso@michelin-avia.co.id',
                'pic_phone'     => '081234567890',
                'tax_id'        => '01.234.567.8-901.000',
                'full_address'  => 'Jl. Raya Cengkareng No. 88, Tangerang, Banten 15125 (Dekat Bandara Soekarno-Hatta)',
                'tire_category' => 'Ban Pesawat Komersial',
                'joined_date'   => '2023-01-15',
            ],
            [
                'supplier_code' => 'SUP-002',
                'company_name'  => 'PT Bridgestone Aircraft Tire',
                'pic_name'      => 'Dewi Kusumawati',
                'pic_email'     => 'dewi.kusuma@bridgestone-aviation.id',
                'pic_phone'     => '082112345678',
                'tax_id'        => '02.345.678.9-012.000',
                'full_address'  => 'Kawasan Industri MM2100, Blok C-5, Cibitung, Bekasi 17520',
                'tire_category' => 'Ban Pesawat Komersial',
                'joined_date'   => '2023-03-22',
            ],
            [
                'supplier_code' => 'SUP-003',
                'company_name'  => 'PT Goodyear Indonesia Tbk',
                'pic_name'      => 'Ahmad Fauzi',
                'pic_email'     => 'ahmad.fauzi@goodyear-indonesia.co.id',
                'pic_phone'     => '081398765432',
                'tax_id'        => '03.456.789.0-123.000',
                'full_address'  => 'Jl. Daan Mogot KM 19, Batu Ceper, Tangerang 15122',
                'tire_category' => 'Ban Kendaraan Darat Bandara',
                'joined_date'   => '2022-11-05',
            ],
            [
                'supplier_code' => 'SUP-004',
                'company_name'  => 'CV Angkasa Avia Parts',
                'pic_name'      => 'Siti Rahayu',
                'pic_email'     => 'siti.rahayu@angkasa-avia.co.id',
                'pic_phone'     => '087788901234',
                'tax_id'        => '04.567.890.1-234.000',
                'full_address'  => 'Ruko Aeropolis, Jl. Marsekal Suryadharma No. 10, Tangerang 15126',
                'tire_category' => 'Ban Pesawat Komersial',
                'joined_date'   => '2024-02-10',
            ],
            [
                'supplier_code' => 'SUP-005',
                'company_name'  => 'PT Dirgantara Aero Tyres',
                'pic_name'      => 'Yusuf Pratama',
                'pic_email'     => 'yusuf.pratama@dirgantara-aero.co.id',
                'pic_phone'     => '081322334455',
                'tax_id'        => '05.678.901.2-345.000',
                'full_address'  => 'Jl. Raya Serpong KM 8, Pakulonan, Tangerang Selatan 15325',
                'tire_category' => 'Ban Pesawat & Alat Berat Bandara',
                'joined_date'   => '2023-07-18',
            ],
        ];

        $stmt = $db->prepare("
            INSERT INTO suppliers (
                id, supplier_code, company_name, pic_name, pic_email, pic_phone,
                tax_id, full_address, tire_category, joined_date,
                created_by, created_at, updated_at
            ) VALUES (
                :id, :supplier_code, :company_name, :pic_name, :pic_email, :pic_phone,
                :tax_id, :full_address, :tire_category, :joined_date,
                :created_by, NOW(), NOW()
            )
        ");

        foreach ($suppliers as $s) {
            $stmt->execute([
                ':id'            => Uuid::generate(),
                ':supplier_code' => $s['supplier_code'],
                ':company_name'  => $s['company_name'],
                ':pic_name'      => $s['pic_name'],
                ':pic_email'     => $s['pic_email'],
                ':pic_phone'     => $s['pic_phone'],
                ':tax_id'        => $s['tax_id'],
                ':full_address'  => $s['full_address'],
                ':tire_category' => $s['tire_category'],
                ':joined_date'   => $s['joined_date'],
                ':created_by'    => $adminId,
            ]);
        }

        echo "  SupplierSeeder: 5 suppliers inserted.\n";
    }
}
