<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\UUID;
use App\Models\Core\Common_model;

class UpdateUuidSeeder extends Seeder
{
    public function run()
    {
         // Get the database connection
         $db = \Config\Database::connect();
        // Get the list of tables in the database
        $tables = $db->listTables();
        $commonModel = new Common_model();
        $nonUpdateTables = ['menu_category', 'migrations', 'blog_comments'];
        foreach ($tables as $key => $table) {
            if ($db->tableExists($table) && !in_array($table, $nonUpdateTables)) {
                // Check if the 'uuid' column exists in the table
                if (!$db->fieldExists('uuid', $table)) {
                    // Add the 'uuid' column to the table if it doesn't exist
                    $db->query("ALTER TABLE $table ADD uuid CHAR(36) NULL");
                }
                $getData = $commonModel->getAllDataFromTable($table);
                foreach ($getData as $fk => $field) {
                    $id = $field['id'];
                    $uuidNamespace = UUID::v4();
                    $uuid = UUID::v5($uuidNamespace, $table);
                    // Update records where 'uuid' is empty or null
                    $query = "UPDATE $table SET uuid = '$uuid' WHERE id = $id AND (uuid IS NULL OR uuid = '')";
                    $db->query($query);
                }
    
                echo "Seeder: UpdateUuidSeeder executed successfully for " . $table . PHP_EOL;
            } else {
                echo "Seeder: UpdateUuidSeeder skipped table " . $table . PHP_EOL;
            }
        }
 
    }
}
