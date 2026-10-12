<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillAuditLogsRegionId extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        // Update all existing logs tied to property 1 / unit 1 / provider 1 to region 5 (Depok / Sleman)
        $db->table('audit_logs')->where('region_id IS NULL')->update(['region_id' => 5]);
    }

    public function down()
    {
    }
}
