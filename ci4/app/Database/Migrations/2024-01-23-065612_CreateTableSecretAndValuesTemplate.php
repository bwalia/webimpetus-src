<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableSecretAndValuesTemplate extends Migration
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
            'secret_template_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'values_template_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'service_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('templates__services');
    }

    public function down()
    {
        $this->forge->dropTable('templates__services');
    }
}
