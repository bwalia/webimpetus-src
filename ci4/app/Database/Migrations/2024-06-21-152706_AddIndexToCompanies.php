<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexToCompanies extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `companies` ADD INDEX(`company_number`, `company_name`);");
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `companies` DROP INDEX `company_number`;');
        $this->db->query('ALTER TABLE `companies` DROP INDEX `company_name`;');
    }
}
