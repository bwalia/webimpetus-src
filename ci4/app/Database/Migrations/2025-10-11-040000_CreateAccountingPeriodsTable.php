<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountingPeriodsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => false,
            ],
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => false,
            ],
            'period_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'comment' => 'e.g., "Q1 2024", "January 2024"',
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'is_current' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 if this is the active accounting period',
            ],
            'is_closed' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 if this period is closed (no more transactions)',
            ],
            'closed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'closed_by' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'comment' => 'UUID of user who closed the period',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'modified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('uuid');
        $this->forge->addKey('uuid_business_id');
        $this->forge->addKey(['uuid_business_id', 'is_current']);
        $this->forge->addKey(['start_date', 'end_date']);

        $this->forge->createTable('accounting_periods');
    }

    public function down()
    {
        $this->forge->dropTable('accounting_periods');
    }
}
