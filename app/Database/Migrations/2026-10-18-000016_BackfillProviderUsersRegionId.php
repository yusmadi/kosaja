<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillProviderUsersRegionId extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Assign existing providers to their appropriate operational region based on properties or default to Sleman (2)
        // Haji Ahmad Santoso (id: 3) -> Kabupaten Sleman (id: 2)
        $db->table('users')->where('id', 3)->where('role', 'provider')->update(['region_id' => 2]);

        // Yusmadi (id: 28) -> Kota Banda Aceh (id: 9)
        $db->table('users')->where('id', 28)->where('role', 'provider')->update(['region_id' => 9]);

        // Yusmadi Pending (id: 29) -> Kabupaten Sleman (id: 2)
        $db->table('users')->where('id', 29)->where('role', 'provider')->update(['region_id' => 2]);

        // Any remaining provider without region_id gets assigned to default Sleman (2)
        $db->table('users')->where('role', 'provider')->where('region_id IS NULL')->update(['region_id' => 2]);
    }

    public function down()
    {
        // No revert needed
    }
}
