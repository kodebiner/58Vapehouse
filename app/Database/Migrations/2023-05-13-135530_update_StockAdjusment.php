<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateStockAdjustment extends Migration
{
    public function up()
    {
        $fields = [
            'note'        => ['type' => 'VARCHAR', 'constraint' => 255],
        ];
        $this->forge->addColumn('stockadjustment', $fields);
    }

    public function down()
    {
        
    }
}