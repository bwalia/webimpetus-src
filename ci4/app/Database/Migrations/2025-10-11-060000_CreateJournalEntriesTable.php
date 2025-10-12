<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJournalEntriesTable extends Migration
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
            'entry_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'Unique entry number (e.g., JE000001)',
            ],
            'entry_date' => [
                'type' => 'DATE',
                'null' => false,
                'comment' => 'Transaction date',
            ],
            'entry_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Type: General, Adjusting, Closing, Reversing',
            ],
            'reference_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'e.g., Invoice, Payment, Adjustment',
            ],
            'reference_id' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'comment' => 'UUID of related document',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'total_debit' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'total_credit' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'is_balanced' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 if debits = credits',
            ],
            'is_posted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 if entry is finalized',
            ],
            'posted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'comment' => 'UUID of user who created entry',
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
        $this->forge->addKey('entry_number');
        $this->forge->addKey(['uuid_business_id', 'entry_date']);
        $this->forge->addKey('is_posted');

        $this->forge->createTable('journal_entries');
    }

    public function down()
    {
        $this->forge->dropTable('journal_entries');
    }
}
