<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use CodeIgniter\Controller;
use App\Models\Content_model;
use App\Libraries\UUID;
use App\Models\Users_model;
use App\Models\Cat_model;
use App\Models\ContentImage;
use App\Controllers\Core\CommonController;
use App\Models\Core\Common_model;

ini_set('display_errors', 1);

class Jobs extends CommonController
{
	public $content_model;
	public $user_model;
	public $cat_model;
	public $contentImage;
	public function __construct()
	{
		parent::__construct();
		$this->content_model = new Content_model();
		$this->user_model = new Users_model();
		$this->cat_model = new Cat_model();
		$this->contentImage = new ContentImage();
	}
	public function index()
	{
		$data['content'] = $this->content_model->where(['type' => 4, "uuid_business_id" => $this->businessUuid])->findAll();
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['is_add_permission'] = 1;
		echo view($this->table . "/list", $data);
	}

	public function jobsList()
	{
		$limit = $this->request->getVar('limit');
		$offset = $this->request->getVar('offset');
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "title";
		$dir = $this->request->getVar('dir') ?? "asc";

		$sqlQuery = $this->content_model
			->select("uuid, id, title, sub_title, status, publish_date, created")
			->where(['type' => 4, "uuid_business_id" => $this->businessUuid]);
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

	public function edit($id = 0)
	{
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['content'] = [];
		if ($id) {
			$data['content'] = $this->content_model->getRows($id)->getRow();
		}

		$data['images'] = [];
		$data["blocks_list"] = [];
		if (!empty($id)) {
			$data['images'] = $this->model->getDataWhere("media_list", $id, "uuid_linked_table");
			$data["blocks_list"] = $this->model->getDataWhere("blocks_list", $id, "uuid_linked_table");
		}

		$data['users'] = $this->user_model->getUser();
		$data['cats'] = $this->cat_model->getCats();

		$array1 = $this->cat_model->getCatIds(@$data['content']->id);

		$arr = array_map(function ($value) {
			return $value['categoryid'];
		}, $array1);
		$data['selected_cats'] = $arr;

		echo view($this->table . "/edit", $data);
	}

	public function insertOrUpdate($table, $id = null, $data = null)
	{
		unset($data["id"]);

		if (@$id > 0) {

			$builder = $this->db->table($table);
			$builder->where('id', $id);
			$result = $builder->update($data);

			if ($result) {
				session()->setFlashdata('message', 'Data updated Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
				return $id;
			}
		} else {
			$query = $this->db->table($table)->insert($data);
			if ($query) {
				session()->setFlashdata('message', 'Data updated Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
				return $this->db->insertID();
			}
		}

		return false;
	}


	public function update()
	{
		$id = $this->request->getPost('id');
		$uuid = $this->request->getPost('uuid');
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
			//'code' => $this->request->getPost('code') ? $this->content_model->format_uri($this->request->getPost('code'), '-', $id) : $this->content_model->format_uri($this->request->getPost('title'), '-', $id),
			'code' => $this->request->getPost('code'),
			'meta_keywords' => $this->request->getPost('meta_keywords'),
			'meta_title' => $this->request->getPost('meta_title'),
			'meta_description' => $this->request->getPost('meta_description'),
			'status' => $this->request->getPost('status'),
			'publish_date' => ($this->request->getPost('publish_date') ? strtotime($this->request->getPost('publish_date') ?? "") : strtotime(date('Y-m-d H:i:s'))),
			'custom_fields' => json_encode($cus_fields),
			'type' => ($this->request->getPost('type') ? $this->request->getPost('type') : 1),
			'user_uuid' => $this->request->getPost('user_uuid'),
			'language_code' => $this->request->getPost('language_code')
		);

		if (!empty($id)) {
			$this->content_model->updateData($id, $data);
			if (!empty($id) && !empty($this->request->getPost('catid'))) {
				$this->cat_model->deleteCatData($id);
				foreach ($this->request->getPost('catid') as $val) {
					$cat_data = [];
					$cat_data['categoryid'] = $val;
					$cat_data['contentid'] = $id;
					$cat_data['uuid_business_id'] = session('uuid_business');
					$this->cat_model->saveData2($cat_data);
				}
			}

			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		} else {

			$uuid = $data['uuid'] = UUID::v5(UUID::v4(), 'jobs');

			$id = $this->content_model->saveData($data);

			if (!empty($bid) && !empty($this->request->getPost('catid'))) {
				foreach ($this->request->getPost('catid') as $val) {
					$cat_data = [];
					$cat_data['categoryid'] = $val;
					$cat_data['contentid'] = $id;
					$this->cat_model->saveData2($cat_data);
				}
			}

			session()->setFlashdata('message', 'Data entered Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}

		$files = $this->request->getPost("file");
		if (!empty($id)) {
			$row = $this->content_model->getRows($uuid)->getRow();
			$filearr = ($row->custom_assets != "") ? json_decode($row->custom_assets) : [];
			$count = !empty($filearr) ? count($filearr) : 0;

			if (is_array($files)) {
				foreach ($files as $key => $filePath) {
					$job_images = [];
					$job_images['uuid_business_id'] =  session('uuid_business');
					$job_images['name'] = $filePath;
					$job_images['uuid_linked_table'] = $uuid;
					$this->content_model->saveDataInTable($job_images, "media_list");
				}
			}
			$this->content_model->updateData($id, $data);
			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		} else {

			if (is_array($files)) {
				foreach ($files as $key => $filePath) {
					$job_images = [];
					$job_images['uuid_business_id'] =  session('uuid_business');
					$job_images['name'] = $filePath;
					$job_images['uuid_linked_table'] = $data['uuid'];

					$this->content_model->saveDataInTable($job_images, "media_list");
				}
			}
			session()->setFlashdata('message', 'Data entered Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}

		if ($id > 0) {
			$i = 0;
			$post = $this->request->getPost();
			if (isset($post["blocks_code"])) {
				foreach ($post["blocks_code"] as $code) {
					$blocks = [];
					$blocks["code"] = $code;
					$blocks["uuid_linked_table"] = $uuid;
					$blocks["text"] = $post["blocks_text"][$i];
					$blocks["title"] = $post["blocks_title"][$i];
					$blocks["sort"] = $post["sort"][$i];
					$blocks["type"] = $post["block_type"][$i];

					$blocks["uuid_business_id"] = session('uuid_business');
					$blocks_id =  @$post["blocks_id"][$i];
					if (empty($blocks["sort"])) {
						$blocks["sort"] = $blocks_id;
					}
					$blocks_id = $this->insertOrUpdate("blocks_list", $blocks_id, $blocks);
					if (empty($blocks["sort"])) {
						$this->insertOrUpdate("blocks_list", $blocks_id, ["sort" => $blocks_id]);
					}
					$i++;
				}
			} else {
				$this->model->deleteTableData("blocks_list", $uuid, "uuid_linked_table");
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


	public function delete($id)
	{
		if (!empty($id)) {
			$response = $this->content_model->deleteData($id);
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


	public function deleteBlocks()
	{
		$blocks_id = $this->request->getPost("blocks_id");
		$res = $this->model->deleteTableData("blocks_list", $blocks_id);

		return $res;
	}


	public function rmimg($id, $rowId)
	{
		if (!empty($id)) {
			$this->model->deleteTableData("media_list", $id);
			session()->setFlashdata('message', 'Image deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}
		return redirect()->to('//' . $this->table . '/edit/' . $rowId);
	}
}
