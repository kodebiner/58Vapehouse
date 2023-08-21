<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class createSop extends Migration
{
    public function up()
    {
        $fields = [
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false, 'auto_increment' => true],
            'name'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'shift'            => ['type' => 'INT', 'constraint' => 11],          
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('sop', true);
    }

    public function down()
    {

    }
}

?>