<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReceiptsTable extends Migration
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
            'receipt_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'Unique receipt reference (e.g., REC-000001)',
            ],
            'receipt_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'receipt_type' => [
                'type' => 'ENUM',
                'constraint' => ['Customer Payment', 'Sales Receipt', 'Deposit', 'Other'],
                'default' => 'Customer Payment',
                'comment' => 'Type of receipt',
            ],
            'payer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'comment' => 'Name of person/company paying',
            ],
            'payer_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'customer, client, other',
            ],
            'payer_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'comment' => 'Links to customers table',
            ],
            'invoice_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'comment' => 'Links to sales_invoices if applicable',
            ],
            'invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
            ],
            'currency' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'GBP',
            ],
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['Bank Transfer', 'Cheque', 'Cash', 'Credit Card', 'Debit Card', 'PayPal', 'Stripe', 'Other'],
                'default' => 'Bank Transfer',
            ],
            'bank_account_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'comment' => 'Links to accounts table (bank account receiving)',
            ],
            'reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'External reference (transaction ID, cheque number)',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Draft', 'Pending', 'Cleared', 'Cancelled'],
                'default' => 'Draft',
            ],
            'is_posted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 if posted to journal entries',
            ],
            'journal_entry_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'comment' => 'Links to journal_entries',
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
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
        $this->forge->addKey('receipt_number');
        $this->forge->addKey(['uuid_business_id', 'receipt_date']);
        $this->forge->addKey('payer_uuid');
        $this->forge->addKey('invoice_uuid');
        $this->forge->addKey('is_posted');

        $this->forge->createTable('receipts');
    }

    public function down()
    {
        $this->forge->dropTable('receipts');
    }
}
