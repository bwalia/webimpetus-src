<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Core\Common_model;
use App\Models\Contact;
use App\Libraries\UUID;
use App\Models\CustomerContactModel;
use App\Models\Customers_model;

class Customers extends CommonController
{
    protected $contactModel;
    protected $customerContactModel;
    protected $customerModel;
    protected $table;
    protected $rawTblName;
    function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->contactModel = new Contact();
        $this->customerModel = new Customers_model();
        $this->customerContactModel = new CustomerContactModel();
        $this->table = "customers";
        $this->rawTblName = "customers";
    }

    public function getAdditionalData($id)
    {
        //die(session('uuid_business'));
        $model = new Common_model();
        $builder = $this->db->table("contacts");
        $builder->select("id as contact_id,first_name,surname,email as contact_email");
        $builder->where("client_id", $id);
        $builder->where("uuid_business_id", session('uuid_business'));
        $data["contacts"]  = $builder->get()->getResultArray();
        //echo $this->db->getLastQuery();
        //print_r($data["contacts"]);die;
        return  $data;
    }

    public function index()
    {
        $keyword = $this->request->getVar('query');
        $pager = \Config\Services::pager();
        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
            'customers' => $this->customerModel->where('uuid_business_id', session('uuid_business'))->search($keyword)->paginate(2),
            'pager'     => $this->customerModel->pager,
        ];

        return view($this->table . '/list', $data);
    }

    public function customersList()
    {
        $limit = (int)$this->request->getVar('limit');
        $offset = (int)$this->request->getVar('offset');
        $query = $this->request->getVar('query');
        $order = $this->request->getVar('order') ?? "company_name";
        $dir = $this->request->getVar('dir') ?? "asc";

        $sqlQuery = $this->customerModel
            ->select("uuid, id, company_name, acc_no, status, email, phone, city, supplier, created_at")
            ->where(['uuid_business_id' => session('uuid_business')]);
        if ($query) {
            $sqlQuery = $sqlQuery->like("company_name", $query);
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

    public function summary()
    {
        $businessUuid = session('uuid_business');

        // Get total count
        $totalCount = $this->customerModel
            ->where(['uuid_business_id' => $businessUuid])
            ->countAllResults();

        // Get active customers count
        $activeCount = $this->customerModel
            ->where(['uuid_business_id' => $businessUuid, 'status' => 1])
            ->countAllResults();

        // Get suppliers count
        $suppliersCount = $this->customerModel
            ->where(['uuid_business_id' => $businessUuid, 'supplier' => 1])
            ->countAllResults();

        // Get new this month count
        $monthStart = date('Y-m-01 00:00:00');
        $newThisMonth = $this->customerModel
            ->where(['uuid_business_id' => $businessUuid])
            ->where('created_at >=', $monthStart)
            ->countAllResults();

        $data = [
            'total' => $totalCount,
            'active' => $activeCount,
            'suppliers' => $suppliersCount,
            'newThisMonth' => $newThisMonth
        ];

        return $this->response->setJSON($data);
    }

    public function edit($uuid = 0)
	{
		$tableData =  $uuid ? $this->model->getExistsRowsByUUID($uuid)->getRow() : '';
        
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data["users"] = $this->model->getUser();
		$data["customer"] = $tableData;
        $data['contacts'] = $this->customerContactModel->getContacts($uuid);
		echo view($this->table . "/edit", $data);
	}

    public function update()
    {
        $post = $this->request->getPost();
        $data["company_name"] = @$post["company_name"];
        $data["acc_no"] = @$post["acc_no"];
        $data["status"] = @$post["status"];
        $data["contact_firstname"] = @$post["contact_firstname"];
        $data["contact_lastname"] = @$post["contact_lastname"];
        $data["email"] = @$post["email"];
        $data["address1"] = @$post["address1"];
        $data["address2"] = @$post["address2"];
        $data["city"] = @$post["city"];
        $data["country"] = @$post["country"];
        $data["postal_code"] = @$post["postal_code"];
        $data["phone"] = @$post["phone"];
        $data["notes"] = $post["notes"];
        $data["supplier"] = @$post["supplier"];
        $data["website"] = @$post["website"];
        $data["categories"] = json_encode(@$post["categories"]);
        $data["uuid_business_id"] = session('uuid_business');

        $data["uuid"] = $uuid = $post["uuid"];
        if (empty($data["uuid"])) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'customers');
        }
        $response = $this->model->insertOrUpdateByUUID($uuid, $data);
        if (!$response) {
            session()->setFlashdata('message', 'Something wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        } else {
            $this->customerContactModel->deleteDataByCustomer($uuid);
            if (!empty($post["cnId"]) && $post["cnId"] && isset($post["cnId"])) {
                foreach ($post["cnId"] as $contactId) {
                    $cscnData = [
                        'uuid' => UUID::v5(UUID::v4(), 'customer__contact'),
                        'customer_uuid' => $uuid,
                        'contact_uuid' => $contactId
                    ];
                    $this->customerContactModel->saveData($cscnData);
                }
            }

            $this->model->deleteTableData("customer_categories", $uuid, "customer_id");

            if (isset($post["categories"])) {
                foreach ($post["categories"] as $key => $categories_id) {

                    $c_data = [];

                    $c_data['customer_id'] = $post['id'];
                    $c_data['categories_id'] = $categories_id;
                    $c_data['uuid'] = UUID::v5(UUID::v4(), 'customer_categories');

                    $this->model->insertTableData($c_data, "customer_categories");
                }
            }
        }

        return redirect()->to('/' . $this->table);
    }


    public function insertOrUpdate($table, $id = null, $data = null)
    {
        unset($data["id"]);

        if (@$id > 0) {

            $builder = $this->db->table($table);
            $builder->where('id', $id);
            $result = $builder->update($data);

            if ($result) {
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
                return $id;
            }
        } else {
            $query = $this->db->table($table)->insert($data);
            if ($query) {
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
                return $this->db->insertID();
            }
        }

        return false;
    }


    public function deleteCustomer()
    {
        $customerId = $this->request->getPost("customerId");
        $res = $this->model->deleteTableData("contacts", $customerId);
        return $res;
    }

    public function checkEmail() {
        $email = $this->request->getPost("email");
        
        $checkEmail = $this->model->getSingleRowWhere("customers", $email, "email");
        
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

    public function removeContact ()
    {
        $contactUuid = $this->request->getPost("contactUuid");
        $removeContact = $this->customerContactModel->deleteDataByContact($contactUuid);
        echo json_encode($removeContact);
    }

    public function clone($uuid = null)
    {
        $data = $this->customerModel->getBusinessRows($uuid)->getRowArray();
        $uuidVal = UUID::v5(UUID::v4(), 'customers');
        unset($data['id'], $data['client_id'], $data['email'], $data['created_at']);
        $data['uuid'] = $uuidVal;

        $isCloned = $this->customerModel->insertOrUpdate(null, $data);

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
