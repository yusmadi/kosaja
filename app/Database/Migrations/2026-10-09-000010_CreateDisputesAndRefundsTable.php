<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDisputesAndRefundsTable extends Migration
{
    public function up()
    {
        // Table: disputes
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'contract_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'invoice_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'raised_by_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'against_user_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
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
                'constraint' => ['opened', 'investigating', 'resolved', 'closed'],
                'default'    => 'opened',
            ],
            'decision' => [
                'type'       => 'ENUM',
                'constraint' => ['none', 'no_refund', 'partial_refund', 'full_refund'],
                'default'    => 'none',
            ],
            'decision_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'decided_by_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'decided_at' => [
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
        $this->forge->addForeignKey('contract_id', 'contracts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('raised_by_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('against_user_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('decided_by_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('disputes', true);

        // Table: refunds
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'dispute_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'payment_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'invoice_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'refund_type' => [
                'type'       => 'ENUM',
                'constraint' => ['full', 'partial'],
                'default'    => 'full',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'completed', 'failed'],
                'default'    => 'completed',
            ],
            'reason' => [
                'type' => 'TEXT',
            ],
            'processed_by_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'processed_at' => [
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
        $this->forge->addForeignKey('dispute_id', 'disputes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('payment_id', 'payments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('processed_by_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('refunds', true);
    }

    public function down()
    {
        $this->forge->dropTable('refunds', true);
        $this->forge->dropTable('disputes', true);
    }
}
