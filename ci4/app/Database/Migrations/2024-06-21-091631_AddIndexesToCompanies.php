<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesToCompanies extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE `companies` ADD INDEX(`uuid`, `uuid_business_id`);');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `companies` DROP INDEX `uuid`;');
        $this->db->query('ALTER TABLE `companies` DROP INDEX `uuid_business_id`;');
    }
}
