<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;


class Businesses extends CommonController
{
	public function __construct()
	{
		parent::__construct();
	}


	public function index()
	{
		$roles = getResultWithoutBusiness("roles", ["uuid" => $_SESSION['role']], false);
		$data['columns'] = $this->db->getFieldNames($this->table);
		$data['fields'] = array_diff($data['columns'], $this->notAllowedFields);
		$data[$this->table] = getWithOutUuidResultArray("businesses");
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		if ((@$_SESSION['role'] && $roles['role_name'] == "Administrator") || session('uuid') == 1) {
			$data['is_add_permission'] = 1;
		} else {
			$data['is_add_permission'] = 0;
		}
		$data['identifierKey'] = 'id';

		$viewPath = "common/list";
		if (file_exists(APPPATH . 'Views/' . $this->table . "/list.php")) {
			$viewPath = $this->table . "/list";
		}

		return view($viewPath, $data);
	}


	public function update()
	{
		$uuid = $this->request->getPost('uuid');

		$data = $this->request->getPost();
		if (isset($data['default_business'])) {
			$data['default_business'] = 1;
			$this->db->table($this->table)->update(array('default_business' => 0));
		}
		$data['business_contacts'] = json_encode(@$data['business_contacts']);
		if (empty($uuid)) {
			$uuidNamespace = UUID::v4();
			$data['uuid'] = UUID::v5($uuidNamespace, 'businesses');
		}

		$response = $this->model->insertOrUpdateByUUID($uuid, $data);
		if (!$response) {
			session()->setFlashdata('message', 'Something wrong!');
			session()->setFlashdata('alert-class', 'alert-danger');
		}

		return redirect()->to('/' . $this->table);
	}

	public function edit($uuid = '')
	{
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data["users"] = $this->model->getUser();
		$data[$this->rawTblName] = getRowArray($this->table, ['uuid' => $uuid]);
		// if there any special cause we can overried this function and pass data to add or edit view
		$data['additional_data'] = $this->getAdditionalData($uuid);
		$data['role'] = $this->session->get('role');
		echo view($this->table . "/edit", $data);
	}
}
