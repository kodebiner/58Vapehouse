<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddChange extends Migration
{
    public function up()
    {
        $fields = [
            'amountpaid'            => ['type' => 'INT', 'constraint' => 11],
        ];
        $this->forge->addColumn('transaction', $fields);
    }

    public function down()
    {
        
    }
}