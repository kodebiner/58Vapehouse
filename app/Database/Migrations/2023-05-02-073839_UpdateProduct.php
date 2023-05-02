<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateProduct extends Migration
{
    public function up()
    {
        $fields = [
            'brandid'   => ['type' => 'int', 'constraint' => 11],
        ];
        $this->forge->addColumn('product', $fields);
    }

    public function down()
    {
        //
    }
}
