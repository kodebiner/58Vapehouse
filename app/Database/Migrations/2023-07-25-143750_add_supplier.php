<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSupplier extends Migration
{
    public function up()
    {
        // Supplier
        $this->forge->addField([
            'id'               => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'             => ['type' => 'varchar', 'constraint' => 255],
            'phone'            => ['type' => 'varchar', 'constraint' => 255],
            'address'          => ['type' => 'varchar', 'constraint' => 255],
            'city'             => ['type' => 'varchar', 'constraint' => 255],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('supplier', true);
    }

    public function down()
    {
        //
    }
}
