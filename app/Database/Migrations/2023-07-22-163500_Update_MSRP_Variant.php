<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateMSRP extends Migration
{
    public function up()
    {
        $fields = [
            'hargarekomendasi'         => ['type' => 'varchar', 'constraint' => 255],
        ];
        $this->forge->addColumn('variant', $fields);
    }

    public function down()
    {
        
    }
}