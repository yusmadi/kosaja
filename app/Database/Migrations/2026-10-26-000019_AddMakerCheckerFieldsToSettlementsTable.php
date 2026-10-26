<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMakerCheckerFieldsToSettlementsTable extends Migration
{
    public function up()
    {
        // 1. Add region_id and Maker-Checker verification columns to settlements
        $fields = [
            'region_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'contract_id',
            ],
            'verified_by_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'status',
            ],
            'verified_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'verified_by_id',
            ],
            'verification_notes' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'verified_at',
            ],
            'rejection_reason' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'verification_notes',
            ],
        ];

        $this->forge->addColumn('settlements', $fields);

        // 2. Modify status ENUM to support Maker-Checker lifecycle
        $this->db->query("ALTER TABLE `settlements` MODIFY COLUMN `status` ENUM('pending', 'verified', 'processing', 'completed', 'failed', 'rejected') NOT NULL DEFAULT 'pending'");

        // 3. Add foreign keys
        $this->db->query("ALTER TABLE `settlements` ADD CONSTRAINT `settlements_region_id_fk` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE `settlements` ADD CONSTRAINT `settlements_verified_by_id_fk` FOREIGN KEY (`verified_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE");

        // 4. Backfill region_id on existing settlements
        $this->db->query("
            UPDATE `settlements` s
            LEFT JOIN `contracts` c ON c.id = s.contract_id
            LEFT JOIN `properties` p ON p.id = c.property_id
            LEFT JOIN `users` u ON u.id = s.provider_id
            SET s.region_id = COALESCE(p.region_id, u.region_id)
            WHERE s.region_id IS NULL
        ");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `settlements` DROP FOREIGN KEY `settlements_region_id_fk`");
        $this->db->query("ALTER TABLE `settlements` DROP FOREIGN KEY `settlements_verified_by_id_fk`");

        $this->forge->dropColumn('settlements', [
            'region_id',
            'verified_by_id',
            'verified_at',
            'verification_notes',
            'rejection_reason',
        ]);

        $this->db->query("ALTER TABLE `settlements` MODIFY COLUMN `status` ENUM('pending', 'processing', 'completed', 'failed') NOT NULL DEFAULT 'pending'");
    }
}
