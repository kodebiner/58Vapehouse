<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCash extends Migration
{
    public function up()
    {
        $fields = [
            'name' => [
                'name'          => 'description',
                'type'          => 'VARCHAR',
                'constraint'    => '255',
                'null'          => false,
            ],
        ];
        $this->forge->modifyColumn('cash', $fields);
    }

    public function down()
    {
        
    }
}