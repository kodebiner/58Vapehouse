<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUserOut extends Migration
{
    public function up()
    {
        $fields = [
            'outletid'   => ['type' => 'int', 'constraint' => 11, null => false],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        //
    }
}
