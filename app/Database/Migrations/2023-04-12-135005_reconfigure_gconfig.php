<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfigUpdate extends Migration
{
    public function up()
    {
        $fields = [
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true, 'first' => true]
        ];
        $this->forge->addColumn('gconfig', $fields);
    }

    public function down()
    {
        
    }
}