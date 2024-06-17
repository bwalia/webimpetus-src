<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableCompaniesCategories extends Migration
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
            'company_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'category_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'

        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('companies__categories', true);
    }

    public function down()
    {
        $this->forge->dropTable('companies__categories');
    }
}
