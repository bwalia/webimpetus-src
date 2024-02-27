<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContentListCustomFieldTable extends Migration
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
            'content_list_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'custom_field_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('content_list__custom_fields', true);
    }

    public function down()
    {
        $this->forge->dropTable('content_list__custom_fields');
    }
}
