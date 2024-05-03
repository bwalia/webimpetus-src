<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Users_model;
use App\Models\Core\Common_model;
use App\Libraries\UUID;

class Employees extends CommonController
{

    function __construct()
    {
        parent::__construct();
    }

    public function getAdditionalData($id)
    {
        $model = new Common_model();
        $data["customers"] = $model->getAllDataFromTable("customers");
        return  $data;
    }

    public function employeesList()
    {
        $limit = $this->request->getVar('limit');
        $offset = $this->request->getVar('offset');
        $query = $this->request->getVar('query');
        $order = $this->request->getVar('order') ?? "first_name";
        $dir = $this->request->getVar('dir') ?? "asc";

        $model = new Common_model();

        $sqlQuery = $model->builder("employees")
            ->where(['uuid_business_id' => session('uuid_business')])
            ->limit($limit, $offset)
            ->orderBy($order, $dir)
            ->get()
            ->getResultArray();
        if ($query) {
            $sqlQuery = $model->builder("employees")
                ->where(['uuid_business_id' => session('uuid_business')])
                ->like("first_name", $query)
                ->limit($limit, $offset)
                ->orderBy($order, $dir)
                ->get()
                ->getResultArray();
        }

        $countQuery = $model->builder("employees")
            ->where(["uuid_business_id" => session("uuid_business")])
            ->countAllResults();
        if ($query) {
            $countQuery = $model->builder("employees")
                ->where(["uuid_business_id" => session("uuid_business")])
                ->like("first_name", $query)
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

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $data = $this->request->getPost();

        if (empty($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'employees');
        }

        if (strlen($data['password']) > 0) {
            $data['password'] = md5($data['password']);
        }
        $data['businesses'] = json_encode(@$data['businesses']);
        $response = $this->model->insertOrUpdateByUUID($uuid, $data);
        if (!$response) {
            session()->setFlashdata('message', 'Something wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to('/' . $this->table);
    }

    public function checkEmail()
    {
        $email = $this->request->getPost("email");

        $checkEmail = $this->model->getSingleRowWhere("employees", $email, "email");

        if (isset($checkEmail) || $checkEmail || !empty($checkEmail)) {
            echo json_encode([
                "status" => 409,
                "message" => "Email already exists."
            ]);
        } else {
            echo json_encode([
                "status" => 200,
                "message" => "Email is unique."
            ]);
        }
    }
}
