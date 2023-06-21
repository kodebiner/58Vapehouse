<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePayment extends Migration
{
    public function up()
    {
        $fields = [
            'outletid'   => ['type' => 'int', 'constraint' => 11, null => false],
        ];
        $this->forge->addColumn('payment', $fields);
    }

    public function down()
    {
        //
    }
}
