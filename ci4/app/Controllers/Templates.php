<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Blocks_model;

class Templates extends CommonController
{

    function __construct()
    {
        parent::__construct();
        $this->blocks_model = new Blocks_model();
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
}
