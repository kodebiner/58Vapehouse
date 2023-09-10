<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Addvalue extends Migration
{
    public function up()
    {
        $fields = [
            'value'            => ['type' => 'INT', 'constraint' => 11],
        ];
        $this->forge->addColumn('debt', $fields);
    }

    public function down()
    {
        
    }
}