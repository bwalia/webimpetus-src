<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesToCompaniesCategory extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `companies__categories` ADD INDEX(`uuid`, `company_id`, `category_id`, `uuid_business_id`);");
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `companies` DROP INDEX `uuid`;');
        $this->db->query('ALTER TABLE `companies` DROP INDEX `company_id`;');
        $this->db->query('ALTER TABLE `companies` DROP INDEX `category_id`;');
        $this->db->query('ALTER TABLE `companies` DROP INDEX `uuid_business_id`;');
    }
}
