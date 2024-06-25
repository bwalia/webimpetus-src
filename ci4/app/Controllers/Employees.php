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
            ->select('uuid, id, first_name, surname, email, mobile, allow_web_access')
            ->where(['uuid_business_id' => session('uuid_business')]);
        if ($query) {
            $sqlQuery = $sqlQuery
                ->like("first_name", $query);
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
