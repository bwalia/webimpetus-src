<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterRolesMakeUuidBsNull extends Migration
{
    public function up()
    {
        $fields = [
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => '36',
            ],
        ];
        $this->forge->modifyColumn('roles', $fields);
    }

    public function down()
    {
        $this->forge->modifyColumn('roles', [
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'null' => false,
                'constraint' => '36',
            ],
        ]);
    }
}
