<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\UUID;

class AddAdministratorRole extends Seeder
{
    public function run()
    {
        // Get the database connection
        $db = \Config\Database::connect();
        $businessQuery  = $db->query('SELECT uuid FROM businesses');
        $businessUUIDs = $businessQuery->getResultArray();

        $roleQuery = $db->query('SELECT role_name FROM roles WHERE uuid_business_id = NULL');
        $isRoleExists = $roleQuery->getRow();
        if (!$isRoleExists && empty($isRoleExists) && !isset($isRoleExists)) {
            $saUUID = UUID::v4();
            $sUUID = UUID::v5($saUUID, 'roles');
            $data = [
                'uuid' => $sUUID,
                'role_name'  => "Administrator",
                'uuid_business_id'  => NULL,
            ];
            $db->table('roles')->insert($data);
            $db->query("UPDATE `users` SET `role` = '$sUUID' WHERE `id` = 1");
        }
        // foreach ($businessUUIDs as $businessUUID) {
        //     $bID = $businessUUID['uuid'];
        //     $roleQuery = $db->query('SELECT role_name FROM roles WHERE uuid_business_id = "' . $bID . '"');
        //     $isRoleExists = $roleQuery->getRow();
        //     if (!$isRoleExists && empty($isRoleExists) && !isset($isRoleExists)) {
        //         $uuidNamespace = UUID::v4();
        //         $uuid = UUID::v5($uuidNamespace, 'roles');
        //         $data = [
        //             'uuid' => $uuid,
        //             'role_name'  => "Admin",
        //             'uuid_business_id'  => $bID,
        //         ];
        //         $db->table('roles')->insert($data);
        //     }
        // }
    }
}
