<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserName extends Migration
{
    public function up()
    {
        $fields = [
            'firstname'         => ['type' => 'varchar', 'constraint' => 255],
            'lastname'          => ['type' => 'varchar', 'constraint' => 255],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        
    }
}