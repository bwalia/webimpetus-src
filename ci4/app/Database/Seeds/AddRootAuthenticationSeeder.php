<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddRootAuthenticationSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = '/opt/nginx/data/';
        $jsonFilePath = '/opt/nginx/data/root-auth.json';
        if (!is_dir($jsonPath)) {
            mkdir($jsonPath, 0755, true);
        }
        if (file_exists($jsonFilePath)) {
            echo 'JSON file already exists.';
            return;
        }
        $jsonData = [
            'email' => 'admin@admin.com',
            'password' => 'e8438c20e70da5ecf8a10e6b970d703d'
        ];
        $jsonContent = json_encode($jsonData, JSON_PRETTY_PRINT);

        if (write_file($jsonFilePath, $jsonContent)) {
            echo 'JSON file created successfully.';
        } else {
            echo 'Unable to create JSON file.';
        }
    }
}
