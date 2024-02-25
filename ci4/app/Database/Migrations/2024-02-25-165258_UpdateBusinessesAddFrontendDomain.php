<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateBusinessesAddFrontendDomain extends Migration
{
    public function up()
    {
        $fields = [
            'frontend_domain' => ['type' => 'VARCHAR', 'constraint' => '124',],
        ];
        $this->forge->addColumn('businesses', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('businesses', 'frontend_domain');
    }
}
