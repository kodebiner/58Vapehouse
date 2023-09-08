<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiscvar extends Migration
{
    public function up()
    {
        $fields = [
            'discvar'            => ['type' => 'INT', 'constraint' => 11],
        ];
        $this->forge->addColumn('trxdetail', $fields);
    }

    public function down()
    {
        
    }
}