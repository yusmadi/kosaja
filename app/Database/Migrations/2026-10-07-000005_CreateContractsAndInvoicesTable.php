<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContractsAndInvoicesTable extends Migration
{
    public function up()
    {
        // Table: contracts
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'application_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'tenant_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'provider_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'property_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'unit_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'agreed_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'payment_schedule' => [
                'type'       => 'ENUM',
                'constraint' => ['full_upfront', 'monthly', '3_monthly', '6_monthly', 'yearly'],
                'default'    => 'monthly',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'completed', 'terminated'],
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
        $this->forge->addKey('status');
        $this->forge->addForeignKey('application_id', 'rental_applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tenant_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('provider_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('unit_id', 'units', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('contracts', true);

        // Table: invoices
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
            'tenant_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'rental_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'gateway_fee' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'due_date' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['upcoming', 'pending', 'paid', 'overdue', 'cancelled'],
                'default'    => 'pending',
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
        $this->forge->addKey('due_date');
        $this->forge->addForeignKey('contract_id', 'contracts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tenant_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('invoices', true);

        // Table: payments
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'invoice_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'amount_paid' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'gateway_trx_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'success', 'failed', 'refunded'],
                'default'    => 'pending',
            ],
            'paid_at' => [
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
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payments', true);
    }

    public function down()
    {
        $this->forge->dropTable('payments', true);
        $this->forge->dropTable('invoices', true);
        $this->forge->dropTable('contracts', true);
    }
}
