<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJournalEntryLinesTable extends Migration
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
            'uuid_journal_entry_id' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => false,
                'comment' => 'Links to journal_entries.uuid',
            ],
            'uuid_account_id' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => false,
                'comment' => 'Links to accounts.uuid',
            ],
            'line_number' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'comment' => 'Order of line items',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'debit_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'credit_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
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
        $this->forge->addKey('uuid_journal_entry_id');
        $this->forge->addKey('uuid_account_id');

        $this->forge->createTable('journal_entry_lines');
    }

    public function down()
    {
        $this->forge->dropTable('journal_entry_lines');
    }
}
