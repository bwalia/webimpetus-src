<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Blocks_model;
use App\Models\Template_model;

class Templates extends CommonController
{
    public $blocks_model;
    public $templateModel;
    function __construct()
    {
        parent::__construct();
        @$this->blocks_model = new Blocks_model();
        @$this->templateModel = new Template_model();
    }

    public function templateList()
	{
		$limit = (int)$this->request->getVar('limit');
		$offset = (int)$this->request->getVar('offset');
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "code";
		$dir = $this->request->getVar('dir') ?? "asc";

		$sqlQuery = $this->templateModel
            ->select("uuid, id, code, subject")
			->where(['uuid_business_id' => session('uuid_business')]);
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

    public function edit($id = '')
    {
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data["users"] = $this->model->getUser();
        $data["blocks_lists"] = $this->blocks_model->where(["uuid_business_id" => $this->businessUuid])->where('status', 1)->findAll();
        $data[$this->rawTblName] = $this->model->getRowsByUUID($id)->getRow();
        // if there any special cause we can overried this function and pass data to add or edit view
        $data['additional_data'] = $this->getAdditionalData($id);
        echo view($this->table . "/edit", $data);
    }

    public function getBlockListBySearch($search_code = "")
    {
        if (!empty($search_code)) {
            $data["blocks_lists"] = $this->blocks_model->where(["uuid_business_id" => $this->businessUuid])->like('code', $search_code)->where('status', 1)->findAll();
        } else {
            $data["blocks_lists"] = $this->blocks_model->where(["uuid_business_id" => $this->businessUuid])->where('status', 1)->findAll();
        }
        return json_encode($data, JSON_PRETTY_PRINT);
    }


    public function update()
    {
        $uuid = $this->request->getPost('uuid');

        $data = $this->request->getPost();
        $data['is_default'] = isset($data['is_default']) && $data['is_default'] == 'on' ? 1 : 0;

        if (empty($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'templates');
        }

        if ($data['is_default']) {
            $this->db->table($this->table)->update(array('is_default' => 0), array('module_name' => $data['module_name']));
        }

        $response = $this->model->insertOrUpdateByUUID($uuid, $data);

        if (!$response) {
            session()->setFlashdata('message', 'Something wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to('/' . $this->table);
    }

    public function clone($uuid = null)
    {
        $data = $this->templateModel->getRowsByUUID($uuid)->getRowArray();
        $uuidVal = UUID::v5(UUID::v4(), 'companies');
        unset($data['id'], $data['created_at'], $data['modified_at'], $data['comment'], $data['module_name']);
        $data['uuid'] = $uuidVal;

        $isCloned = $this->templateModel->saveData($data);

        if ($isCloned) {
            session()->setFlashdata('message', 'Data cloned Successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        } else {
            session()->setFlashdata('message', 'Something went wrong while clone the data. Please try again.');
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to($this->table . "/edit/" . $uuidVal);
    }
}
