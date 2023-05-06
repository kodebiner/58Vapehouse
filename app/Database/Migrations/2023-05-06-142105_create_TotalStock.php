<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTotalStock extends Migration
{
    public function up()
    {
        $fields = [
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false, 'auto_increment' => true],
            'variantid'         => ['type' => 'INT', 'constraint' => 11],
            'hargadasar'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'hargamodal'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'qty'               => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '0'],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('totalstock', true);
    }

    public function down()
    {
        $this->forge->dropTable('totalstock', true);
    }
}