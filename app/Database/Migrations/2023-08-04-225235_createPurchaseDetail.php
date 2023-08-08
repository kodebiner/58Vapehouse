<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchaseDetail extends Migration
{
    public function up()
    {
        $fields = [
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false, 'auto_increment' => true],
            'variantid'         => ['type' => 'INT', 'constraint' => 11],
            'qty'               => ['type' => 'INT', 'constraint' => 11],
            'price'             => ['type' => 'VARCHAR', 'constraint' => 255],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('purchasedetail', true);
    }

    public function down()
    {

    }
}