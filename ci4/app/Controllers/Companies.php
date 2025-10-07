<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Companies as ModelCompanies;
use App\Libraries\UUID;
use App\Models\Core\Common_model;

class Companies extends BaseController
{
    protected $companyModel;
    protected $table;
    protected $rawTblName;
    protected $commonModel;
    public function __construct()
    {
        parent::__construct();
        $this->companyModel = new ModelCompanies();
        $this->table = "companies";
        $this->rawTblName = "companies";
        $this->commonModel = new Common_model();
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
            $limit = (int)$this->request->getVar('limit');
            $offset = (int)$this->request->getVar('offset');
            $query = $this->request->getVar('query');
            $order = $this->request->getVar('order') ?? "company_name";
            $dir = $this->request->getVar('dir') ?? "asc";
        }

        $uuidBusineess = session('uuid_business');

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
        $data["categories"] = $this->commonModel->getCategories();
        $data["selectedCategories"] = $this->companyModel->selectedCategories($uuid);

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
        unset($postData['categories']);
        $id = $this->companyModel->insertOrUpdateByUUID($uuid, $postData);

        if ($id) {
            $contactID = $this->request->getPost('contactID');
            if ($contactID && isset($contactID)) {
                // $this->companyModel->deleteRelationData($postData['uuid']);
                $relationData = [
                    'company_uuid' => $postData['uuid'],
                    'contact_uuid' => $contactID,
                    'uuid' => UUID::v5(UUID::v4(), 'company__contact')
                ];
                $this->companyModel->insertRelationData($relationData);
            }

            $categories = $this->request->getPost('categories');
            if ($categories && !empty($categories)) {
                $this->companyModel->deleteCategoriesRelation($postData['uuid']);
                foreach ($categories as $category) {
                    $catData = [
                        'company_id' => $postData['uuid'],
                        'category_id' => $category,
                        'uuid' => UUID::v5(UUID::v4(), 'companies__categories'),
                        'uuid_business_id' => session('uuid_business')
                    ];
                    $this->companyModel->insertCategoryData($catData);
                }
            }

        }
        return redirect()->to($this->table);
    }

    public function removeContact ()
    {
        $contactUuid = $this->request->getPost("contactUuid");
        $removeContact = $this->companyModel->deleteRelationDataByContact($contactUuid);
        echo json_encode($removeContact);
    }

    public function clone($uuid = null)
    {
        $data = $this->companyModel->getRowsByUUID($uuid)->getRowArray();
        $uuidVal = UUID::v5(UUID::v4(), 'companies');
        unset($data['id'], $data['created_at'], $data['updated_at'], $data['is_email_sent']);
        $data['uuid'] = $uuidVal;

        $isCloned = $this->companyModel->insertOrUpdate(null, $data);

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
