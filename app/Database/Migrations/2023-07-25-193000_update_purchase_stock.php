<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePurchaseStock extends Migration
{
    public function up()
    {
        $fields = [
            'productid'     => ['type' => 'int', 'constraint' => 11, null => false],
            'supplierid'    => ['type' => 'int', 'constraint' => 11, null => false],
            'price'         => ['type' => 'varchar', 'constraint' => 255],
            'status'        => ['type' => 'varchar', 'constraint' => 255],
        ];
        $this->forge->addColumn('stock', $fields);
    }

    public function down()
    {
        //
    }
}