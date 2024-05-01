<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Companies as ModelCompanies;
use App\Libraries\UUID;

class Companies extends BaseController
{
    protected $companyModel;
    protected $table;
    protected $rawTblName;
    public function __construct()
    {
        parent::__construct();
        $this->companyModel = new ModelCompanies();
        $this->table = "companies";
        $this->rawTblName = "companies";
    }
    public function index()
    {
        $pager = \Config\Services::pager();
        // $data['tableName'] = $this->table;
        // $data['rawTblName'] = $this->rawTblName;
        // $data['companies'] = $this->companyModel->getRowsByUUID();
        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
            'companies' => $this->companyModel->where('uuid_business_id', session('uuid_business'))->paginate(10), // Adjust the number as needed
            'pager'     => $this->companyModel->pager,
        ];

        return view($this->table . '/list', $data);
    }

    public function companiesList()
    {
        if ($this->request) {
            $limit = $this->request->getVar('limit');
            $offset = $this->request->getVar('offset');
            $query = $this->request->getVar('query');
            $order = $this->request->getVar('order') ?? "company_name";
            $dir = $this->request->getVar('dir') ?? "asc";
        } else {
            $limit = $_GET['limit'] ?? 20;
            $offset = $_GET['offset'] ?? 0;
            $query = $_GET['query'] ?? false;
            $order = $_GET['order'] ?? "company_name";
            $dir = $_GET['dir'] ?? "asc";
        }

        $uuidBusineess = session('uuid_business') ?? $_GET['uuid_business_id'];

        $sqlQuery = $this->companyModel
                    ->where(['uuid_business_id' => $uuidBusineess])
                    ->limit($limit, $offset)
                    ->orderBy($order, $dir)
                    ->get()
                    ->getResultArray();
        if ($query) {
            $sqlQuery = $this->companyModel
                        ->where(['uuid_business_id' => $uuidBusineess])
                        ->like("company_name", $query)
                        ->limit($limit, $offset)
                        ->orderBy($order, $dir)
                        ->get()
                        ->getResultArray();
        }

        $countQuery = $this->companyModel
                        ->where(["uuid_business_id"=> $uuidBusineess])
                        ->countAllResults();
        if ($query) {
            $countQuery = $this->companyModel
                            ->where(["uuid_business_id"=> $uuidBusineess])
                            ->like("company_name", $query)
                            ->countAllResults();
        }
        
        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
            'data' => $sqlQuery,
            'recordsTotal' => $countQuery,
        ];
        if ($this->response) {
            return $this->response->setJSON($data);
        } else {
            return $data;
        }
    }

    public function edit($uuid = 0)
	{   
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['company'] = $uuid ? $this->companyModel->getRowsByUUID($uuid)->getRow() : [];
		$data['contacts'] = $this->companyModel->getContacts($uuid);

		return view($this->table . '/edit', $data);
	}

    public function update()
	{   
        if ($this->request) {
            $uuid = $this->request->getPost('uuid');
            $postData = $this->request->getPost();
        } else {
            $uuid = $_POST['uuid'] ?? false;
            $postData = $_POST;
        }
        if (!$uuid || empty($uuid) || !isset($uuid)) {
            $postData['uuid'] = UUID::v5(UUID::v4(), 'roles');
        }
        if ($this->request) {
            $postData['uuid_business_id'] = session('uuid_business');
        } else {
            $postData['uuid_business_id'] = $_POST['uuid_business_id'];
        }
        
        unset($postData['contactID']);
        $id = $this->companyModel->insertOrUpdateByUUID($uuid, $postData);

        if ($id) {
            $this->companyModel->deleteRelationData($postData['uuid']);
            if ($this->request) {
                $contactID = $this->request->getPost('contactID');
            } else {
                $contactID = $_POST['contactID'];
            }
            $relationData = [
                'company_uuid' => $postData['uuid'],
                'contact_uuid' => $contactID,
                'uuid' => UUID::v5(UUID::v4(), 'company__contact')
            ];
            $this->companyModel->insertRelationData($relationData);
        }
        if ($this->request) {
            return redirect()->to($this->table);
        } else {
            return $postData;
        }
    }
}
