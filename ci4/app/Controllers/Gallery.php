<?php

namespace App\Controllers;

use App\Models\Gallery_model;
use App\Models\Users_model;
use App\Controllers\Core\CommonController;
use App\Libraries\UUID;

ini_set('display_errors', 1);

class Gallery extends CommonController
{
	protected $gallery_model;
	protected $user_model;
	protected $gallery;
	public function __construct()
	{
		parent::__construct();
		$this->gallery_model = new Gallery_model();
		$this->user_model = new Users_model();
		$this->table = 'media_list';
		$this->gallery = 'gallery';
		$menucode = $this->getMenuCode("/gallery");
		$this->session->set("menucode", $menucode);
	}

	public function index()
	{

		$data[$this->table] = $this->gallery_model->where(["uuid_business_id" => $this->businessUuid])->findAll();
		$data['tableName'] = $this->gallery;
		$data['rawTblName'] = "Galary";
		$data['is_add_permission'] = 1;

		echo view($this->table . "/list", $data);
	}

	public function gallaryList()
	{
		$limit = $this->request->getVar('limit');
		$offset = $this->request->getVar('offset');
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "id";
		$dir = $this->request->getVar('dir') ?? "asc";

		$sqlQuery = $this->gallery_model
			->select("uuid, id, code, status, created")
			->where(["uuid_business_id" => $this->businessUuid]);
		if ($query) {
			$sqlQuery = $sqlQuery->like("code", $query);
		}

        $countQuery = $sqlQuery->countAllResults(false);
        $sqlQuery = $sqlQuery->limit($limit, $offset)->orderBy($order, $dir);

		$data = [
			'rawTblName' => $this->rawTblName,
			'tableName' => $this->table,
			'data' => $sqlQuery->get()->getResultArray(),
			'recordsTotal' => $countQuery,
		];
		return $this->response->setJSON($data);
	}

	public function getMenuCode($value)
	{
		$result = $this->db->table("menu")->getWhere([
			"link" => $value
		])->getRowArray();

		return @$result['id'];
	}

	public function edit($uuid = 0)
	{
		$data['rawTblName'] = "Galary";
		$data['tableName'] = $this->gallery;
		$data[$this->table] = $uuid ? $this->gallery_model->getRowsByUUID($uuid)->getRow() : "";
		$data['users'] = $this->user_model->getUser();
		echo view($this->table . '/edit', $data);
	}


	public function update()
	{
		$data = array(
			'code' => $this->request->getPost('code'),
			'status' => $this->request->getPost('status'),
			"uuid_business_id" => $this->businessUuid,
		);

		$id = $this->request->getPost('id');
		$uuid = $this->request->getPost('uuid');
		$file = $this->request->getPost('file');
		if ($file && !empty($file) && strlen($file) > 0) {
			$data['name'] = $file;
		}
		if ($uuid > 0) {

			$this->gallery_model->updateDataByUUID($uuid, $data);

			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		} else {


			if (!empty($file) && strlen($file) > 0) {
				$data['uuid'] =  UUID::v5(UUID::v4(), 'gallery');
				session()->setFlashdata('message', 'Data entered Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');

				$this->gallery_model->saveData($data);
			} else {

				session()->setFlashdata('message', 'File not uploaded.');
				session()->setFlashdata('alert-class', 'alert-danger');
			}
		}
		return redirect()->to('/' . $this->gallery);
	}


	public function delete($id)
	{

		if (!empty($id)) {
			$this->gallery_model->deleteData($id);
			session()->setFlashdata('message', 'Data deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}

		return redirect()->to('/' . $this->gallery);
	}
	public function delete_task($id, $url)
	{

		if (!empty($id)) {
			$this->gallery_model->deleteData($id);
			session()->setFlashdata('message', 'Data deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}

		return redirect()->to(base64_decode($url));
	}
}
