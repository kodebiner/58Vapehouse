<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsers extends Migration
{
    public function up()
    {
        $fields = [
            'phone'     => ['type' => 'varchar', 'constraint' => 255],
            'photo'     => ['type' => 'varchar', 'constraint' => 255],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        
    }
}