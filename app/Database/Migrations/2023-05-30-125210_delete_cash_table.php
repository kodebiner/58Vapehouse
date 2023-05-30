<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCash extends Migration
{
    public function up()
    {
        // $this->forge->addField([
        //     'id' => [
        //         'type'           => 'INT',
        //         'constraint'     => 11,
        //         'auto_increment' => true,
        //     ],
        //     'outletid' => [
        //         'type'       => 'VARCHAR',
        //         'constraint' => '11',
        //     ],
        //     'name' => [
        //         'type' => 'VARCHAR',
        //         'null' => false,
        //         'constraint' => '255',
        //     ],
        //     'qty' => [
        //         'type' => 'VARCHAR',
        //         'null' => false,
        //         'constraint' => '255',
        //     ],
        // ]);
        // $this->forge->addKey('id', true);
        // $this->forge->createTable('cash');
    }

    public function down()
    {
        $this->forge->dropTable('cash');
    }
}