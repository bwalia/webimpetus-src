<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Meta_model;
use App\Models\User_business_model;
use App\Models\Users_model;

class User_business extends CommonController
{
    protected $timeSlipsModel;
    public $meta_model;
    public $User_business_model;
    public $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->meta_model = new Meta_model();
        $this->User_business_model = new User_business_model();
		$this->userModel = new Users_model();
    }

    public function index()
    {   
        $data['columns'] = $this->db->getFieldNames($this->table);
        $data['fields'] = array_diff($data['columns'], $this->notAllowedFields);
        $data[$this->table] = $this->model->getRows();
        $allBusiness = $this->meta_model->getAllBusiness();

        $businessNameArr = [];
        foreach ($allBusiness as $singleBusiness) {
            $businessNameArr[$singleBusiness->uuid] = $singleBusiness->name;
        }

        $allUsers = $this->meta_model->getAllUsers();
        $userNameArray = [];
        foreach ($allUsers as $singleUser) {
            $userNameArray[$singleUser->uuid] = $singleUser->name;
        }

        $data['userNameArray'] = $userNameArray;
        $data['businessNameArr'] = $businessNameArr;
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;
        $data['identifierKey'] = 'uuid';

        $viewPath = "user_business/list";

        $viewPath = $this->table . "/list";

        return view($viewPath, $data);
    }

    public function userBusinessList()
	{
		$limit = (int)$this->request->getVar('limit');
		$offset = (int)$this->request->getVar('offset');
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "name";
		$dir = $this->request->getVar('dir') ?? "asc";

		$sqlQuery = $this->User_business_model
            ->select("user_business.*")
            ->join('users', 'user_business.user_uuid = users.uuid', 'LEFT')
            ->select("users.name as username")
            ->join('businesses', 'user_business.primary_business_uuid = businesses.uuid', 'LEFT')
            ->select("businesses.name as business_name");
			// ->where(["businesses.uuid_business_id" => $this->businessUuid]);
		if ($query) {
			$sqlQuery = $sqlQuery->like("businesses.name", $query);
		}

        $countQuery = $sqlQuery->countAllResults(false);
        $sqlQuery = $sqlQuery->limit($limit, $offset)->orderBy("businesses.".$order, "businesses.".$dir);

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
        $userBsResults = $this->User_business_model->getResultByUUID((string) $id);
        
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['result'] = $userBsResults;
        $data['selectedUser'] = $this->userModel->userByUUID($userBsResults ? $userBsResults[0]->user_uuid : false);
        $data['allUsers'] = $this->meta_model->getAllUsers();
        $data['userBusiness'] = $this->meta_model->getAllBusiness();
        return view('user_business/edit', $data);
    }


    public function update()
    {
        $id = $this->request->getPost('id');
        $selectedUserId = $this->request->getPost('selectedUserId');
        $user_id = $this->request->getPost('user_id') ?? $selectedUserId;
        $user_business_id = $this->request->getPost('user_business_id');
        $data["user_id"] = $user_id;
        $data["uuid"] = $id;
        $data["user_business_id"] = json_encode($user_business_id);
        $primary_business_uuid = $this->request->getPost('primary_business_uuid');
        $data["primary_business_uuid"] = '';
        if (!is_null($user_business_id) && in_array($primary_business_uuid, $user_business_id)) {
            $data["primary_business_uuid"] = $primary_business_uuid;
        }
        if (empty($data['uuid'])) {
            $uuidNamespace = UUID::v4();
            $uuid = UUID::v5($uuidNamespace, 'user_business');
            $data['uuid'] = $uuid;
        }        
        $data['business'] = $this->User_business_model->insertOrUpdate($id, $data);
        return redirect()->to('/user_business');
    }

    public function savepwd()
    {
        if (!empty($this->request->getPost('id')) && !empty($this->request->getPost('npassword')) && $this->request->getPost('npassword') == $this->request->getPost('cpassword')) {
            $data = array(
                'password' => md5($this->request->getPost('npassword') ?? "")
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