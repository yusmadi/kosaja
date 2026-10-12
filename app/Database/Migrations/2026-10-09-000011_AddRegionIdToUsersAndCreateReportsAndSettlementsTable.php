<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegionIdToUsersAndCreateReportsAndSettlementsTable extends Migration
{
    public function up()
    {
        // 1. Add region_id to users
        $this->forge->addColumn('users', [
            'region_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'role',
            ],
        ]);
        $this->db->query('ALTER TABLE `users` ADD CONSTRAINT `fk_users_region_id` FOREIGN KEY (`region_id`) REFERENCES `regions`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');

        // 2. Add escalation fields to disputes
        $this->forge->addColumn('disputes', [
            'is_escalated' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'status',
            ],
            'escalation_notes' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'is_escalated',
            ],
            'escalated_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'escalation_notes',
            ],
        ]);

        // 3. Create table: reports (Laporan Pelanggaran Regional)
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'reporter_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'object_type' => [
                'type'       => 'ENUM',
                'constraint' => ['listing', 'property', 'provider'],
                'default'    => 'listing',
            ],
            'object_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'region_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'reason' => [
                'type' => 'TEXT',
            ],
            'evidence_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['open', 'investigating', 'resolved', 'dismissed'],
                'default'    => 'open',
            ],
            'admin_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'resolved_by_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'resolved_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey('status');
        $this->forge->addForeignKey('reporter_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('region_id', 'regions', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('resolved_by_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('reports', true);

        // 4. Create table: settlements (Pencairan Dana Mitra Provider)
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'provider_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'contract_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'gross_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '0.00',
            ],
            'platform_fee' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '0.00',
            ],
            'net_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '0.00',
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
            'bank_account_holder' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'completed', 'failed'],
                'default'    => 'pending',
            ],
            'payout_reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'processed_by_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('status');
        $this->forge->addForeignKey('provider_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('contract_id', 'contracts', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('processed_by_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('settlements', true);
    }

    public function down()
    {
        $this->forge->dropTable('settlements', true);
        $this->forge->dropTable('reports', true);
        
        $this->forge->dropColumn('disputes', ['is_escalated', 'escalation_notes', 'escalated_at']);

        $this->db->query('ALTER TABLE `users` DROP FOREIGN KEY `fk_users_region_id`');
        $this->forge->dropColumn('users', 'region_id');
    }
}
