<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableCustomerContact extends Migration
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
            'customer_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'contact_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('customer__contact', true);
    }

    public function down()
    {
        $this->forge->dropTable('customer__contact');
    }
}
