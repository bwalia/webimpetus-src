<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController; 
use App\Models\Users_model;
use App\Models\Core\Common_model;
use App\Libraries\UUID;
class Contacts extends CommonController
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

    public function edit($id = 0)
    {
        if(empty($id)){
            $data['uuid'] = UUID::v5(UUID::v4(), 'contacts_saving');
            $data['uuid_business_id'] = $this->session->get('uuid_business');

            $id = $this->model->insertOrUpdate($id, $data);

            return redirect()->to('/'.$this->table.'/edit/'.$id);die;
        }
		$data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
		$data["users"] = $this->model->getUser();
        $data["categories"] = $this->model->getCategories();
		$data[$this->rawTblName] = $this->model->getRows($id)->getRow();
		// if there any special cause we can overried this function and pass data to add or edit view
		$data['additional_data'] = $this->getAdditionalData($id);

        echo view($this->table."/edit",$data);
    }
    public function update()
    {        
       
        $id = $this->request->getPost('id');

		$data = $this->request->getPost();

        if(!isset($data['allow_web_access'])){
            $data['allow_web_access'] = 0;
        }
        if(empty($id)){
            $data['uuid'] = UUID::v5(UUID::v4(), 'contacts_saving');
            $data['uuid_business_id'] = $this->session->get('uuid_business');
        }
        if(strlen($data['password']) > 0){
            $data['password'] = md5($data['password']);
        }
        
		$response = $this->model->insertOrUpdate($id, $data);
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
}