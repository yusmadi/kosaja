<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPriceSixMonthlyToListingsTable extends Migration
{
    public function up()
    {
        $fields = [
            'price_six_monthly' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'price_monthly',
            ],
        ];

        $this->forge->addColumn('listings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('listings', 'price_six_monthly');
    }
}
