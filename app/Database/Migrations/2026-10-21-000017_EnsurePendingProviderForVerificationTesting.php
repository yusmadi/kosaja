<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnsurePendingProviderForVerificationTesting extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Check if user 29 exists, set as pending provider for Sleman (Region 2)
        $user29 = $db->table('users')->where('id', 29)->get()->getRowArray();
        if ($user29) {
            $db->table('users')->where('id', 29)->update([
                'name'      => 'Yusmadi (Mitra Pending Sleman)',
                'role'      => 'provider',
                'region_id' => 2,
                'status'    => 'active',
            ]);

            // Ensure profile exists with 'pending' status
            $profile = $db->table('provider_profiles')->where('user_id', 29)->get()->getRowArray();
            if ($profile) {
                $db->table('provider_profiles')->where('user_id', 29)->update([
                    'verification_status' => 'pending',
                    'rejection_reason'    => null,
                    'bank_name'           => 'BSI (Bank Syariah Indonesia)',
                    'bank_account_number' => '7999881122',
                    'id_card_photo'       => 'ktp_ahmad.jpg',
                ]);
            } else {
                $db->table('provider_profiles')->insert([
                    'user_id'             => 29,
                    'verification_status' => 'pending',
                    'rejection_reason'    => null,
                    'bank_name'           => 'BSI (Bank Syariah Indonesia)',
                    'bank_account_number' => '7999881122',
                    'id_card_photo'       => 'ktp_ahmad.jpg',
                    'free_period_end'     => date('Y-m-d', strtotime('+30 days')),
                    'created_at'          => date('Y-m-d H:i:s'),
                    'updated_at'          => date('Y-m-d H:i:s'),
                ]);
            }
        } else {
            // If user 29 does not exist, insert test pending provider
            $db->table('users')->insert([
                'id'         => 29,
                'name'       => 'Yusmadi (Mitra Pending Sleman)',
                'email'      => 'yusmadi80@gmail.com',
                'password'   => password_hash('password123', PASSWORD_BCRYPT),
                'whatsapp'   => '081362549920',
                'gender'     => 'L',
                'role'       => 'provider',
                'region_id'  => 2,
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $db->table('provider_profiles')->insert([
                'user_id'             => 29,
                'verification_status' => 'pending',
                'rejection_reason'    => null,
                'bank_name'           => 'BSI (Bank Syariah Indonesia)',
                'bank_account_number' => '7999881122',
                'id_card_photo'       => 'ktp_ahmad.jpg',
                'free_period_end'     => date('Y-m-d', strtotime('+30 days')),
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        // No revert needed
    }
}
