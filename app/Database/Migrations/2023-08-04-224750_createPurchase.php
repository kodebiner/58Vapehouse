<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchase extends Migration
{
    public function up()
    {
        $fields = [
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false, 'auto_increment' => true],
            'outletid'          => ['type' => 'INT', 'constraint' => 11],
            'supplierid'        => ['type' => 'INT', 'constraint' => 11],
            'date'              => ['type' => 'DATETIME'],
            'status'            => ['type' => 'INT', 'constraint' => 11],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('purchase', true);
    }

    public function down()
    {

    }
}