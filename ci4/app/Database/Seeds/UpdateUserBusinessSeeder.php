<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\User_business_model;
use App\Models\Users_model;
use App\Libraries\UUID;

class UpdateUserBusinessSeeder extends Seeder
{
    public function run()
    {
        $userBsModel = new User_business_model();
        $userModel = new Users_model();
        $userBusinesses = $userBsModel->getRows();
        $bsData = [];
        foreach ($userBusinesses as $key => $userBusiness) {
            if (empty($userBusiness['uuid']) and !isset($userBusiness['uuid'])) {
                $uuidNamespace = UUID::v4();
                $uuid = UUID::v5($uuidNamespace, 'user_business');
                $bsData['uuid'] = $uuid;
            } else {
                $bsData['uuid'] = $userBusiness['uuid'];
            }
            $user = $userModel->getApiUsers($userBusiness['user_id']);
            if (!$user || empty($user) || !isset($user['uuid'])) {
                $this->db->query('DELETE FROM user_business WHERE id='.$userBusiness['id']);
            } else {
                $bsData['user_uuid'] = $user['uuid'];
                $this->db->query('UPDATE user_business SET uuid = "'. $bsData['uuid'] . '", user_uuid = "' . $bsData['user_uuid'] . '" WHERE id = '.$userBusiness['id']);
            }
        }
    }
}