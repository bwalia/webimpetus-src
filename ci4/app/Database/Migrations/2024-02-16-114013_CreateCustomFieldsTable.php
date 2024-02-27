<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomFieldsTable extends Migration
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
            'field_name' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'field_type' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'field_value' => [
                'type' => 'TEXT',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('custom_fields', true);
    }

    public function down()
    {
        $this->forge->dropTable('custom_fields');
    }
}
