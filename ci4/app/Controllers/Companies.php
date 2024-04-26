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
		$uuid = $this->request->getPost('uuid');
        $postData = $this->request->getPost();
        if (!$uuid || empty($uuid) || !isset($uuid)) {
            $postData['uuid'] = UUID::v5(UUID::v4(), 'roles');
        }
        $postData['uuid_business_id'] = session('uuid_business');
        
        unset($postData['contactID']);
        $id = $this->companyModel->insertOrUpdateByUUID($uuid, $postData);

        if ($id) {
            $this->companyModel->deleteRelationData($postData['uuid']);
            $relationData = [
                'company_uuid' => $postData['uuid'],
                'contact_uuid' => $this->request->getPost('contactID'),
                'uuid' => UUID::v5(UUID::v4(), 'company__contact')
            ];
            $this->companyModel->insertRelationData($relationData);
        }
        return redirect()->to($this->table);
    }
}
