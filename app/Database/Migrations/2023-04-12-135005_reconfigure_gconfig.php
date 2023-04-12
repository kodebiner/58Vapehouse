<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfigUpdate extends Migration
{
    public function up()
    {
        $fields = [
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false, 'auto_increment' => true],
            'poinvalue'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'poinorder'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'memberdisc'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'memberdisctype'    => ['type' => 'TINYINT', 'constraint' => 1],
            'logo'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'bizname'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'ppn'               => ['type' => 'VARCHAR', 'constraint' => 255],
        ];
        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable('gconfig', true);
    }

    public function down()
    {
        $this->forge->dropTable('gconfig', true);
    }
}