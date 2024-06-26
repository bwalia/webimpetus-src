<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\UUID;

use CodeIgniter\Controller;
use App\Models\Content_model;
use App\Models\Users_model;
use App\Models\Cat_model;
use App\Controllers\Core\CommonController;

ini_set('display_errors', 1);

class Jobapps extends CommonController
{
	protected $user_model;
	protected $cat_model;
	public function __construct()
	{
		parent::__construct();
		$this->model = new Content_model();
		$this->user_model = new Users_model();
		$this->cat_model = new Cat_model();
	}
	public function index()
	{
		$data['content'] = $this->model->where(['type' => 3, "uuid_business_id" => $this->businessUuid])->findAll();
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['is_add_permission'] = 1;
		echo view($this->table . "/list", $data);
	}

	public function jobAppList()
	{
		$limit = $this->request->getVar('limit');
		$offset = $this->request->getVar('offset');
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "title";
		$dir = $this->request->getVar('dir') ?? "asc";

		$sqlQuery = $this->model
			->select("uuid, id, title, sub_title, status, publish_date, created")
			->where(['type' => 3, 'uuid_business_id' => session('uuid_business')]);
		if ($query) {
			$sqlQuery = $sqlQuery->like("title", $query);
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
	public function edit($id = "")
	{

		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['content'] = $this->model->getRows($id)->getRow();
		$data['users'] = $this->user_model->getUser();
		$data['cats'] = $this->cat_model->getCats();

		$array1 = $this->cat_model->getCatIds($id);

		$arr = array_map(function ($value) {
			return $value['categoryid'];
		}, $array1);
		$data['selected_cats'] = $arr;

		echo view($this->table . "/edit", $data);
	}

	public function rmimg($id)
	{
		if (!empty($id)) {
			$data['custom_assets'] = null;
			$this->model->updateData($id, $data);
			session()->setFlashdata('message', 'Image deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}
		return redirect()->to('//jobs/edit/' . $id);
	}

	public function update()
	{
		$id = $this->request->getPost('id');
		$cus_fields = [];
		$cus_fields['reference'] = $this->request->getPost('reference');
		$cus_fields['job_type'] = $this->request->getPost('job_type');
		$cus_fields['salary'] = $this->request->getPost('salary');
		$cus_fields['employer'] = $this->request->getPost('employer');
		$cus_fields['jobstatus'] = $this->request->getPost('jobstatus');
		$cus_fields['location'] = $this->request->getPost('location');
		$data = array(
			'title'  => $this->request->getPost('title'),
			'sub_title' => $this->request->getPost('sub_title'),
			'content' => $this->request->getPost('content'),
			'code' => $this->request->getPost('code') ? $this->model->format_uri($this->request->getPost('code'), '-', $id) : $this->model->format_uri($this->request->getPost('title'), '-', $id),
			'meta_keywords' => $this->request->getPost('meta_keywords'),
			'meta_title' => $this->request->getPost('meta_title'),
			'meta_description' => $this->request->getPost('meta_description'),
			'status' => $this->request->getPost('status'),
			'publish_date' => ($this->request->getPost('publish_date') ? strtotime($this->request->getPost('publish_date') ?? "") : strtotime(date('Y-m-d H:i:s'))),
			'custom_fields' => json_encode($cus_fields),
			'type' => ($this->request->getPost('type') ? $this->request->getPost('type') : 3),
			//'image_logo' => $filepath
		);

		if (!empty($this->request->getPost('uuid'))) {
			$data['uuid'] = $this->request->getPost('uuid');
		}

		if (!empty($id)) {

			$this->model->updateData($id, $data);

			if (!empty($id) && !empty($this->request->getPost('catid'))) {
				$this->cat_model->deleteCatData($id);
				foreach ($this->request->getPost('catid') as $val) {
					$cat_data = [];
					$cat_data['categoryid'] = $val;
					$cat_data['contentid'] = $id;
					$this->cat_model->saveData2($cat_data);
				}
			}

			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		} else {

			$uuidVal = UUID::v5(UUID::v4(), 'job_applications');
			$data['uuid'] = $uuidVal;


			$bid = $this->model->saveData($data);

			if (!empty($bid) && !empty($this->request->getPost('catid'))) {

				foreach ($this->request->getPost('catid') as $val) {
					$cat_data = [];
					$cat_data['categoryid'] = $val;
					$cat_data['contentid'] = $bid;
					$this->cat_model->saveData2($cat_data);
				}
			}

			session()->setFlashdata('message', 'Data entered Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}
		return redirect()->to('/' . $this->table);
	}

	public function delete($id)
	{
		//echo $id; die;
		if (!empty($id)) {
			$response = $this->model->deleteData($id);
			if ($response) {
				session()->setFlashdata('message', 'Data deleted Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
			} else {
				session()->setFlashdata('message', 'Something wrong delete failed!');
				session()->setFlashdata('alert-class', 'alert-danger');
			}
		}

		return redirect()->to('/' . $this->table);
	}
	public function upload($filename = null)
	{
		$input = $this->validate([
			$filename => "uploaded[$filename]|max_size[$filename,1024]|ext_in[$filename,jpg,jpeg,docx,pdf],"
		]);

		if (!$input) { // Not valid
			return '';
		} else { // Valid

			if ($file = $this->request->getFile($filename)) {
				if ($file->isValid() && !$file->hasMoved()) {
					// Get file name and extension
					$name = $file->getName();
					$ext = $file->getClientExtension();

					// Get random file name
					$newName = $file->getRandomName();

					// Store file in public/uploads/ folder
					$file->move('../public/uploads', $newName);

					// File path to display preview
					return $filepath = base_url() . "/uploads/" . $newName;
				}
			}
		}
	}
}
