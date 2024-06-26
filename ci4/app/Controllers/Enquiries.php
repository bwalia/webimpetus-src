<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use CodeIgniter\Controller;
use App\Models\Enquiries_model;
use App\Models\Users_model;
use App\Models\Cat_model;
use App\Models\Content_model;
use App\Controllers\Core\CommonController;


class Enquiries extends CommonController
{
	protected $whereCond = array();
	protected $content_model;
	protected $enquries_model;
	protected $user_model;
	protected $cat_model;
	function __construct()
	{
		parent::__construct();
		// $this->model = new Content_model();
		$this->content_model = new Content_model();
		$this->enquries_model = new Enquiries_model();
		$this->user_model = new Users_model();
		$this->cat_model = new Cat_model();
		$this->whereCond['uuid_business_id'] = $this->businessUuid;
	}
	public function index()
	{
		$builder = $this->enquries_model;
		if (!empty($this->request->getGet('filter'))) {
			$builder->like('name', $this->request->getGet('filter'));
			$builder->orLike('email', $this->request->getGet('filter'));
			$builder->orLike('message', $this->request->getGet('filter'));
		}
		$data[$this->table] = $builder->orderBy('id', 'desc')->paginate(10);
		//total rows
		$total_query = $this->enquries_model->asArray();
		if (!empty($this->request->getGet('filter'))) {
			$total_query->like('name', $this->request->getGet('filter'));
			$total_query->orLike('email', $this->request->getGet('filter'));
			$total_query->orLike('message', $this->request->getGet('filter'));
		}
		$data['total'] = $total_query->countAllResults();
		//$data['results'] = $this->enquries_model->orderBy('id','desc')->paginate(10);
		//$data['pager'] = $this->enquries_model->pager;
		//print_r($data['pager']);die;
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['is_add_permission'] = 1;

		return view($this->table . "/list", $data);
	}

	public function enquiriesList()
	{
		$limit = $this->request->getVar('limit');
		$offset = $this->request->getVar('offset');
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "name";
		$dir = $this->request->getVar('dir') ?? "asc";

		$sqlQuery = $this->enquries_model
			->select("uuid, id, name, email, phone, created")
			->where(['uuid_business_id' => session('uuid_business')]);
		if ($query) {
			$sqlQuery = $sqlQuery->like("name", $query);
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

	public function loadData($record = 0)
	{
		$builder = $this->enquries_model;
		if (!empty($this->request->getGet('filter'))) {
			$builder->like('name', $this->request->getGet('filter'));
			$builder->orLike('email', $this->request->getGet('filter'));
			$builder->orLike('message', $this->request->getGet('filter'));
		}
		$data['results'] = $builder->orderBy('id', 'desc')->paginate(10);
		echo json_encode($data);
		die;
	}



	public function edit($uuid = 0)
	{
		$enquiriesData = $uuid ? $this->enquries_model->getRowsByUUID($uuid)->getRow() : "";
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['enquiries'] = $enquiriesData;
		$data['users'] = $this->user_model->getUser();
		$data['cats'] = $this->cat_model->getRows();
		$array1 = $this->cat_model->getCatIds($enquiriesData ? $enquiriesData->id : $uuid);

		$arr = array_map(function ($value) {
			return $value['categoryid'];
		}, $array1);
		$data['selected_cats'] = $arr;

		return view($this->table . "/edit", $data);
	}
}
