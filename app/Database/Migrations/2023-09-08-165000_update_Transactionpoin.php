<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPoinused extends Migration
{
    public function up()
    {
        $fields = [
            'pointused'            => ['type' => 'INT', 'constraint' => 11],
        ];
        $this->forge->addColumn('transaction', $fields);
    }

    public function down()
    {
        
    }
}