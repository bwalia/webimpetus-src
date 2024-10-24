<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateServiceSecretTemplateKey extends Migration
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
            'service_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'secret_temp_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'secret_key' => [
                'type' => 'TEXT'
            ],
            'values_temp_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'values_key' => [
                'type' => 'TEXT'
            ],
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'

        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('service__secret_value_template__key', true);
    }

    public function down()
    {
        $this->forge->dropTable('service__secret_value_template__key');
    }
}
