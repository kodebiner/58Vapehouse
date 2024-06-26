<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDailyReport extends Migration
{
    public function up()
    {
        $fields = [
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false, 'auto_increment' => true],
            'dateopen'          => ['type' => 'DATETIME'],
            'dateclose'         => ['type' => 'DATETIME'],
            'useridopen'        => ['type' => 'INT', 'constraint' => 11],
            'useridclose'       => ['type' => 'INT', 'constraint' => 11],
            'outletid'          => ['type' => 'INT', 'constraint' => 11],
            'initialcash'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'totalcashin'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'totalcashout'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'cashclose'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'noncashclose'      => ['type' => 'VARCHAR', 'constraint' => 255],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('dailyreport', true);
    }

    public function down()
    {

    }
}