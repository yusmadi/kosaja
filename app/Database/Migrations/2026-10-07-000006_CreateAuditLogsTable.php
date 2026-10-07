<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuditLogsTable extends Migration
{
    public function up()
    {
        // Table: audit_logs
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'actor_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'object_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'object_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'previous_state' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'new_state' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['object_type', 'object_id']);
        $this->forge->addKey('action');
        $this->forge->addForeignKey('actor_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('audit_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs', true);
    }
}
