<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Users_model;
use App\Models\Menu_model;
use App\Controllers\Core\CommonController;
use App\Libraries\UUID;

class Users extends CommonController
{

	function __construct()
	{
		parent::__construct();
		$this->session = \Config\Services::session();
		$this->userModel = new Users_model();
		$this->menu_model = new Menu_model();
	}


	public function edit($id = '')
	{
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['user'] = !empty($id)?$this->userModel->getUserByUUID($id)->getRow():[];
		$data['menu'] = $this->menu_model->getRows();
		return view('users/edit', $data);
	}

	public function update()
	{
		$id = $this->request->getPost('id');

		if ($id) {
			$count = $this->userModel->getWhere(['email' => $this->request->getPost('email'), 'id!=' => $id])->getNumRows();
			if (!empty($count)) {
				session()->setFlashdata('message', 'Email already exist!');
				session()->setFlashdata('alert-class', 'alert-danger');
				return redirect()->to('/users/edit/' . $id);
			} else {
				$data = array(
					'name'  => $this->request->getPost('name'),
					'email' => $this->request->getPost('email'),
					'address' => $this->request->getPost('address'),
					'notes' => $this->request->getPost('notes'),
					'permissions' => json_encode($this->request->getPost('sid')),
					'role' => $this->request->getPost('role'),
					'language_code' => $this->request->getPost('language_code'),
				);
				$this->userModel->updateUser($data, $id);
				session()->setFlashdata('message', 'Data updated Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
			}
		} else {
			if (!empty($this->request->getPost('email'))) {

				$count = $this->userModel->getWhere(['email' => $this->request->getPost('email')])->getNumRows();
				if (!empty($count)) {
					session()->setFlashdata('message', 'Email already exist!');
					session()->setFlashdata('alert-class', 'alert-danger');
					return redirect()->to('/users/edit');
				} else {

					$allMenu = getWithOutUuidResultArray("menu");
					$menu_ids = array_column($allMenu, 'id');

					$uuidNamespace = UUID::v4();
					$uuid = UUID::v5($uuidNamespace, 'users');
					$data = array(
						'name'  => $this->request->getPost('name'),
						'email' => $this->request->getPost('email'),
						//'password' => md5($this->request->getPost('password')),
						'address' => $this->request->getPost('address'),
						'notes' => $this->request->getPost('notes'),
						'language_code' => $this->request->getPost('language_code')?$this->request->getPost('language_code'):'en',
						'uuid' => $uuid,
						'uuid_business_id' => session('uuid_business'),
						'password' => md5($this->request->getPost('password')),					
						'status' => 0,
						'permissions' => json_encode($menu_ids),
						'role' => $this->request->getPost('role'),
					);
					$this->userModel->saveUser($data);
					session()->setFlashdata('message', 'Data entered Successfully!');
					session()->setFlashdata('alert-class', 'alert-success');
				}
			}
		}
		return redirect()->to('/users');
	}

	public function savepwd()
	{
		if (!empty($this->request->getPost('id')) && !empty($this->request->getPost('npassword')) && $this->request->getPost('npassword') == $this->request->getPost('cpassword')) {
			$data = array(
				'password' => md5($this->request->getPost('npassword'))
			);
			$this->userModel->updateUser($data, $this->request->getPost('id'));
			session()->setFlashdata('message', 'Password changed Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}
		return redirect()->to('/users');
	}

	public function status()
	{
		if (!empty($id = $this->request->getPost('id'))) {
			$data = array(
				'status' => $this->request->getPost('status')
			);
			$this->userModel->updateUser($data, $id);
		}
		echo '1';
	}

	public function getuser($id)
	{
		$json = $this->userModel->select('permissions')->getWhere(array('id' => $id))->getRow();
		echo json_encode($json->permissions);
	}

	public function update_permission()
	{
		if ($this->request->getPost('userid') && !empty($this->request->getPost('userid'))) {
			$data = array(
				'permissions' => json_encode($this->request->getPost('items'))
			);
			$this->userModel->updateUser($data, $this->request->getPost('userid'));

			echo json_encode([]);
		} else {
			echo json_encode([]);
		}
	}
}
