<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPasutriAndNotesToRentalApplications extends Migration
{
    public function up()
    {
        $fields = [
            'marital_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'payment_schedule',
            ],
            'applicant_notes' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'rental_amount',
            ],
            'marriage_certificate_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'applicant_notes',
            ],
            'is_family_verified' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'marriage_certificate_url',
            ],
            'gateway_fee' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'after'      => 'rental_amount',
            ],
        ];

        $this->forge->addColumn('rental_applications', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('rental_applications', [
            'marital_status',
            'applicant_notes',
            'marriage_certificate_url',
            'is_family_verified',
            'gateway_fee',
        ]);
    }
}
