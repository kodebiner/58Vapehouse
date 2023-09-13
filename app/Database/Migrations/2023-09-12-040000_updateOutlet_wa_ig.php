<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Add_WA_IG extends Migration
{
    public function up()
    {
        $fields = [
            'instagram'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'phone'                 => ['type' => 'VARCHAR', 'constraint' => 255],
        ];
        $this->forge->addColumn('outlet', $fields);
    }

    public function down()
    {
        
    }
}