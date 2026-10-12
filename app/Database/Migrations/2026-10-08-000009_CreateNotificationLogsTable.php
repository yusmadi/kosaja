<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'recipient_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'recipient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'recipient_role' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100, // e.g., 'application_approved', 'application_rejected', 'payment_success'
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['sent', 'simulated', 'failed'],
                'default'    => 'simulated',
            ],
            'channel' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'whatsapp',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('recipient_phone');
        $this->forge->addKey('event_type');
        $this->forge->createTable('notification_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('notification_logs', true);
    }
}
