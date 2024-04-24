<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Companies extends Migration
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
            'company_number' => [
                'type' => 'INT',
                'constraint' => 10
            ],
            'company_name' => [
                'type' => 'VARCHAR',
                'constraint' => 128
            ],
            'address_1' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'address_2' => [
                'type' => 'TEXT'
            ],
            'address_3' => [
                'type' => 'TEXT'
            ],
            'town_or_city' => [
                'type' => 'VARCHAR',
                'constraint' => 128
            ],
            'state_or_county' => [
                'type' => 'VARCHAR',
                'constraint' => 128
            ],
            'post_zip_code' => [
                'type' => 'VARCHAR',
                'constraint' => 128
            ],
            'region_area' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'website' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 128
            ],
            'premises_type' => [
                'type' => 'VARCHAR',
                'constraint' => 128
            ],
            'no_of_employees' => [
                'type' => 'INT',
                'constraint' => 5
            ],
            'company_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'sic_code' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'turnover' => [
                'type' => 'VARCHAR',
                'constraint' => 128
            ],
            'company_telephone' => [
                'type' => 'VARCHAR',
                'constraint' => 128
            ],
            'company_fax' => [
                'type' => 'VARCHAR',
                'constraint' => 128
            ],
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 5
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp'

        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('companies', true);
    }

    public function down()
    {
        $this->forge->dropTable('companies');
    }
}
