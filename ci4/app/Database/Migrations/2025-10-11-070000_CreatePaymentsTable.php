<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
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
            'payment_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'Unique payment reference (e.g., PAY-000001)',
            ],
            'payment_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'payment_type' => [
                'type' => 'ENUM',
                'constraint' => ['Supplier Payment', 'Expense Payment', 'Refund', 'Other'],
                'default' => 'Supplier Payment',
                'comment' => 'Type of payment',
            ],
            'payee_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'comment' => 'Name of person/company being paid',
            ],
            'payee_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'supplier, employee, other',
            ],
            'payee_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'comment' => 'Links to suppliers/employees table',
            ],
            'invoice_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'comment' => 'Links to purchase_invoices if applicable',
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
                'constraint' => ['Bank Transfer', 'Cheque', 'Cash', 'Credit Card', 'Debit Card', 'PayPal', 'Other'],
                'default' => 'Bank Transfer',
            ],
            'bank_account_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'comment' => 'Links to accounts table (bank account used)',
            ],
            'reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'External reference (cheque number, transaction ID)',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Draft', 'Pending', 'Completed', 'Cancelled'],
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
        $this->forge->addKey('payment_number');
        $this->forge->addKey(['uuid_business_id', 'payment_date']);
        $this->forge->addKey('payee_uuid');
        $this->forge->addKey('invoice_uuid');
        $this->forge->addKey('is_posted');

        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}
