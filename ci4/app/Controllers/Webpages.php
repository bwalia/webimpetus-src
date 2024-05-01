<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Controller;
use App\Models\Content_model;
use App\Models\Users_model;
use App\Controllers\Core\CommonController;
use App\Libraries\UUID;

ini_set('display_errors', 1);


class Webpages extends CommonController
{
	public $content_model;
	public $user_model;
	public function __construct()
	{
		parent::__construct();
		$this->content_model = new Content_model();
		$this->user_model = new Users_model();
	}
	public function index()
	{
		$menuName = '';
		if (isset($_GET['cat']) && $_GET['cat'] == 'strategies') {
			$menuName = $_GET['cat'];
		}
		$keyword = $this->request->getVar('query');
        $pager = \Config\Services::pager();
        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
			'menuName' => $menuName,
			'is_add_permission' => 1,
            $this->table => $this->content_model->where(['type' => 1, "uuid_business_id" => $this->businessUuid])->search($keyword)->paginate(10),
            'pager'     => $this->content_model->pager,
        ];

		echo view($this->table . "/list", $data);
	}


	public function edit($id = 0)
	{
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;

		$data['webpage'] = [];
		$data['images'] = [];
		if (!empty($id)) {
			$data['webpage'] = $this->content_model->getRows($id)->getRow();
			$data['images'] = $this->model->getDataWhere("media_list", $id, "uuid_linked_table");
			$fieldsAllData = $this->model->getDataWhere("content_list__custom_fields", $id, "content_list_id");
			if (!empty($fieldsAllData)) {
				$fieldsIds = array_map(function ($v, $k) {
					return $v['custom_field_id'];
				}, $fieldsAllData, array_keys($fieldsAllData));

				if (!empty($fieldsIds)) {
					$data['custom_fields'] = $this->model->getDataWhereIN('custom_fields', $fieldsIds, 'uuid');
				} else {
					$data['custom_fields'] = [];
				}
				
			} else {
				$data['custom_fields'] = [];
			}
		} else {
			$data['custom_fields'] = [];
		}
		
		$data['users'] = $this->user_model->getUser();
		if (isset($_GET['cat']) && $_GET['cat'] == 'strategies') {
			$data['menuName'] = $_GET['cat'];
		}

		echo view($this->table . "/edit", $data);
	}


	public function update()
	{
		// $post = $this->request->getPost();
		// echo '<pre>'; print_r($post); echo '</pre>'; die;
		
		$id = $this->request->getPost('id');
		$uuid = $this->request->getPost('uuid');
		$menuName = $this->request->getPost('strategies') ?? "";
		$data = array(
			'title'  => $this->request->getPost('title'),
			'sub_title' => $this->request->getPost('sub_title'),
			'content' => $this->request->getPost('content'),
			'meta_keywords' => $this->request->getPost('meta_keywords'),
			'published_date' => $this->request->getPost('published_date') ? strtotime($this->request->getPost('published_date') ?? 0) : strtotime(date('Y-m-d H:i:s')),
			'meta_title' => $this->request->getPost('meta_title'),
			'meta_description' => $this->request->getPost('meta_description'),
			'status' => $this->request->getPost('status'),
			'publish_date' => ($this->request->getPost('publish_date') ? strtotime($this->request->getPost('publish_date') ?? 0) : strtotime(date('Y-m-d H:i:s'))),
			"categories" => json_encode($this->request->getPost('categories')),
			'user_uuid' => $this->request->getPost('user_uuid'),
			'language_code' => $this->request->getPost('language_code')
		);
		$post = $this->request->getPost();

		$i = 0;
		if (isset($post["blocks_code"])) {
		foreach ($post["blocks_code"] as $code) {
			if ($post["type"][$i] == 'JSON') {
				@json_decode($post["blocks_text"][$i]);
				if (json_last_error() !== JSON_ERROR_NONE) {
					session()->setFlashdata('message', 'JSON is not valid');
					session()->setFlashdata('alert-class', 'alert-danger');
					$fromWhere = "";
					if (strlen($menuName) > 1) {
						$fromWhere = "?cat=$menuName";
					}
					return redirect()->to('/' . $this->table . $fromWhere);
				}
			} else if ($post["type"][$i] == 'YAML') {
				// if(yaml_parse($post["blocks_text"][$i])===null)
				// {
				// 	session()->setFlashdata('message', 'YAML is not valid');
				// 	session()->setFlashdata('alert-class', 'alert-danger');
				// 	$fromWhere = "";
				// 	if(strlen($menuName) > 1){
				// 		$fromWhere = "?cat=$menuName";
				// 	}
				// 	return redirect()->to('/'.$this->table.$fromWhere);
				// }
			}
			$i++;
		}
		}
		$i = 0;

		$files = $this->request->getPost("file");

		if (!empty($id)) {

			$row = $this->content_model->getRows($uuid)->getRow();

			$filearr = ($row->custom_assets != "") ? json_decode($row->custom_assets) : [];
			$count = !empty($filearr) ? count($filearr) : 0;


			if (is_array($files)) {
				foreach ($files as $key => $filePath) {

					$blog_images = [];
					$blog_images['uuid_business_id'] =  session('uuid_business');
					$blog_images['name'] = $filePath;
					$blog_images['uuid_linked_table'] = $uuid;

					$this->content_model->saveDataInTable($blog_images, "media_list");
				}
			}


			$this->content_model->updateData($id, $data);

			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		} else {
			$uuid = $data['uuid'] = UUID::v5(UUID::v4(), 'webpages');
			$id = $this->content_model->saveData($data);

			if (is_array($files)) {
				foreach ($files as $key => $filePath) {

					$blog_images = [];
					$blog_images['uuid_business_id'] =  session('uuid_business');
					$blog_images['name'] = $filePath;
					$blog_images['uuid_linked_table'] = $uuid;

					$this->content_model->saveDataInTable($blog_images, "media_list");
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
					$blocks["webpages_id"] = $id;
					$blocks["text"] = $post["blocks_text"][$i];
					$blocks["title"] = $post["blocks_title"][$i];
					$blocks["sort"] = $post["sort"][$i];
					$blocks["type"] = $post["type"][$i];
					$blocks["uuid_linked_table"] = $uuid;
					$blocks["uuid_business_id"] = session('uuid_business');
					$blocks_id =  @$post["blocks_id"][$i];
					$blocks_uuid =  @$post["blocks_uuid"][$i];
					if (!$blocks_uuid || !isset($blocks_uuid)) {
						$blocks['uuid'] = UUID::v5(UUID::v4(), 'blocks_list');
					} else {
						$blocks['uuid'] = $blocks_uuid;
					}
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


			$this->model->deleteTableData("webpage_categories", $id, "webpage_id");

			if (isset($post["categories"])) {

				foreach ($post["categories"] as $key => $categories_id) {

					$c_data = [];

					$c_data['webpage_id'] = $id;
					$c_data['categories_id'] = $categories_id;
					$c_data['uuid'] = UUID::v5(UUID::v4(), 'webpage_categories');

					$this->model->insertTableData($c_data, "webpage_categories");
				}
			}
		}

		$customFieldNames = $this->request->getPost("customFieldName");
		$customFieldValues = $this->request->getPost("customFieldValue");
		$customFieldTypes = $this->request->getPost("customFieldType");
		$customFieldsUuids = $this->request->getPost("custom_fields_uuid");
		
		if (!empty($customFieldNames) && !empty($customFieldValues)) {
			$customData = [];
			foreach ($customFieldValues as $ckey => $customFieldValue) {
				$customData['field_name'] = $customFieldNames[$ckey];
				$customData['field_value'] = $customFieldValues[$ckey];
				$customData['field_type'] = $customFieldTypes[$ckey];
				
				if (!empty($customFieldsUuids) && isset($customFieldsUuids[$ckey])) {
					$customData['uuid'] = $customFieldsUuids[$ckey];
				} else {
					$customData['uuid'] = UUID::v5(UUID::v4(), 'custom_fields');
				}
				$isFieldExists = $this->model->getExistsTableRowsByUUID("custom_fields", $customData['uuid']);
				
				if (empty($isFieldExists) || !isset($isFieldExists) || !$isFieldExists) {
					$this->model->insertTableData($customData, "custom_fields");
				} else {
					$this->model->updateTableDataByUUID($customData['uuid'], $customData, "custom_fields");
				}
				if ($customData['uuid']) {
					$this->model->deleteTableData("content_list__custom_fields", $customData['uuid'], "custom_field_id");
				} 
				$relationData = [
					'custom_field_id' => $customData['uuid'],
					'content_list_id' => $uuid,
					'uuid' => UUID::v5(UUID::v4(), 'content_list__custom_fields')
				];
				$this->model->insertTableData($relationData, "content_list__custom_fields");
			}
		}

		$fromWhere = "";
		if (strlen($menuName ?? "") > 1) {
			$fromWhere = "?cat=$menuName";
		}
		if ($id > 0) {
			return redirect()->to('/' . $this->table . '/edit/' . $uuid . $fromWhere);
		}
		return redirect()->to('/' . $this->table . $fromWhere);
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

	public function rmimg($id, $rowId)
	{
		if (!empty($id)) {
			$this->model->deleteTableData("media_list", $id);
			session()->setFlashdata('message', 'Image deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}
		return redirect()->to('//' . $this->table . '/edit/' . $rowId);
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
}
