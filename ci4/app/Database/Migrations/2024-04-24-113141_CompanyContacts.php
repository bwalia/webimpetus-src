<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CompanyContacts extends Migration
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
            'company_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'contact_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('company__contact');
    }

    public function down()
    {
        $this->forge->dropTable('company__contact');
    }
}
