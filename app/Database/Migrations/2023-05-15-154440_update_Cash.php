<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCash extends Migration
{
    public function up()
    {
        $fields = [
            'userid'      => ['type' => 'INT', 'constraint' => 11],
            'type'        => ['type' => 'INT', 'constraint' => 11],
            'date'        => ['type' => 'DATETIME'],
        ];
        $this->forge->addColumn('cash', $fields);
    }

    public function down()
    {
        
    }
}