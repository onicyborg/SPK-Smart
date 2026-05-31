<?php

declare(strict_types=1);

namespace Database\Seeders;

use Core\Database;
use Core\Seeder;
use Core\Uuid;

class UserSeeder extends Seeder
{
    protected int $priority = 10;

    public function run(): void
    {
        $db = Database::connection();

        $users = [
            [
                'full_name' => 'Administrator',
                'email'     => 'admin@spk-smart.local',
                'username'  => 'admin',
                'password'  => password_hash('Qwerty123*', PASSWORD_BCRYPT),
                'role'      => 'admin',
            ],
            [
                'full_name' => 'Staff User',
                'email'     => 'staff@spk-smart.local',
                'username'  => 'staff',
                'password'  => password_hash('Qwerty123*', PASSWORD_BCRYPT),
                'role'      => 'staff',
            ],
            [
                'full_name' => 'Pimpinan User',
                'email'     => 'pimpinan@spk-smart.local',
                'username'  => 'pimpinan',
                'password'  => password_hash('Qwerty123*', PASSWORD_BCRYPT),
                'role'      => 'pimpinan',
            ],
        ];

        $inserted = 0;
        foreach ($users as $u) {
            $existing = $db->query("SELECT COUNT(*) FROM users WHERE username = '{$u['username']}'")->fetchColumn();
            if ((int) $existing > 0) {
                continue;
            }

            $id = Uuid::generate();
            $db->exec("
                INSERT INTO users (id, full_name, email, username, password, role, created_at, updated_at)
                VALUES (
                    '{$id}',
                    '{$u['full_name']}',
                    '{$u['email']}',
                    '{$u['username']}',
                    '{$u['password']}',
                    '{$u['role']}',
                    NOW(),
                    NOW()
                )
            ");
            $inserted++;
            echo "  UserSeeder: User '{$u['username']}' ({$u['role']}) created with ID {$id}.\n";
        }

        if ($inserted === 0) {
            echo "  UserSeeder: All default users already exist, skipping.\n";
        } else {
            echo "  UserSeeder: {$inserted} user(s) inserted.\n";
        }
    }
}
