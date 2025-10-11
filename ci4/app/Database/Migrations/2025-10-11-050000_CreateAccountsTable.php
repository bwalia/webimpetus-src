<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountsTable extends Migration
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
            'account_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'comment' => 'Unique account code (e.g., 1000, 2000)',
            ],
            'account_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'comment' => 'Account name (e.g., Cash, Accounts Receivable)',
            ],
            'account_type' => [
                'type' => 'ENUM',
                'constraint' => ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'],
                'null' => false,
                'comment' => 'Main account type',
            ],
            'account_subtype' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Subtype (e.g., Current Asset, Fixed Asset)',
            ],
            'parent_account_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'For hierarchical chart of accounts',
            ],
            'is_system_account' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 if this is a system account (cannot be deleted)',
            ],
            'normal_balance' => [
                'type' => 'ENUM',
                'constraint' => ['Debit', 'Credit'],
                'null' => false,
                'comment' => 'Normal balance side for this account',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'opening_balance' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'current_balance' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
                'comment' => 'Updated automatically from journal entries',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        $this->forge->addKey('account_code');
        $this->forge->addKey(['uuid_business_id', 'account_type']);
        $this->forge->addKey('parent_account_id');

        $this->forge->createTable('accounts');

        // Add sample chart of accounts
        $this->db->query("
            INSERT INTO `accounts`
            (`uuid`, `uuid_business_id`, `account_code`, `account_name`, `account_type`, `account_subtype`, `normal_balance`, `is_system_account`, `is_active`, `created_at`)
            VALUES
            -- Assets
            (UUID(), 'system', '1000', 'Assets', 'Asset', 'Header', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '1100', 'Current Assets', 'Asset', 'Current Asset', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '1110', 'Cash and Cash Equivalents', 'Asset', 'Current Asset', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '1120', 'Accounts Receivable', 'Asset', 'Current Asset', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '1130', 'Inventory', 'Asset', 'Current Asset', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '1200', 'Fixed Assets', 'Asset', 'Fixed Asset', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '1210', 'Property, Plant & Equipment', 'Asset', 'Fixed Asset', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '1220', 'Accumulated Depreciation', 'Asset', 'Fixed Asset', 'Credit', 1, 1, NOW()),

            -- Liabilities
            (UUID(), 'system', '2000', 'Liabilities', 'Liability', 'Header', 'Credit', 1, 1, NOW()),
            (UUID(), 'system', '2100', 'Current Liabilities', 'Liability', 'Current Liability', 'Credit', 1, 1, NOW()),
            (UUID(), 'system', '2110', 'Accounts Payable', 'Liability', 'Current Liability', 'Credit', 1, 1, NOW()),
            (UUID(), 'system', '2120', 'VAT Payable', 'Liability', 'Current Liability', 'Credit', 1, 1, NOW()),
            (UUID(), 'system', '2200', 'Long-term Liabilities', 'Liability', 'Long-term Liability', 'Credit', 1, 1, NOW()),
            (UUID(), 'system', '2210', 'Loans Payable', 'Liability', 'Long-term Liability', 'Credit', 1, 1, NOW()),

            -- Equity
            (UUID(), 'system', '3000', 'Equity', 'Equity', 'Equity', 'Credit', 1, 1, NOW()),
            (UUID(), 'system', '3100', 'Share Capital', 'Equity', 'Equity', 'Credit', 1, 1, NOW()),
            (UUID(), 'system', '3200', 'Retained Earnings', 'Equity', 'Equity', 'Credit', 1, 1, NOW()),

            -- Revenue
            (UUID(), 'system', '4000', 'Revenue', 'Revenue', 'Operating Revenue', 'Credit', 1, 1, NOW()),
            (UUID(), 'system', '4100', 'Sales Revenue', 'Revenue', 'Operating Revenue', 'Credit', 1, 1, NOW()),
            (UUID(), 'system', '4200', 'Service Revenue', 'Revenue', 'Operating Revenue', 'Credit', 1, 1, NOW()),

            -- Expenses
            (UUID(), 'system', '5000', 'Expenses', 'Expense', 'Operating Expense', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '5100', 'Cost of Goods Sold', 'Expense', 'Operating Expense', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '5200', 'Operating Expenses', 'Expense', 'Operating Expense', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '5210', 'Salaries and Wages', 'Expense', 'Operating Expense', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '5220', 'Rent Expense', 'Expense', 'Operating Expense', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '5230', 'Utilities', 'Expense', 'Operating Expense', 'Debit', 1, 1, NOW()),
            (UUID(), 'system', '5240', 'Depreciation Expense', 'Expense', 'Operating Expense', 'Debit', 1, 1, NOW())
        ");
    }

    public function down()
    {
        $this->forge->dropTable('accounts');
    }
}
