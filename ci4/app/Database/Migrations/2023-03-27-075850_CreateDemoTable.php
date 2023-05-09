<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDemoTable extends Migration
{
    public function up(){
        $this->forge->addField([
             'id' => [
                  'type' => 'INT',
                  'constraint' => 5,
                  'unsigned' => true,
                  'auto_increment' => true,
             ],
             'emp_name' => [
                  'type' => 'VARCHAR',
                  'constraint' => '150',
             ],
             'email' => [
                  'type' => 'VARCHAR',
                  'constraint' => '150',
             ],
             'city' => [
                  'type' => 'VARCHAR',
                  'constraint' => '150',
             ],
             'age' => [
                  'type' => 'VARCHAR',
                  'constraint' => '150',
             ],
             'salary' => [
                  'type' => 'VARCHAR',
                  'constraint' => '150',
             ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('demo');
   }

   public function down(){
        $this->forge->dropTable('demo');
   }
}
