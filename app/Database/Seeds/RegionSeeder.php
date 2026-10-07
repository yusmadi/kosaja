<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $regions = [
            // Level 1: Province
            [
                'id'         => 1,
                'parent_id'  => null,
                'name'       => 'D.I. Yogyakarta',
                'level'      => 'province',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Level 2: Cities / Regencies
            [
                'id'         => 2,
                'parent_id'  => 1,
                'name'       => 'Kabupaten Sleman',
                'level'      => 'city',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 3,
                'parent_id'  => 1,
                'name'       => 'Kota Yogyakarta',
                'level'      => 'city',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 4,
                'parent_id'  => 1,
                'name'       => 'Kabupaten Bantul',
                'level'      => 'city',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Level 3: Operational Area (Kecamatan / Klaster Kampus)
            [
                'id'         => 5,
                'parent_id'  => 2,
                'name'       => 'Depok (UGM / UNY / UPN)',
                'level'      => 'operational',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 6,
                'parent_id'  => 2,
                'name'       => 'Mlati (UTY / Jombor)',
                'level'      => 'operational',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id'         => 7,
                'parent_id'  => 3,
                'name'       => 'Gondokusuman (UKDW / Lempuyangan)',
                'level'      => 'operational',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('regions')->ignore(true)->insertBatch($regions);
    }
}
