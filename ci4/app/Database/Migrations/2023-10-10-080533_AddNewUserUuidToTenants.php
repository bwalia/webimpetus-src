<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewUserUuidToTenants extends Migration
{
    public function up()
    {
        $fields = [
            'user_uuid' => ['type' => 'VARCHAR', 'constraint' => 36],
        ];
        $this->forge->addColumn('tenants', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tenants', 'user_uuid');
    }
}
