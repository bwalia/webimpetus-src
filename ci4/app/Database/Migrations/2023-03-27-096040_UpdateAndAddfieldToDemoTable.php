<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAndAddfieldToDemoTable extends Migration
{
    public function up(){

        ## Rename column name from emp_name to fullname 
        $alterfields = [
             'emp_name' => [
                   'name' => 'fullname',
                   'type' => 'VARCHAR',
                   'constraint' => '100',
             ],
        ];
        $this->forge->modifyColumn('demo', $alterfields);

        ## Add age column
        $addfields = [
            'phonenumber' => [
                    'type' => 'INT',
                    'constraint' => '25',
                ],
        ];
        $this->forge->addColumn('demo', $addfields);
    }

    public function down(){
        
        ## Delete 'age' column
        $this->forge->dropColumn('demo', ['phonenumber']);

        ## Rename column name from fullname to emp_name
        $fields = [
             'fullname' => [
                   'name' => 'emp_name',
                   'type' => 'VARCHAR',
                   'constraint' => '100',
             ],
        ];
        $this->forge->modifyColumn('demo', $fields);
    }
}
