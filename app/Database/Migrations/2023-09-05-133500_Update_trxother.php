<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTrxOther extends Migration
{
    public function up()
    {
        $fields = [
            'photo'             => ['type' => 'VARCHAR', 'constraint' => 255],
        ];
        $this->forge->addColumn('trxother', $fields);
        $this->forge->addColumn('transaction', $fields);
    }

    public function down()
    {

    }
}