<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Meta_model;
use App\Models\User_business_model;

class User_business extends CommonController
{
    protected $timeSlipsModel;

    public function __construct()
    {
        parent::__construct();
        $this->meta_model = new Meta_model();
        $this->User_business_model = new User_business_model();
    }

    public function index()
    {

        $data['columns'] = $this->db->getFieldNames($this->table);
        $data['fields'] = array_diff($data['columns'], $this->notAllowedFields);
        $data[$this->table] = $this->model->getRows();
        $allBusiness = $this->meta_model->getAllBusiness();

        $businessNameArr = [];
        foreach ($allBusiness as $singleBusiness) {
            $businessNameArr[$singleBusiness->uuid] =  $singleBusiness->name;
        }

        $allUsers = $this->meta_model->getAllUsers();
        $userNameArray = [];
        foreach ($allUsers as $singleUser) {
            $userNameArray[$singleUser->id] =  $singleUser->name;
        }

        $data['userNameArray'] = $userNameArray;
        $data['businessNameArr'] = $businessNameArr;
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;
        $data['identifierKey'] = 'id';

        $viewPath = "user_business/list";

        $viewPath = $this->table . "/list";

        return view($viewPath, $data);
    }

    public function edit($id = 0)
    {
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['result'] = $this->User_business_model->getResult($id);

        $data['allUsers'] = $this->meta_model->getAllUsers();
        $data['userBusiness'] = $this->meta_model->getAllBusiness();
        return view('user_business/edit', $data);
    }


    public function update()
    {
        $id = $this->request->getPost('id');
        $user_id = $this->request->getPost('user_id');
        $user_business_id = $this->request->getPost('user_business_id');
        $data["user_id"] = $user_id;
        $data["id"] = $id;
        $data["user_business_id"] = json_encode($user_business_id);
        $primary_business_uuid = $this->request->getPost('primary_business_uuid');
        $data["primary_business_uuid"] = '';
        if (!is_null($user_business_id) && in_array($primary_business_uuid, $user_business_id)) {
            $data["primary_business_uuid"] = $primary_business_uuid;
        }

        $data['business'] = $this->User_business_model->insertOrUpdate($id, $data);
        return redirect()->to('/user_business');
    }

    public function savepwd()
    {
        if (!empty($this->request->getPost('id')) && !empty($this->request->getPost('npassword')) && $this->request->getPost('npassword') == $this->request->getPost('cpassword')) {
            $data = array(
                'password' => md5($this->request->getPost('npassword'))
            );
            $this->userModel->updateUser($data, $this->request->getPost('id'));
            session()->setFlashdata('message', 'Password changed Successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        }
        return redirect()->to('/users');
    }

    public function status()
    {
        if (!empty($id = $this->request->getPost('id'))) {
            $data = array(
                'status' => $this->request->getPost('status')
            );
            $this->userModel->updateUser($data, $id);
        }
        echo '1';
    }
}
