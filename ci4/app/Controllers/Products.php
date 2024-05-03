<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use CodeIgniter\Controller;
use App\Models\Product;
use App\Models\Content_model;
use App\Libraries\UUID;
use App\Controllers\Core\CommonController;
use stdClass;

ini_set('display_errors', 1);

class Products extends CommonController
{
	public $productModel;
	public $content_model;
	public function __construct()
	{
		parent::__construct();
		@$this->productModel = new Product();
		@$this->content_model = new Content_model();
	}
	public function index()
	{
		$data['productsList'] = $this->productModel->where(["uuid_business_id" => $this->businessUuid])->findAll();
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['is_add_permission'] = 1;
		echo view($this->table . "/list", $data);
	}

	public function productsList()
	{
		$limit = $this->request->getVar('limit');
		$offset = $this->request->getVar('offset');
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "product_name";
		$dir = $this->request->getVar('dir') ?? "asc";

		$sqlQuery = $this->productModel
			->where(['uuid_business_id' => session('uuid_business')])
			->limit($limit, $offset)
			->orderBy($order, $dir)
			->get()
			->getResultArray();
		if ($query) {
			$sqlQuery = $this->productModel
				->where(['uuid_business_id' => session('uuid_business')])
				->like("product_name", $query)
				->limit($limit, $offset)
				->orderBy($order, $dir)
				->get()
				->getResultArray();
		}

		$countQuery = $this->productModel
			->where(["uuid_business_id" => session("uuid_business")])
			->countAllResults();
		if ($query) {
			$countQuery = $this->productModel
				->where(["uuid_business_id" => session("uuid_business")])
				->like("product_name", $query)
				->countAllResults();
		}

		$data = [
			'rawTblName' => $this->rawTblName,
			'tableName' => $this->table,
			'data' => $sqlQuery,
			'recordsTotal' => $countQuery,
		];
		return $this->response->setJSON($data);
	}

	public function edit($id = 0)
	{
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;

		$data['categoryList'] = $this->model->getDataWhere("categories", session('uuid_business'), "uuid_business_id");
		$data['images'] = [];
		$data['specifications'] = [];

		if (!empty($id)) {
			$data["product"] = $this->productModel->getProduct($id);
			$data['images'] = $this->model->getDataWhere("media_list", $id, "uuid_linked_table");
			if ($data["product"]) {
				$data["specifications"] = $this->productModel->getKeyValueData($data["product"]->uuid);
			}
		} else {
			$data["product"] = (object) ["uuid" => UUID::v5(UUID::v4(), 'products')];
		}

		echo view($this->table . "/edit", $data);
	}

	public function update()
	{
		// Preparing data for products -------------- >>
		$product = [
			'uuid'  => $this->request->getPost('uuid'),
			'name' => $this->request->getPost('name'),
			'code' => $this->request->getPost('code'),
			'description' => $this->request->getPost('description'),
			'sku' => $this->request->getPost('sku'),
			'is_published' => ($this->request->getPost('is_published') !== null) ? 1 : 0,
			'stock_available' => $this->request->getPost('stock_available'),
			'unit_price' => $this->request->getPost('unit_price'),
			'sort_order' => $this->request->getPost('sort_order'),
			'uuid_business_id' => session('uuid_business')
		];
		// Preparing data for products -------------- ||


		// Preparing data for products -------------- >>
		$category = array(
			'uuid_product'  => $this->request->getPost('uuid'),
			'uuid_category' => $this->request->getPost('category')
		);
		// Preparing data for products -------------- ||


		// Preparing data for key_values -------------- >>

		$keyValuesData = [];
		$specCount = $this->request->getPost('czContainer_czMore_specCount');
		if (is_numeric($specCount) && $specCount > 0) {
			for ($i = 0; $i < $specCount; $i++) {
				$keyValuesData[] = [
					"uuid_product" => $this->request->getPost('uuid'),
					"key_name" => $this->request->getPost('spec_' . ($i + 1) . '_name'),
					"key_value" => $this->request->getPost('spec_' . ($i + 1) . '_value'),
				];
			}
		}
		// Preparing data for key_values -------------- ||

		$this->db->transBegin();
		$id = $this->request->getPost('id');

		$condition = null;
		if ($id > 0) {
			$condition = array('id' => $id, 'uuid_business_id' => session('uuid_business'));
		}

		$is_success = $this->productModel->insertOrUpdate(null, $product, $condition);

		if ($is_success) {
			$this->productModel->deleteTableData("product_categories", array("uuid_product" => $product["uuid"]));

			if (sizeof($category) > 0) {
				$this->productModel->saveCategoryData($category);
			}

			$this->productModel->deleteTableData("key_values", array("uuid_product" => $product["uuid"]));
			if (sizeof($keyValuesData) > 0) {
				$this->productModel->saveKeyValueData($keyValuesData);
			}
		}


		$files = $this->request->getPost("file");

		if (is_array($files)) {
			foreach ($files as $key => $filePath) {
				$blog_images = [];
				$blog_images['uuid_business_id'] =  session('uuid_business');
				$blog_images['name'] = $filePath;
				$blog_images['uuid_linked_table'] = $product["uuid"];

				$this->content_model->saveDataInTable($blog_images, "media_list");
			}
		}

		$this->db->transComplete();

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
		if ($this->productModel->deleteProduct($id)) {
			session()->setFlashdata('message', 'Product Deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
			return redirect()->to('/' . $this->table);
		}
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
