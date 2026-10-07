<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUnitsAndListingsTable extends Migration
{
    public function up()
    {
        // Table: units
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'property_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'size' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'floor' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['available', 'reserved', 'occupied', 'maintenance', 'inactive'],
                'default'    => 'available',
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
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('units', true);

        // Table: unit_photos
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'unit_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'photo_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'caption' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_primary' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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
        $this->forge->addForeignKey('unit_id', 'units', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('unit_photos', true);

        // Table: listings
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'unit_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'unique'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'price_monthly' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'price_yearly' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'publication_status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'pending_review', 'published', 'inactive'],
                'default'    => 'draft',
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
        $this->forge->addKey('publication_status');
        $this->forge->addForeignKey('unit_id', 'units', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('listings', true);
    }

    public function down()
    {
        $this->forge->dropTable('listings', true);
        $this->forge->dropTable('unit_photos', true);
        $this->forge->dropTable('units', true);
    }
}
