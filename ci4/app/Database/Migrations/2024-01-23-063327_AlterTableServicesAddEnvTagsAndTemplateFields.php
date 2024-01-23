<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTableServicesAddEnvTagsAndTemplateFields extends Migration
{
    public function up()
    {
        $fields = [
            'env_tags' => ['type' => 'TEXT'],
        ];
        $this->forge->addColumn('services', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('services', 'env_tags');
    }
}
