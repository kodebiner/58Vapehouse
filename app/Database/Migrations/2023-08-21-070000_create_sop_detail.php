<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class createSopDetail extends Migration
{
    public function up()
    {
        $fields = [
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false, 'auto_increment' => true],
            'sopid'             => ['type' => 'INT', 'constraint' => 11],
            'userid'            => ['type' => 'INT', 'constraint' => 11],
            'status'            => ['type' => 'INT', 'constraint' => 11],
            'created_at'        => ['type' => 'datetime', 'null' => true],
            'updated_at'        => ['type' => 'datetime', 'null' => true],
            'deleted_at'        => ['type' => 'datetime', 'null' => true],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('sopdetail', true);
    }

    public function down()
    {

    }
}

?>