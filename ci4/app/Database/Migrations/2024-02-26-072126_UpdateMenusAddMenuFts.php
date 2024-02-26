<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateMenuAddMenuFts extends Migration
{
    public function up()
    {
        $fields = [
            'menu_fts' => ['type' => 'VARCHAR', 'constraint' => '255',],
        ];
        $this->forge->addColumn('menu', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('menu', 'menu_fts');
    }
}
