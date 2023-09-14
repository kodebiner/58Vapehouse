<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Addmargindasar extends Migration
{
    public function up()
    {
        $fields = [
            'marginmodal'            => ['type' => 'INT', 'constraint' => 11],
            'margindasar'            => ['type' => 'INT', 'constraint' => 11],
        ];
        $this->forge->addColumn('trxdetail', $fields);
    }

    public function down()
    {
        
    }
}