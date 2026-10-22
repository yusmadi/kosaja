<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProviderSubscriptionAndBillingTables extends Migration
{
    public function up()
    {
        // 1. Table: subscription_plans
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['listing_fee', 'subscription'],
                'default'    => 'subscription',
            ],
            'billing_cycle' => [
                'type'       => 'ENUM',
                'constraint' => ['15_days', 'monthly'],
                'default'    => 'monthly',
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
            ],
            'duration_days' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 30,
            ],
            'listing_capacity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 3,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'features' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->addKey('type');
        $this->forge->addKey('is_active');
        $this->forge->createTable('subscription_plans', true);

        // 2. Table: provider_subscriptions
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
            'plan_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'grace_period', 'expired', 'cancelled'],
                'default'    => 'active',
            ],
            'capacity_limit' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 3,
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
        $this->forge->addKey('provider_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('provider_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('plan_id', 'subscription_plans', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('provider_subscriptions', true);

        // 3. Table: provider_invoices
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'invoice_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'order_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'provider_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'plan_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'subscription_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'expired', 'cancelled'],
                'default'    => 'pending',
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'snap_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'due_date' => [
                'type' => 'DATE',
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
        $this->forge->addKey('provider_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('provider_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('plan_id', 'subscription_plans', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('provider_invoices', true);

        // 4. Table: provider_payments
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
        $this->forge->addForeignKey('invoice_id', 'provider_invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('provider_payments', true);
    }

    public function down()
    {
        $this->forge->dropTable('provider_payments', true);
        $this->forge->dropTable('provider_invoices', true);
        $this->forge->dropTable('provider_subscriptions', true);
        $this->forge->dropTable('subscription_plans', true);
    }
}
