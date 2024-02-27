<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CretaeTableRoles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'role_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('roles', true);
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}
