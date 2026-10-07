<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegionsAndPropertiesTable extends Migration
{
    public function up()
    {
        // Table: regions
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'parent_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'level' => [
                'type'       => 'ENUM',
                'constraint' => ['province', 'city', 'operational'],
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
        $this->forge->addForeignKey('parent_id', 'regions', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('regions', true);

        // Table: properties
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
            'region_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['kos', 'kontrakan', 'rumah_sewa', 'lainnya'],
                'default'    => 'kos',
            ],
            'target_gender' => [
                'type'       => 'ENUM',
                'constraint' => ['putra', 'putri', 'campur_keluarga'],
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'address' => [
                'type' => 'TEXT',
            ],
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],
            'rules' => [
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
        $this->forge->addKey('region_id');
        $this->forge->addForeignKey('provider_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('region_id', 'regions', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('properties', true);

        // Table: property_photos
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
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('property_photos', true);

        // Table: property_facilities
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
            'facility_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('property_facilities', true);
    }

    public function down()
    {
        $this->forge->dropTable('property_facilities', true);
        $this->forge->dropTable('property_photos', true);
        $this->forge->dropTable('properties', true);
        $this->forge->dropTable('regions', true);
    }
}
