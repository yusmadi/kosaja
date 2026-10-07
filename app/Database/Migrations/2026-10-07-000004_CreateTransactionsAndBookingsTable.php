<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionsAndBookingsTable extends Migration
{
    public function up()
    {
        // Table: rental_applications
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenant_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'listing_id' => [
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
            'duration_months' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'payment_schedule' => [
                'type'       => 'ENUM',
                'constraint' => ['full_upfront', 'monthly', '3_monthly', '6_monthly', 'yearly'],
                'default'    => 'monthly',
            ],
            'rental_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected', 'cancelled', 'expired'],
                'default'    => 'pending',
            ],
            'rejection_reason' => [
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
        $this->forge->addForeignKey('tenant_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('listing_id', 'listings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('unit_id', 'units', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rental_applications', true);

        // Table: reservations
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
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'converted', 'expired', 'cancelled'],
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
        $this->forge->addKey('expires_at');
        $this->forge->addForeignKey('application_id', 'rental_applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('reservations', true);
    }

    public function down()
    {
        $this->forge->dropTable('reservations', true);
        $this->forge->dropTable('rental_applications', true);
    }
}
