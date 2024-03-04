<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\UUID;

class UpdateMenusSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $rolesQuery  = $db->query('SELECT link FROM menu WHERE link = "/roles"');
        $isRoleExists = $rolesQuery->getRowArray();
        if (empty($isRoleExists)) {
            $saUUID = UUID::v4();
            $sUUID = UUID::v5($saUUID, 'menu');
            $data = [
                'uuid' => $sUUID,
                'name'  => "Roles",
                'link'  => "/roles",
                'icon'  => "fa fa-tasks",
                'uuid_business_id'  => NULL,
                'sort_order'  => 555,
                'language_code'  => "en",
                'menu_fts'  => "roles",
            ];
            $db->table('menu')->insert($data);
        }
    }
}
