<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAndAddfieldToUserBusinessTable extends Migration
{
    public function up()
    {
        $fields = [
            'uuid' => ['type' => 'VARCHAR', 'constraint' => 36],
        ];
        $this->forge->addColumn('user_business', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('user_business', 'uuid');
    }
}
