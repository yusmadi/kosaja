<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegionIdToAuditLogsTable extends Migration
{
    public function up()
    {
        $fields = [
            'region_id' => [
                'type'       => 'BIGINT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'actor_id',
            ],
        ];

        $this->forge->addColumn('audit_logs', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('audit_logs', 'region_id');
    }
}
