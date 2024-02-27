<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CretaeTableRolesPermissions extends Migration
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
            'role_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'permission_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('roles__permissions', true);
    }

    public function down()
    {
        $this->forge->dropTable('roles__permissions');
    }
}
