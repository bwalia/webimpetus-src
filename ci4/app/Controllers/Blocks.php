<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Blocks_model;
use App\Models\Users_model;
use App\Controllers\Core\CommonController;
use App\Models\Core\Common_model;
use App\Libraries\UUID;


class Blocks extends CommonController
{
	protected $blocks_model;
	protected $user_model;
	function __construct()
	{
		parent::__construct();
		$this->table = "blocks_list";
		$this->rawTblName =  "blocks";
		$this->blocks_model = new Blocks_model();
		$this->user_model = new Users_model();
	}

	public function index()
	{
		$data['rawTblName'] = $this->rawTblName;
		$data['tableName'] = "blocks";
		$data[$this->rawTblName] = $this->blocks_model->where(["uuid_business_id" => $this->businessUuid])->findAll();
		echo view($this->table . '/list', $data);
	}


	public function edit($id = 0)
	{
		$data['role'] = $this->session->get('role');
		$data['tableName'] = "blocks";
		$data[$this->rawTblName] = $this->blocks_model->getRowsByUUID($id)->getRow();
		$data['users'] = $this->user_model->getUser();
		echo view($this->table . '/edit', $data);
	}


	public function update()
	{
		$role = $this->session->get('role');
		if ($role != 2) {
			$text = $this->request->getPost('text');
			if (strpos($text, '<?') !== false || strpos($text, '?>') !== false) {
				session()->setFlashdata('message', 'You are not allowed to enter PHP code as block content!');
				session()->setFlashdata('alert-class', 'alert-danger');
				return redirect()->to('/' . $this->rawTblName);
			}
		}

		$data = array(
			'code' => $this->request->getPost('code'),
			'title' => $this->request->getPost('title'),
			'status' => $this->request->getPost('status'),
			'text' => $this->request->getPost('text'),
			"uuid_business_id" => $this->businessUuid,
		);

		$uuid = $this->request->getPost('uuid');
		if ($uuid > 0) {
			$this->blocks_model->updateDataByUUID($uuid, $data);
			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		} else {
			$data['uuid'] = UUID::v5(UUID::v4(), 'block_list');
			$this->blocks_model->saveData($data);
			session()->setFlashdata('message', 'Data entered Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}


		return redirect()->to('/' . $this->rawTblName);
	}

	public function delete($id)
	{
		//echo $id; die;
		if (!empty($id)) {
			$response = $this->blocks_model->deleteData($id);
			if ($response) {
				session()->setFlashdata('message', 'Data deleted Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
			} else {
				session()->setFlashdata('message', 'Something wrong delete failed!');
				session()->setFlashdata('alert-class', 'alert-danger');
			}
		}

		return redirect()->to('/blocks');
	}
}
