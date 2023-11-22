<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Menu_model;
use App\Models\RolesModel;
use App\Models\RolesPermissionsModel;

class Roles extends CommonController
{
    protected $rolesModel;
    protected $menuModel;
    protected $rolesPermissionModel;
    public function __construct()
    {
        parent::__construct();
        $this->rolesModel = new RolesModel();
        $this->menuModel = new Menu_model();
        $this->rolesPermissionModel = new RolesPermissionsModel();
    }
    public function index()
    {
        $data['tableName'] = "roles";
        $data['rawTblName'] = "roles";
		$data['roles'] = $this->rolesModel->getRowsByUUID();
		return view('roles/list', $data);
    }

    public function edit($uuid = 0)
	{   
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['roleData'] = $this->rolesModel->getRowsByUUID($uuid)->getRow();
		$data['permissions'] = $this->menuModel->getRows();
		$data['selectedPermissions'] = $this->rolesPermissionModel->getRowsByRole($uuid);

		return view('roles/edit', $data);
	}

    public function update()
	{
		$uuid = $this->request->getPost('uuid');
		$roleData = array(
			'role_name' => $this->request->getPost('role_name'),
            'uuid_business_id' => session('uuid_business')
		);
		if (!$uuid || empty($uuid) || !isset($uuid)) {
            $roleData['uuid'] = UUID::v5(UUID::v4(), 'roles');
        }
		$id = $this->rolesModel->insertOrUpdateByUUID($uuid, $roleData);

		if ($id) {
			$roleId = $id;
			$this->rolesPermissionModel->deleteDataByRole($id);
			if (!empty($this->request->getPost('user_permissions'))) {
				foreach ($this->request->getPost('user_permissions') as $val) {

					$this->rolesPermissionModel->saveData(array(
						'permission_id' => $val,
						'role_id' => $roleId,
						'uuid' => UUID::v5(UUID::v4(), 'roles__permissions')
					));
				}
			}
		} else {
			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}


		return redirect()->to('/roles');
	}
}
