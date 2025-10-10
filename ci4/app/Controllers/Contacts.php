<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController;
use App\Models\Companies;
use App\Models\CustomerContactModel;
use CodeIgniter\Database\BaseBuilder;
use App\Models\Users_model;
use App\Models\Core\Common_model;
use App\Libraries\UUID;
use App\Models\Contact;
use App\Models\Customers_model;
use CodeIgniter\API\ResponseTrait;

class Contacts extends CommonController
{	
    use ResponseTrait;
	protected $contactModel;
	protected $table;
	protected $rawTblName;
    protected $customers_model;
    protected $companyModel;
    protected $customerContactModel;

    function __construct()
    {
        parent::__construct();
        $this->contactModel = new Contact();
        $this->table = "contacts";
        $this->rawTblName = "contacts";
        $this->customers_model = new Customers_model();
        $this->companyModel = new Companies();
        $this->customerContactModel = new CustomerContactModel();
	}

    public function index()
	{
		$keyword = $this->request->getVar('query');
        $pager = \Config\Services::pager();
        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
			'is_add_permission' => 1,
            $this->table => $this->contactModel->where(["uuid_business_id" => $this->businessUuid])->search($keyword)->paginate(10),
            'pager'     => $this->contactModel->pager,
        ];

		echo view($this->table . "/list", $data);
	}

    public function contactsList()
    {
        $limit = (int)$this->request->getVar('limit');
        $offset = (int)$this->request->getVar('offset');
        $query = $this->request->getVar('query');
        $order = $this->request->getVar('order') ?? "first_name";
        $dir = $this->request->getVar('dir') ?? "asc";

        $sqlQuery = $this->contactModel
            ->select("uuid, id, first_name, surname, email, mobile, direct_phone, allow_web_access, news_letter_status, created_at")
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
    
    public function getAdditionalData($id)
    {
        $model = new Common_model();
        $data["customers"] = $model->getAllDataFromTable("customers");
        
        return  $data;

    }

    public function edit($uuid = 0)
    {

        $contactData = $this->model->getRowsByUUID($uuid)->getRow();
        $customers = (new Customers_model());
        if(!empty($contactData) && isset($contactData->client_id)) {
            $customers = $customers->orWhere("id", $contactData->client_id);
        } else {
            $customers = $customers->orWhere("id", 0);
        }
        $customers = $customers->get()->getResultArray();

        $companies = (new Companies());
        if(!empty($contactData) && isset($contactData->client_id)) {
            $companies = $companies->orWhere("id", $contactData->client_id);
        } else {
            $companies = $companies->orWhere("id", 0);
        }
        $companies = $companies->get()->getResultArray();

        if (isset($contactData->linked_module_types) && $contactData->linked_module_types == "companies") {
            $contactData->company_id = $contactData->client_id;
        }
        if (isset($contactData->linked_module_types) && $contactData->linked_module_types == "customers") {
            $contactData->customer_id = $contactData->client_id;
        }

		$data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
		$data["users"] = $this->model->getUser();
        $data["categories"] = $this->model->getCategories();
		$data["contact"] = $uuid ? $contactData : "";
        $data["customers"] = $customers;
        $data["companies"] = $companies;
        
		// if there any special cause we can overried this function and pass data to add or edit view
		$data['additional_data'] = $this->getAdditionalData($uuid);

        echo view($this->table."/edit",$data);
    }

    public function contactsCustomerAjax()
    {
        $q = $this->request->getVar('q');
        $data = $this->customers_model->where('uuid_business_id', session('uuid_business'));
        if(!empty($q)) {
            $data = $data->like('company_name', $q);
        }
        $data = $data->limit(500)->get()->getResult();
        return $this->respond($data);
    }
    public function contactsCompanyAjax()
    {
        $q = $this->request->getVar('q');
        $data = $this->companyModel->where('uuid_business_id', session('uuid_business'));
        if(!empty($q)) {
            $data = $data->like('company_name', $q);
        }
        $data = $data->limit(500)->get()->getResult();
        return $this->respond($data);
    }

    public function isValidUUID($uuid) {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        return preg_match($pattern, $uuid) === 1;
    }
    public function update()
    {
        $uuid = $this->request->getPost('uuid');

		$data = $this->request->getPost();
       
        if(!isset($data['allow_web_access'])){
            $data['allow_web_access'] = 0;
        }
        if(empty($uuid)){
            $data['uuid'] = UUID::v5(UUID::v4(), 'contacts_saving');
            $data['uuid_business_id'] = $this->session->get('uuid_business');
        }
        if(strlen($data['password']) > 0){
            $data['password'] = md5($data['password']);
        }
        if ($data['linked_module_types'] == "customers") {
            $data['client_id'] = $data['customer_id'];
        }
        if ($data['linked_module_types'] == "companies") {
            $data['client_id'] = $data['company_id'];
        }

        unset($data['companyUUID']);
        unset($data['customerUUID']);
        unset($data['customer_id']);
        unset($data['company_id']);
		$response = $this->model->insertOrUpdateByUUID($uuid, $data);

        if ($response) {
            $companyUUID = $this->request->getPost('companyUUID');
            if (($companyUUID && isset($companyUUID) && $companyUUID != "") || $data['linked_module_types'] == "companies") {
                if (!isset($companyUUID) || !$companyUUID || $companyUUID == "") {
                    $companyUUID = $this->request->getPost('company_id');
                    if (!$this->isValidUUID($companyUUID)) {
                        $companyData = $this->companyModel->select('uuid')->where('id', $companyUUID)->first();
                        $companyUUID = $companyData['uuid'];
                    }
                }
                $this->companyModel->deleteRelationDataByContactCompany($data['uuid'], $companyUUID);
                $this->customerContactModel->deleteDataByContact($data['uuid']);
                $relationData = [
                    'company_uuid' => $companyUUID,
                    'contact_uuid' => $data['uuid'],
                    'uuid' => UUID::v5(UUID::v4(), 'company__contact')
                ];
                $this->companyModel->insertRelationData($relationData);
            }
            $customerUUID = $this->request->getPost('customerUUID');
            if (($customerUUID && isset($customerUUID) && $customerUUID != "") || $data['linked_module_types'] == "customers") {
                $customerUUID = $this->request->getPost('customer_id');
                if (!$this->isValidUUID($customerUUID)) {
                    $customerData = $this->customers_model->select('uuid')->where('id', $customerUUID)->first();
                    $customerUUID = $customerData['uuid'];
                }
                $this->customerContactModel->deleteDataByContactCustomer($data['uuid'], $customerUUID);
                $this->companyModel->deleteRelationDataByContact($data['uuid']);
                $cusConData = [
                    'customer_uuid' => $customerUUID,
                    'contact_uuid' => $data['uuid'],
                    'uuid' => UUID::v5(UUID::v4(), 'customer__contact')
                ];
                $this->customerContactModel->saveData($cusConData);
            }
        }
   
		if(!$response){
			session()->setFlashdata('message', 'Something wrong!');
			session()->setFlashdata('alert-class', 'alert-danger');	
		}

        return redirect()->to('/'.$this->table);
    }

    public function addAddress(){

        $m_data['id'] = $this->request->getPost("contactId");
        $m_data['addressId'] = 0;

        $data['html'] = view($this->table."/addAddress", $m_data);

        echo json_encode($data);
    }
    public function editAddress(){

        $data['id'] = $this->request->getPost("contactId");
        $data['addressId'] = $this->request->getPost("addressId");
        $data['data'] = $this->model->getRow("addresses", $data['addressId']);

        $data['html'] = view($this->table."/addAddress", $data);

        echo json_encode($data);
    }
    public function deleteAddress(){

        $data['addressId'] = $this->request->getPost("addressId");
        $data['data'] = $this->model->deleteTableData("addresses", $data['addressId']);


        echo json_encode($data);
    }
    public function renderAddress(){

        $data['uuid'] = $this->request->getPost("uuid");
        $data['data'] = $this->model->getDataWhere("addresses", $data['uuid'], "uuid_contact");

        $data['html'] = view($this->table."/addressList", $data);

        echo json_encode($data);
    }
    public function saveAddress(){

        $post = $this->request->getPost();
        $addressId = $post['addressId'];
        unset($post['addressId']);
        

        if( $addressId > 0){
            unset($post['uuid_contact']);
            $this->model->updateTableData( $addressId, $post, "addresses");
        }else{
           
            $this->model->insertTableData($post, "addresses");
        }

        $response['status'] = "success";
     
        echo json_encode($response);
    }
    public function checkEmail() {
        $email = $this->request->getPost("email");
        
        $checkEmail = $this->model->getSingleRowWhere("contacts", $email, "email");
        
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

    public function clone($uuid = null)
    {
        $data = $this->contactModel->getRowsByUUID($uuid)->getRowArray();
        $uuidVal = UUID::v5(UUID::v4(), 'companies');
        unset($data['id'], $data['client_id'], $data['email'], $data['password'], $data['created_at']);
        $data['uuid'] = $uuidVal;

        $isCloned = $this->contactModel->insertOrUpdate(null, $data);

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