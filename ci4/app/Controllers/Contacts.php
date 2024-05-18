<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController;
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

    function __construct()
    {
        parent::__construct();
        $this->contactModel = new Contact();
        $this->table = "contacts";
        $this->rawTblName = "contacts";
        $this->customers_model = new Customers_model();
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
        $limit = $this->request->getVar('limit');
        $offset = $this->request->getVar('offset');
        $query = $this->request->getVar('query');
        $order = $this->request->getVar('order') ?? "first_name";
        $dir = $this->request->getVar('dir') ?? "asc";

        $sqlQuery = $this->contactModel
            ->where(['uuid_business_id' => session('uuid_business')])
            ->limit($limit, $offset)
            ->orderBy($order, $dir)
            ->get()
            ->getResultArray();
        if ($query) {
            $sqlQuery = $this->contactModel
                ->where(['uuid_business_id' => session('uuid_business')])
                ->like("first_name", $query)
                ->limit($limit, $offset)
                ->orderBy($order, $dir)
                ->get()
                ->getResultArray();
        }

        $countQuery = $this->contactModel
            ->where(["uuid_business_id"=> session("uuid_business")])
            ->countAllResults();
        if ($query) {
            $countQuery = $this->contactModel
                ->where(["uuid_business_id"=> session("uuid_business")])
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


		$data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
		$data["users"] = $this->model->getUser();
        $data["categories"] = $this->model->getCategories();
		$data["contact"] = $uuid ? $contactData : "";
        $data["customers"] = $customers;
        
		// if there any special cause we can overried this function and pass data to add or edit view
		$data['additional_data'] = $this->getAdditionalData($uuid);

        echo view($this->table."/edit",$data);
    }

    public function contactsCustomerAjax()
    {
        $q = $this->request->getVar('q');
        $data = $this->customers_model;
        if(!empty($q)) {
            $data = $data->like('company_name', $q);
        }
        $data = $data->limit(500)->get()->getResult();
        return $this->respond($data);
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
        
		$response = $this->model->insertOrUpdateByUUID($uuid, $data);
   
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
}