<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatebookingOrder extends Migration
{
    public function up()
    {
        $fields = [
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false, 'auto_increment' => true],
            'outletid'          => ['type' => 'INT', 'constraint' => 11],
            'memberid'          => ['type' => 'INT', 'constraint' => 255],
            'value'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'disctype'          => ['type' => 'tinyint', 'constraint' => 1],
            'discvalue'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'discvar'           => ['type' => 'INT', 'constraint' => 255],
            'bargainprice'      => ['type' => 'INT', 'constraint' => 255],
            'date'              => ['type' => 'DATETIME'],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('booking', true);
    }

    public function down()
    {

    }
}