<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewTableServiceDomains extends Migration
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
            'service_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'domain_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('service__domains', true);
    }

    public function down()
    {
        $this->forge->dropTable('service__domains');
    }
}
