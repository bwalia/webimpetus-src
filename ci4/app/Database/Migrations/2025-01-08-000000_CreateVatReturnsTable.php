<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVatReturnsTable extends Migration
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
                'constraint' => '64',
                'null' => false,
            ],
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => false,
            ],
            'quarter' => [
                'type' => 'INT',
                'constraint' => 1,
                'null' => false,
                'comment' => '1-4 for Q1-Q4',
            ],
            'year' => [
                'type' => 'INT',
                'constraint' => 4,
                'null' => false,
            ],
            'period_start' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'period_end' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'uk_vat_total' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Total VAT from UK customers',
            ],
            'uk_sales_total' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Total sales to UK customers',
            ],
            'non_uk_vat_total' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Total VAT from non-UK customers',
            ],
            'non_uk_sales_total' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Total sales to non-UK customers',
            ],
            'total_vat_due' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Total VAT due (UK + non-UK)',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'default' => 'draft',
                'comment' => 'draft, submitted',
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey(['uuid_business_id', 'year', 'quarter']);
        $this->forge->createTable('vat_returns');
    }

    public function down()
    {
        $this->forge->dropTable('vat_returns');
    }
}
