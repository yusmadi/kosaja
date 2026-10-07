<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersAndProfilesTable extends Migration
{
    public function up()
    {
        // Table: users
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'whatsapp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'gender' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
                'null'       => true,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['tenant', 'provider', 'admin_daerah', 'super_admin'],
                'default'    => 'tenant',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'suspended', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        // Table: provider_profiles
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'verification_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'verified', 'rejected', 'suspended'],
                'default'    => 'pending',
            ],
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'id_card_photo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'bank_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'bank_account_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'free_period_end' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('provider_profiles', true);
    }

    public function down()
    {
        $this->forge->dropTable('provider_profiles', true);
        $this->forge->dropTable('users', true);
    }
}
