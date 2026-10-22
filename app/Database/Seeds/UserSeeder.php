<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $defaultPassword = password_hash('password123', PASSWORD_BCRYPT);

        $users = [
            [
                'id'         => 1,
                'name'       => 'Super Administrator',
                'email'      => 'admin@kosaja.id',
                'password'   => $defaultPassword,
                'whatsapp'   => '081234567890',
                'gender'     => 'L',
                'role'       => 'super_admin',
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 2,
                'name'       => 'Admin Daerah Sleman',
                'email'      => 'admin.sleman@kosaja.id',
                'password'   => $defaultPassword,
                'whatsapp'   => '081234567891',
                'gender'     => 'L',
                'role'       => 'admin_daerah',
                'region_id'  => 2,
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 3,
                'name'       => 'Haji Ahmad Santoso (Penyedia)',
                'email'      => 'ahmad@owner.kosaja.id',
                'password'   => $defaultPassword,
                'whatsapp'   => '081234567892',
                'gender'     => 'L',
                'role'       => 'provider',
                'region_id'  => 2,
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 4,
                'name'       => 'Budi Pratama (Penyewa)',
                'email'      => 'budi@gmail.com',
                'password'   => $defaultPassword,
                'whatsapp'   => '081234567893',
                'gender'     => 'L',
                'role'       => 'tenant',
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 5,
                'name'       => 'Siti Aisyah (Penyewa)',
                'email'      => 'siti@gmail.com',
                'password'   => $defaultPassword,
                'whatsapp'   => '081234567894',
                'gender'     => 'P',
                'role'       => 'tenant',
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('users')->ignore(true)->insertBatch($users);

        // Provider profile for user 3
        $providerProfile = [
            'id'                  => 1,
            'user_id'             => 3,
            'verification_status' => 'verified',
            'rejection_reason'    => null,
            'id_card_photo'       => 'ktp_ahmad.jpg',
            'bank_name'           => 'Bank Syariah Indonesia (BSI)',
            'bank_account_number' => '7123456789',
            'free_period_end'     => date('Y-m-d', strtotime('+30 days')),
            'created_at'          => $now,
            'updated_at'          => $now,
        ];

        $this->db->table('provider_profiles')->ignore(true)->insert($providerProfile);
    }
}
