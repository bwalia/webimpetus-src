<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableVirtualMachines extends Migration
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
                'constraint' => 36,
                'null' => false
            ],
            'vm_name' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => false
            ],
            'vm_code' => [
                'type' => 'VARCHAR',
                'constraint' => 56,
                'null' => false
            ],
            'vm_ram_bytes' => [
                'type' => 'TINYBLOB'
            ],
            'vm_ram_display' => [
                'type' => 'VARCHAR',
                'constraint' => 56,
            ],
            'vm_cpu_cores' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'vm_description' => [
                'type' => 'TEXT'
            ],
            'vm_ipv4' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
            ],
            'vm_ipv6' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
            ],
            'status' => [
                'type' => 'TINYINT'
            ],
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('virtual_machines', true);
    }

    public function down()
    {
        $this->forge->dropTable('virtual_machines');
    }
}
