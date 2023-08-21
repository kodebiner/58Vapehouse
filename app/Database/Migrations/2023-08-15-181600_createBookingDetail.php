<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class createBookingDetail extends Migration
{
    public function up()
    {
        $fields = [
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false, 'auto_increment' => true],
            'bookingid'             => ['type' => 'INT', 'constraint' => 11],
            'variantid'             => ['type' => 'INT', 'constraint' => 11],
            'bundleid'              => ['type' => 'INT', 'constraint' => 11],
            'qty'                   => ['type' => 'INT', 'constraint' => 11],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'value'                 => ['type' => 'INT', 'constraint' => 11],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('bookingdetail', true);
    }

    public function down()
    {

    }
}

?>