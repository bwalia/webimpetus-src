<?php
namespace App\Controllers;
use App\Models\Service_model;
use App\Models\Tenant_model;
use App\Models\Domain_model;
use App\Models\Cat_model;
use App\Models\Content_model;
use App\Models\Enquiries_model;
use App\Models\Blocks_model;
use App\Models\Gallery_model;
use App\Models\Secret_model;
use App\Models\Documents_model;
use App\Models\Customers_model;
use App\Models\WebpageCategory;
use App\Models\CustomerCategory;
use App\Models\Email_model;
use App\Models\Menu_model;
use App\Models\Users_model;
use App\Models\Core\Common_model;
use App\Models\TimeslipsModel;
use App\Models\Tasks_model;
use App\Models\Purchase_invoice_model;
use App\Models\Sales_invoice_model;
use App\Models\Work_orders_model;
use App\Libraries\UUID;
class Api extends BaseController
{
    public function __construct()
    {
      $this->smodel = new Service_model();
      $this->tmodel = new Tenant_model();
      $this->dmodel = new Domain_model();
      $this->catmodel = new Cat_model();
      $this->cmodel = new Content_model();
      $this->emodel = new Enquiries_model();
      $this->bmodel = new Blocks_model();
      $this->gmodel = new Gallery_model();
      $this->sec_model = new Secret_model();
      $this->documents_model = new Documents_model();
      $this->customer_model = new Customers_model();
      $this->webCategory_model = new WebpageCategory();
      $this->cusCategory_model = new CustomerCategory();
      $this->emailModel = new Email_model();
      $this->menuModel = new Menu_model();
      $this->userModel = new Users_model();
      $this->timeSlipsModel = new TimeslipsModel();
      $this->tasksModel = new Tasks_model();
      $this->common_model = new Common_model();
      $this->purchase_invoice_model = new Purchase_invoice_model();
      $this->sales_invoice_model = new Sales_invoice_model();
      $this->work_orders_model = new Work_orders_model();
      header('Content-Type: application/json; charset=utf-8');
      // header('Access-Control-Allow-Origin: *');
      // header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
      //header('Access-Control-Allow-Headers: Accept,Authorization,Content-Type');
    }
    public function index()
    {
        echo 'API ....';
    }
    public function services($id=false,$write=false)
    {
        if($this->request->getVar('q')){
            $data['data'] = $this->smodel->where(['status' => 1])->like('name', $this->request->getVar('q'))->get()->getResult();
        }else {
            //$data['data'] = ($id>0)?$this->smodel->getWhere(['id' => $id,'status' => 1])->getRow():$this->smodel->getApiRows();
            if($id>0){
                $data1 = $this->smodel->getRows($id)->getRow();
                $data1->domains = $this->dmodel->where(['sid' => $id])->get()->getResult();
            }else {
                $data1 = $this->smodel->getApiRows();
            }
            $data['data'] =$data1;
        }
        $data['status'] = 'success';
        if($write==true){
            return json_encode($data['data']);
        }else echo json_encode($data); die;
    }
    public function tenants($id=false)
    {
        if($this->request->getVar('q')){
            $data['data'] = $this->tmodel->like('name', $this->request->getVar('q'))->get()->getResult();
        }else {
            $data['data'] = ($id>0)?$this->tmodel->getRows($id)->getRow():$this->tmodel->getRows();
        }
        $data['status'] = 'success';
        echo json_encode($data); die;
    }
    public function domains($id=false)
    {
        if($this->request->getVar('q')){
            $data['data'] = $this->dmodel->like('name', $this->request->getVar('q'))->get()->getResult();
        }else {
            $data['data'] = ($id>0)?$this->dmodel->getRows($id)->getRow():$this->dmodel->getRows();
        }
        $data['status'] = 'success';
        echo json_encode($data); die;
    }
    public function categories($id=false)
    {
        if($this->request->getVar('q')){
            $data['data'] = $this->catmodel->like('name', $this->request->getVar('q'))->get()->getResult();
        }else {
            $data['data'] = ($id>0)?$this->catmodel->getRows($id)->getRow():$this->catmodel->getRows();
        }
        $data['status'] = 'success';
        echo json_encode($data); die;
    }
    public function templates($type=1,$id=false)
    {
        if($this->request->getVar('q')){
            $data['data'] = $this->cmodel->where(['status' => 1,'type'=>$type])->like('name', $this->request->getVar('q'))->get()->getResult();
        }else {
            $data['data'] = ($id>0)?$this->cmodel->getWhere(['status' => 1,'id' => $id,'type'=>$type])->getRow():$this->cmodel->where(['status' => 1,'type'=>$type])->get()->getResult();
        }
        $data['status'] = 'success';
        echo json_encode($data); die;
    }
    public function enquiries($type=1,$id=false)
    {
        if($this->request->getVar('q')){
            $data['data'] = $this->emodel->where(['type'=>$type])->like('name', $this->request->getVar('q'))->get()->getResult();
        }else {
            $data['data'] = ($id>0)?$this->emodel->getWhere(['id' => $id,'type'=>$type])->getRow():$this->emodel->where(['type'=>$type])->get()->getResult();
        }
        $data['status'] = 'success';
        echo json_encode($data); die;
    }
    public function blocks($id=false)
    {
        if($this->request->getVar('q')){
            $data['data'] = $this->bmodel->like('code', $this->request->getVar('q'))->get()->getResult();
        }else {
            $data['data'] = ($id>0)?$this->bmodel->getWhere(['id' => $id])->getRow():$this->bmodel->get()->getResult();
        }
        $data['status'] = 'success';
        echo json_encode($data); die;
    }
    public function media($id=false)
    {
        if($this->request->getVar('q')){
            $data['data'] = $this->gmodel->like('code', $this->request->getVar('q'))->get()->getResult();
        }else {
            $data['data'] = ($id>0)?$this->gmodel->getWhere(['id' => $id])->getRow():$this->gmodel->get()->getResult();
        }
        $data['status'] = 'success';
        echo json_encode($data); die;
    }
    public function secrets($id=false)
    {
        if($this->request->getVar('q')){
            $data['data'] = $this->sec_model->like('key_name', $this->request->getVar('q'))->get()->getResult();
        }else {
            $data['data'] = ($id>0)?$this->sec_model->getWhere(['id' => $id])->getRow():$this->sec_model->get()->getResult();
        }
        $data['status'] = 'success';
        echo json_encode($data); die;
    }
    public function webpages($customer_id=false){
        $categories=$this->cusCategory_model->where('customer_id',$customer_id)->get()->getResult();
        $categoriesId=[];
        foreach($categories as $row)
        {
            $categoriesId[$row->categories_id]=$row->categories_id;
        }
        $webPagesId=[];
        if(count($categoriesId))
        {
            $webPages = $this->webCategory_model->whereIn('categories_id',$categoriesId)->get()->getResult();
            foreach($webPages as $row)
            {
                $webPagesId[$row->webpage_id]=$row->webpage_id;
            }
        }
        if(count($webPagesId))
        {
            $webpages = $this->cmodel->where("status", 1)->whereIn('id', $webPagesId)->get()->getResult();
            if( $webpages ){
                $webPageList = [];
                foreach($webpages as $key => $eachPage){
                    $eachPage->contacts = $this->getContacts(@$eachPage->id);
                    $webPageList[$key] = $eachPage;
                }
                $data['data'] = $webPageList;
                $data['status'] = 'success';
            }else{
                $data['status'] = 'error';
            }
        }
        else{
            $data['status'] = 'error';
        }
        echo json_encode($data); die;
    }
    public function getContacts( $id){
        $blocks = $this->bmodel->where(["webpages_id" => $id])->get()->getResult();
        return $blocks;
    }
    public function webpagesEdit($id=false){
        if( strlen($id) > 0){
            $webpages = $this->cmodel->where(["id" => $id])->get()->getResult();
            $blocks = $this->bmodel->where(["webpages_id" => $id])->get()->getResult();
            $webPageList = [];
            $blockList = [];
            foreach($blocks as $eachBlock){
                if( $eachBlock->webpages_id > 0){
                    $blockList[$eachBlock->webpages_id][] = $eachBlock;
                }
            }
            foreach($webpages as $key => $eachPage){
                $webPageList[$key] = $eachPage;
                $webPageList[$key]->blockList = @$blockList[$eachPage->id];
            }
            $data['data'] = $webPageList;
            $data['status'] = 'success';
        }else{
            $data['status'] = 'error';
            $data['status'] = 'Id is missing';
        }
        echo json_encode($data); die;
    }
    public function getDocument($id=false){
        if( strlen($id) > 0){
            $documents = $this->documents_model->where(["id" => $id])->get()->getResult();
        }else{
            $documents = $this->documents_model->get()->getResult();
        }
        $documentList = [];
        foreach($documents as $key => $eachPage){
            $documentList[$key] = $eachPage;
        }
        $data['data'] = $documentList;
        $data['status'] = 'success';
        echo json_encode($data); die;
    }
    public function sendEmail() {
        $name = $this->request->getVar('name');
        $ccEmail = $this->request->getVar('email');
        $token = $this->request->getVar('token');
		$secretKey = getenv('CAPTCHA_SECRET_KEY');
		$verifyCaptchaUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$token";
		
		$client = \Config\Services::curlrequest();
		$response = $client->request('GET', $verifyCaptchaUrl, [
			'auth' => ['user', 'pass'],
		]);

		$responseBody = json_decode($response->getBody());
		if ($responseBody->success) {
			$toEmail = !empty(getenv('ADMINISTRATOR_EMAIL')) ? getenv('ADMINISTRATOR_EMAIL') : 'balinder.walia@gmail.com';
			// BW/HS: we add block query here to fetch email address from block table customer field
			if (strlen(trim($toEmail)) < 1) {
				$data['status'] = 'error';
				$data['msg']    = 'Please contact website administrator!';
				echo json_encode($data); die;
			}
			if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
				$data['status'] = 'error';
				$data['msg']    = 'Please Enter a valid email!';
				echo json_encode($data); die;
			}
			$message = $this->request->getVar('message');
			$organisation = $this->request->getVar('organisation');
			$organisation = isset($organisation) ? $organisation : 'Organization not provided';
			$phone = $this->request->getVar('phone');
			if (isset($message)) {
				$emailMessage =
					'Email Sent by user ' .
					$name . "<br>
					Phone: " . $phone . "<br>
					Organization: " . $organisation . "<br>
					Message: " . $message;
			} else {
				$emailMessage = 'Email Sent by user '.$name."<br> Phone ".$phone. "<br> Organization :".$organisation;
			}
			$uuid_business_id = $this->request->getVar('uuid_business_id') ? $this->request->getVar('uuid_business_id') : 6;
			$subject = "Odin contact email";
			$name = $name ? $name : "";
			$from_email = "info@odincm.com";
			$is_send = $this->emailModel->send_mail($from_email, $name, $from_email, $emailMessage, $subject, [], "", $ccEmail);
			if($is_send){
				$data['status'] = 'success';
				$data['msg']    = 'Email send successfully!';
				$insertArray["uuid_business_id"] = $uuid_business_id;
				$insertArray["name"] = $name;
				$insertArray["email"] = $ccEmail;
				$insertArray["phone"] = $phone;
				$insertArray["message"] = $emailMessage;
				$insertArray["type"] = 1;
				$this->emodel->saveData($insertArray);
			}else{
				$data['status'] = 'error';
				$data['msg']    = 'Email send failed!';
			}
		} else {
			$data['status'] = 'error';
			$data['msg']    = 'Captcha failed!';
		}
		echo json_encode($data); die;
    }

    public function menus($uuid_business_id='',$lang='en')
    {   
        $data['data'] = $this->menuModel->getMenu($uuid_business_id, $lang);
        $data['status'] = 'success';
        echo json_encode($data); die;
    }

    public function addMenu()
    { 
        if(empty($this->request->getPost('name')) || empty($this->request->getPost('link')) || empty($this->request->getPost('uuid_business_id'))){
            $data['status'] = 'error';
            $data['msg']    = 'name, link and business id required for add menu!!';
        }else {
            $cat_data = [];
            $cat_data['name'] = $this->request->getPost('name');
            $cat_data['link'] = $this->request->getPost('link');
            $cat_data['icon'] = $this->request->getPost('icon');
            $cat_data['language_code'] = $this->request->getPost('language_code')?$this->request->getPost('language_code'):'en';
            $cat_data['menu_fts'] = !empty($this->request->getPost('tags'))?implode(',',$this->request->getPost('tags')):$this->request->getPost('name');
            $cat_data['uuid_business_id'] = $this->request->getPost('uuid_business'); 
            $in_id = $this->menuModel->saveData($cat_data);
            $this->menuModel->saveMenuCat($in_id,$this->request->getPost('categories')); 
            
            $data['data'] = $cat_data;
            $data['status'] = 'success';
        }
        echo json_encode($data); die;
    }

    public function updateMenu()
    { 
        if(empty($this->request->getPost('name')) || empty($this->request->getPost('link')) || empty($this->request->getPost('uuid'))){
            $data['status'] = 'error';
            $data['msg']    = 'name, business id, Menu uuid and link required for update menu!!';
        }else {
            $cat_data = [];
            $cat_data['name'] = $this->request->getPost('name');
            $cat_data['link'] = $this->request->getPost('link');
            if(!empty($this->request->getPost('icon'))) $cat_data['icon'] = $this->request->getPost('icon');
            if(!empty($this->request->getPost('language_code'))) $cat_data['language_code'] = $this->request->getPost('language_code')?$this->request->getPost('language_code'):'en';
            if(!empty($this->request->getPost('menu_fts')))  $cat_data['menu_fts'] = !empty($this->request->getPost('tags'))?implode(',',$this->request->getPost('tags')):$this->request->getPost('name');
            if(!empty($this->request->getPost('uuid_business_id')))  $cat_data['uuid_business_id'] = $this->request->getPost('uuid_business');        
            $this->menuModel->updateData($this->request->getPost('uuid'),$cat_data);
            $this->menuModel->saveMenuCat($this->request->getPost('uuid'),$this->request->getPost('categories'));           
            $data['status'] = 'success';
            $data['data'] = $cat_data;
        }        
        echo json_encode($data); die;
    }

    public function users($id = false)
    {   
        $data['data'] = $this->userModel->getApiUsers($id);
        $data['status'] = 'success';
        echo json_encode($data); die;
    }

    public function addUser()
    { 
        if(!empty($this->request->getPost('email')) && !empty($this->request->getPost('name')) && !empty($this->request->getPost('password')) && !empty($this->request->getPost('uuid_business_id'))){		

            $count = $this->userModel->getWhere(['email' => $this->request->getPost('email')])->getNumRows();
            if(!empty($count)){
                $data['status'] = 'error';
                $data['msg']    = 'Email already exist!!';
                echo json_encode($data); die;
            }else {
                $uuidNamespace = UUID::v4();
                $uuid = UUID::v5($uuidNamespace, 'users');
                $data_array = array(
                    'name'  => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'password' => md5($this->request->getPost('password')),
                    'address' => !empty($this->request->getPost('address'))?$this->request->getPost('address'):'',
                    'notes' => !empty($this->request->getPost('notes'))?$this->request->getPost('notes'):'',
                    'language_code' => !empty($this->request->getPost('language_code'))?$this->request->getPost('language_code'):'en',
                    'uuid' => $uuid,
                    'uuid_business_id' => $this->request->getPost('uuid_business_id'),
                    'status' => 0,
                    'permissions' => !empty($this->request->getPost('permissions'))?json_encode($this->request->getPost('permissions')):'',
                    'role' => $this->request->getPost('role')?$this->request->getPost('role'):0,
                );
                //echo json_encode($data_array); die;
                $this->userModel->saveUser($data_array);
                $data['data'] = $data_array;
                $data['status'] = 'success';
                echo json_encode($data); die;
            }           

        }else {
            $data['status'] = 'error';
            $data['msg']    = 'Name, Email, password, business uuid could not be empty!!';
            echo json_encode($data); die;  
        }
    }

    public function updateUser()
    {               
        $id = $this->request->getPost('uuid');
		if(!empty($id) && !empty($this->request->getPost('email'))){
			$count = $this->userModel->getWhere(['email' => $this->request->getPost('email'), 'id!=' => $id])->getNumRows();
            if(!empty($count)){ 
                $data['status'] = 'error';
                $data['msg']    = 'Email already exist!!';
                echo json_encode($data); die;                
            }else {
				$data_array = array(
					'email' => $this->request->getPost('email'),
				);

                if(!empty($this->request->getPost('name'))) $data_array['name'] = $this->request->getPost('name');
                if(!empty($this->request->getPost('address'))) $data_array['address'] = $this->request->getPost('address');
                if(!empty($this->request->getPost('notes'))) $data_array['notes'] = $this->request->getPost('notes');
                if(!empty($this->request->getPost('role'))) $data_array['role'] = $this->request->getPost('role');
                if(!empty($this->request->getPost('language_code'))) $data_array['language_code'] = $this->request->getPost('language_code');
                if(!empty($this->request->getPost('permissions'))) $data_array['permissions'] = !empty($this->request->getPost('permissions'))?json_encode($this->request->getPost('permissions')):'';
                
                $this->userModel->updateUser($data_array, $id);

                $data['data'] = $data_array;
                $data['status'] = 'success';
                echo json_encode($data); die;
			}	
		} else {
                $data['status'] = 'error';
                $data['msg']    = 'Email and user uuid could not be empty!!';
                echo json_encode($data); die;  
        }
           
    }

    public function customers($id = false)
    {   
        $data['data'] = $this->customer_model->getCustomers($id);
        $data['status'] = 'success';
        echo json_encode($data); die;
    }

    public function addCustomer()
    {
        if(!empty($this->request->getPost('company_name')) && !empty($this->request->getPost('acc_no')) && !empty($this->request->getPost('uuid_business'))){		
                
            $post = $this->request->getPost(); 
            $data["company_name"] = @$post["company_name"];
            $data["acc_no"] = @$post["acc_no"];
            $data["status"] = @$post["status"];
            $data["uuid_business_id"] = @$post['uuid_business'];
            $data["uuid"] = UUID::v5(UUID::v4(), 'customers');
            if(!empty($post["contact_firstname"])) $data["contact_firstname"] = @$post["contact_firstname"];
            if(!empty($post["contact_lastname"])) $data["contact_lastname"] = @$post["contact_lastname"];
            if(!empty($post["email"])) $data["email"] = @$post["email"];
            if(!empty($post["address1"])) $data["address1"] = @$post["address1"];
            if(!empty($post["address2"])) $data["address2"] = @$post["address2"];
            if(!empty($post["city"])) $data["city"] = @$post["city"];
            if(!empty($post["country"])) $data["country"] = @$post["country"];
            if(!empty($post["postal_code"])) $data["postal_code"] = @$post["postal_code"];
            if(!empty($post["phone"])) $data["phone"] = @$post["phone"];
            if(!empty($post["notes"])) $data["notes"] = @$post["notes"];
            if(!empty($post["supplier"])) $data["supplier"] = @$post["supplier"];
            if(!empty($post["website"])) $data["website"] = @$post["website"];
            if(!empty($post["categories"])) $data["categories"] = !empty($post["categories"])?json_encode(@$post["categories"]):json_encode([]);
            //$data['id']= @$post["id"];
            $response = $this->customer_model->insertOrUpdate('', $data);
            if(!$response){
                $response_data['status'] = 'error';
                $response_data['msg']    = 'something wrong!!';
                echo json_encode($response_data); die;   
            }else{           
                $id = $response;          
                $i = 0;
                if(count($post["first_name"])>0){
                    foreach($post["first_name"] as $firstName){

                        $contact["first_name"] = $firstName;
                        $contact["client_id"] = $data["uuid"];
                        $contact["surname"] = @$post["surname"][$i];
                        $contact["email"] = @$post["contact_email"][$i];
                        $contact["uuid_business_id"] = @$post['uuid_business'];
                        $contactId =  '';
                        if(strlen(trim($firstName)) > 0 || strlen(trim($contact["surname"])>0) || strlen(trim($contact["email"])>0)){
                            $this->common_model->CommonInsertOrUpdate("contacts",$contactId, $contact);
                            //print_r($contact); die;
                        }

                        $i++;
                    }


                }

                if(!empty($post["categories"])){
                    $this->common_model->deleteTableData("customer_categories", $id, "customer_id");
                    foreach( $post["categories"] as $key => $categories_id){

                        $c_data = [];

                        $c_data['customer_id'] = $data["uuid"];
                        $c_data['categories_id'] = $categories_id;
                        //print_r($c_data); die;

                        $this->common_model->CommonInsertOrUpdate("customer_categories",'',$c_data);
                    }
                }
                $response_data['data'] = $data;
                $response_data['status'] = 'success';
                echo json_encode($response_data); die;
            }

        }else {
            $response_data['status'] = 'error';
            $response_data['msg']    = 'business uuid, Company name and account number cannot be empty!!';
            echo json_encode($response_data); die; 
        }


    }

    public function updateCustomer()
    {  
            if(!empty($this->request->getPost('company_name')) && !empty($this->request->getPost('acc_no')) && !empty($this->request->getPost('uuid')) && !empty($this->request->getPost('uuid_business'))){	     
                $post = $this->request->getPost(); 
                $data["company_name"] = @$post["company_name"];
                $data["acc_no"] = @$post["acc_no"];
                $data["uuid_business_id"] = @$post['uuid_business'];
                if(!empty($post["contact_firstname"])) $data["contact_firstname"] = @$post["contact_firstname"];
                if(!empty($post["contact_lastname"])) $data["contact_lastname"] = @$post["contact_lastname"];
                if(!empty($post["email"])) $data["email"] = @$post["email"];
                if(!empty($post["address1"])) $data["address1"] = @$post["address1"];
                if(!empty($post["address2"])) $data["address2"] = @$post["address2"];
                if(!empty($post["city"])) $data["city"] = @$post["city"];
                if(!empty($post["country"])) $data["country"] = @$post["country"];
                if(!empty($post["postal_code"])) $data["postal_code"] = @$post["postal_code"];
                if(!empty($post["phone"])) $data["phone"] = @$post["phone"];
                if(!empty($post["notes"])) $data["notes"] = @$post["notes"];
                if(!empty($post["supplier"])) $data["supplier"] = @$post["supplier"];
                if(!empty($post["website"])) $data["website"] = @$post["website"];
                if(!empty($post["categories"])) $data["categories"] = !empty($post["categories"])?json_encode(@$post["categories"]):json_encode([]);

                $id= $post["uuid"];
                $response = $this->customer_model->insertOrUpdate($id, $data);
                if(!$response){
                    $response_data['status'] = 'error';
                    $response_data['msg']    = 'something wrong!!';
                    echo json_encode($response_data); die;   
                }else{            
                    $i = 0;

                    if(!empty($post["first_name"])){
                        foreach($post["first_name"] as $firstName){

                            $contact["first_name"] = $firstName;
                            $contact["client_id"] = $id;
                            $contact["surname"] = $post["surname"][$i];
                            $contact["email"] = $post["contact_email"][$i];
                            $contact["uuid_business_id"] = $post['uuid_business'];
                            $contactId =  @$post["contact_id"][$i];
                            if(strlen(trim($firstName)) > 0 || strlen(trim($contact["surname"])>0) || strlen(trim($contact["email"])>0)){
                                $this->common_model->CommonInsertOrUpdate("contacts",$contactId, $contact);
                            }

                            $i++;
                        }


                    }

                    if(!empty($post["categories"])){
                        $this->common_model->deleteTableData("customer_categories", $id, "customer_id");
                        foreach( $post["categories"] as $key => $categories_id){

                            $c_data = [];

                            $c_data['customer_id'] = $id;
                            $c_data['categories_id'] = $categories_id;
                            //print_r($c_data); die;

                            $this->common_model->CommonInsertOrUpdate("customer_categories",'',$c_data);
                        }
                    }
                    $response_data['data'] = $data;
                    $response_data['status'] = 'success';
                    echo json_encode($response_data); die;
                }
        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'uuid, business uuid, Company name and account number cannot be empty!!';
            echo json_encode($response_data); die;         
        }
    }

    public function timeslips($ubusiness_id = "") {
        $arr2 = [];
        $search = !empty($_GET['filter'])?$_GET['filter']:'';
        if(!empty($ubusiness_id)){
            $arr2 = ['tasks.uuid_business_id'=>$ubusiness_id];
        }

        $list_week = !empty($_GET['list_week'])?$_GET['list_week']:'';
        $list_monthpicker = !empty($_GET['list_month'])?$_GET['list_month']:'';
        $list_yearpicker = !empty($_GET['list_year'])?$_GET['list_year']:'';

        if (!empty($list_week)) {
            $arr2['week_no'] = $list_week;
        }

        if (!empty($list_monthpicker) && !empty($list_yearpicker)) {
           
            $lmonth = "{$list_yearpicker}-{$list_monthpicker}-01";
            $submitted_time = strtotime($lmonth);
            $submitted_time2 = strtotime("{$list_yearpicker}-{$list_monthpicker}-".date("t", strtotime($lmonth)));
            $arr2['slip_start_date >='] = $submitted_time;
            $arr2['slip_start_date <='] = $submitted_time2;
        }

        $count = $this->timeSlipsModel->getApiCount(false, $arr2,$search);
        $rows = $this->timeSlipsModel->getApiRows(false, $arr2,$search);
        //echo $this->timeSlipsModel->getLastQuery()->getQuery();die;
        $data['data'] = $rows;
        $data['total'] = $count;
        $data['status'] = 'success';
        echo json_encode($data); die;
    }

    public function timeslip($uuid = "") {
        $row = $this->timeSlipsModel->getApiRows($uuid);
        //$this->timeSlipsModel->getLastQuery()->getQuery();die;
        $data['data'] = $row;
        $data['status'] = 'success';
        echo json_encode($data); die;
    }

    public function addTimeslip()
    {
        // $post = $this->request->getPost(); 
        // echo '<pre>';print_r($post); die;
        if(!empty($this->request->getPost('task_id')) && !empty($this->request->getPost('slip_start_date')) && !empty($this->request->getPost('uuid_business_id')) && !empty($this->request->getPost('employee_id'))){	
            
            $uuidVal = UUID::v5(UUID::v4(), 'timeslips_saving');
                
            $post = $this->request->getPost(); 
            $data["task_name"] = @$post["task_id"];
            $data["slip_start_date"] = strtotime(@$post["slip_start_date"]);
            $data["employee_name"] = @$post["employee_id"];
            $data["uuid_business_id"] = @$post['uuid_business_id'];
            $data["uuid"] = @$uuidVal;
            if(!empty($post["slip_timer_started"])) $data["slip_timer_started"] = @$post["slip_timer_started"];
            if(!empty($post["slip_end_date"])) $data["slip_end_date"] = @$post["slip_end_date"];
            if(!empty($post["slip_timer_end"])) $data["slip_timer_end"] = @$post["slip_timer_end"];
            if(!empty($post["break_time"])) $data["break_time"] = @$post["break_time"];
            if(!empty($post["break_time_start"])) $data["break_time_start"] = @$post["break_time_start"];
            if(!empty($post["break_time_end"])) $data["break_time_end"] = @$post["break_time_end"];
            if(!empty($post["slip_hours"])) $data["slip_hours"] = @$post["slip_hours"];
            if(!empty($post["slip_description"])) $data["slip_description"] = @$post["slip_description"];
            if(!empty($post["slip_rate"])) $data["slip_rate"] = @$post["slip_rate"];
            if(!empty($post["slip_timer_accumulated_seconds"])) $data["slip_timer_accumulated_seconds"] = @$post["slip_timer_accumulated_seconds"];
            if(!empty($post["billing_status"])) $data["billing_status"] = @$post["billing_status"];
            
            //$data['id']= @$post["id"];
            $response = $this->timeSlipsModel->saveByUuid('', $data);
            $response_data['data'] = $data;
            $response_data['status'] = 'success';
            echo json_encode($response_data); die;
        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'Task id, slip start date,employee id , business uuid cannot be empty!!';
            echo json_encode($response_data); die;         
        }
    }

    public function updateTimeslip()
    {
        if(!empty($this->request->getPost('uuid'))){	
            
            $uuidVal = $this->request->getPost('uuid');
                
            $post = $this->request->getPost(); 
            if(!empty($post["task_id"])) $data["task_name"] = @$post["task_id"];
            if(!empty($post["slip_start_date"])) $data["slip_start_date"] = strtotime(@$post["slip_start_date"]);
            $data["employee_name"] = @$post["employee_id"];
            if(!empty($post["uuid_business_id"])) $data["uuid_business_id"] = @$post['uuid_business_id'];
            if(!empty($post["slip_timer_started"])) $data["slip_timer_started"] = @$post["slip_timer_started"];
            if(!empty($post["slip_end_date"])) $data["slip_end_date"] = strtotime(@$post["slip_end_date"]);
            if(!empty($post["slip_timer_end"])) $data["slip_timer_end"] = @$post["slip_timer_end"];
            if(!empty($post["break_time"])) $data["break_time"] = @$post["break_time"];
            if(!empty($post["break_time_start"])) $data["break_time_start"] = @$post["break_time_start"];
            if(!empty($post["break_time_end"])) $data["break_time_end"] = @$post["break_time_end"];
            if(!empty($post["slip_hours"])) $data["slip_hours"] = @$post["slip_hours"];
            if(!empty($post["slip_description"])) $data["slip_description"] = @$post["slip_description"];
            if(!empty($post["slip_rate"])) $data["slip_rate"] = @$post["slip_rate"];
            if(!empty($post["slip_timer_accumulated_seconds"])) $data["slip_timer_accumulated_seconds"] = @$post["slip_timer_accumulated_seconds"];
            if(!empty($post["billing_status"])) $data["billing_status"] = @$post["billing_status"];
            
            //$data['id']= @$post["id"];
            $response = $this->timeSlipsModel->saveByUuid($uuidVal, $data);
            $response_data['data'] = $data;
            $response_data['status'] = 'success';
            echo json_encode($response_data); die;
        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'uuid, task id cannot be empty!!';
            echo json_encode($response_data); die;         
        }
    }

    public function tasks($ubusiness_id = false) {
        $rows = $this->tasksModel->getApiTaskList($ubusiness_id);
        $data['data'] = $rows;
        $data['status'] = 'success';
        echo json_encode($data); die;
    }

    public function addTask()
    {
        // $post = $this->request->getPost(); 
        if(!empty($this->request->getPost('projects_id')) && !empty($this->request->getPost('customers_id')) && !empty($this->request->getPost('contacts_id')) && !empty($this->request->getPost('name')) && !empty($this->request->getPost('reported_by')) && !empty($this->request->getPost('category')) && !empty($this->request->getPost('start_date')) && !empty($this->request->getPost('priority')) && !empty($this->request->getPost('end_date')) && !empty($this->request->getPost('sprint_id')) && !empty($this->request->getPost('uuid_business_id'))){	
                
            $post = $this->request->getPost(); 
            $data["projects_id"] = @$post["projects_id"];
            $data["contacts_id"] = @$post["contacts_id"];
            $data["customers_id"] = @$post["customers_id"];
            $uuidVal = UUID::v5(UUID::v4(), 'tasks');
            $data["uuid_business_id"] = @$post['uuid_business_id'];
            $data["uuid"] = $uuidVal;
            $data["name"] = @$post["name"];
            $data["reported_by"] = @$post["reported_by"];
            $data["category"] = @$post["category"];
            $data["start_date"] = strtotime(@$post["start_date"]);
            $data["priority"] = @$post["priority"];
            $data["end_date"] = strtotime(@$post["end_date"]);
            $data["sprint_id"] = @$post["sprint_id"];
            $data['task_id'] = $this->common_model->CommonfindMaxFieldValue('tasks', "task_id");
            //echo '<pre>';print_r($data); die;
            if (empty($data['task_id'])) {
                $data['task_id'] = 1001;
            } else {
                $data['task_id'] += 1;
            }
            $data["status"] = !empty($post["status"])?@$post["status"]:'assigned';
            $data["active"] = !empty($post["active"])?@$post["active"]:1;

            if(!empty($post["estimated_hour"])) $data["estimated_hour"] = @$post["estimated_hour"];
            if(!empty($post["rate"])) $data["rate"] = @$post["rate"];
           

            $response = $this->common_model->CommonInsertOrUpdate("tasks",'',$data);
            $response_data['data'] = $data;
            $response_data['status'] = 'success';
            echo json_encode($response_data); die;
        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'projects_id, customers_id, contacts_id, name,reported_by, category, start_date, priority, end_date, sprint_id business uuid cannot be empty!!';
            echo json_encode($response_data); die;         
        }
    }

    public function updateTask()
    {
        // $post = $this->request->getPost(); 
        if(!empty($this->request->getPost('uuid'))){	
                
            $post = $this->request->getPost(); 
            if(!empty($post["projects_id"])) $data["projects_id"] = @$post["projects_id"];
            if(!empty($post["contacts_id"])) $data["contacts_id"] = @$post["contacts_id"];
            if(!empty($post["customers_id"])) $data["customers_id"] = @$post["customers_id"];
            if(!empty($post["uuid_business_id"])) $data["uuid_business_id"] = @$post['uuid_business_id'];
            if(!empty($post["name"])) $data["name"] = @$post["name"];
            if(!empty($post["reported_by"])) $data["reported_by"] = @$post["reported_by"];
            if(!empty($post["category"])) $data["category"] = @$post["category"];
            if(!empty($post["start_date"])) $data["start_date"] = strtotime(@$post["start_date"]);
            if(!empty($post["priority"])) $data["priority"] = @$post["priority"];
            if(!empty($post["end_date"])) $data["end_date"] = strtotime(@$post["end_date"]);
            if(!empty($post["sprint_id"])) $data["sprint_id"] = @$post["sprint_id"];
            
            if(!empty($post["status"])) $data["status"] = @$post["status"];
            if(!empty($post["active"]))  $data["active"] = @$post["active"];
            if(!empty($post["estimated_hour"])) $data["estimated_hour"] = @$post["estimated_hour"];
            if(!empty($post["rate"])) $data["rate"] = @$post["rate"];           

            $response = $this->common_model->CommonInsertOrUpdate("tasks",$post['uuid'],$data);
            $response_data['data'] = $data;
            $response_data['status'] = 'success';
            echo json_encode($response_data); die;
        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'task uuid cannot be empty!!';
            echo json_encode($response_data); die;         
        }
    }

    public function employees($id = false)
    {   
        $data['data'] = $id!=false?$this->common_model->getCommonData('employees',array('uuid_business_id'=>$id)):$this->common_model->getCommonData('employees');
        $data['status'] = 'success';
        echo json_encode($data); die;
    }

    public function addEmployee()
    {
        // $post = $this->request->getPost(); 
        // echo '<pre>';print_r($post); die;
        if(!empty($this->request->getPost('first_name')) && !empty($this->request->getPost('email')) && !empty($this->request->getPost('uuid_business_id'))){
            $count = $this->common_model->getCount('employees',['email'=>$this->request->getPost('email')]);
            if(!empty($count)){
                $data['status'] = 'error';
                $data['msg']    = 'Email already exist!!';
                echo json_encode($data); die;
            }else {
                
                $post = $this->request->getPost(); 
                $data["first_name"] = @$post["first_name"];
                $data["email"] = @$post["email"];
                $data["uuid_business_id"] = @$post['uuid_business_id'];
                if(!empty($post["businesses"])) $data['businesses'] = json_encode(@$post['businesses']);
                $data['uuid'] = UUID::v5(UUID::v4(), 'employees');
                if(!empty($post["surname"])) $data["surname"] = @$post["surname"];            
                if(!empty($post["password"])) $data["password"] = md5(@$post["password"]);
                if(!empty($post["direct_phone"])) $data["direct_phone"] = @$post["direct_phone"];
                if(!empty($post["mobile"])) $data["mobile"] = @$post["mobile"];
                if(!empty($post["direct_fax"])) $data["direct_fax"] = @$post["direct_fax"];
                if(!empty($post["allow_web_access"])) $data["allow_web_access"] = @$post["allow_web_access"];
                if(!empty($post["comments"])) $data["comments"] = @$post["comments"];
                if(!empty($post["title"])) $data["title"] = @$post["title"];
                if(!empty($post["saludation"])) $data["saludation"] = @$post["saludation"];
                if(!empty($post["news_letter_status"])) $data["news_letter_status"] = @$post["news_letter_status"];
                
                //$data['id']= @$post["id"];
                $response = $this->common_model->CommonInsertOrUpdate('employees','', $data);
                $response_data['data'] = $data;
                $response_data['status'] = 'success';
                echo json_encode($response_data); die;
            }

        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'first_name, uuid_business_id and email cannot be empty!!';
            echo json_encode($response_data); die;         
        }
    }

    public function updateEmployee()
    {
        // $post = $this->request->getPost(); 
        // echo '<pre>';print_r($post); die;
        if(!empty($this->request->getPost('uuid'))){

            $count = $this->common_model->getCount('employees',['email'=>$this->request->getPost('email'),'id!='=>$this->request->getPost('uuid')]);
            if(!empty($count)){
                $data['status'] = 'error';
                $data['msg']    = 'Email already exist!!';
                echo json_encode($data); die;
            }else {                
                $post = $this->request->getPost(); 
                if(!empty($post["first_name"])) $data["first_name"] = @$post["first_name"];
                if(!empty($post["uuid_business_id"])) $data["uuid_business_id"] = @$post['uuid_business_id'];
                if(!empty($post["businesses"])) $data['businesses'] = json_encode(@$post['businesses']);
                if(!empty($post["surname"])) $data["surname"] = @$post["surname"];
                if(!empty($post["direct_phone"])) $data["direct_phone"] = @$post["direct_phone"];
                if(!empty($post["mobile"])) $data["mobile"] = @$post["mobile"];
                if(!empty($post["direct_fax"])) $data["direct_fax"] = @$post["direct_fax"];
                if(!empty($post["allow_web_access"])) $data["allow_web_access"] = @$post["allow_web_access"];
                if(!empty($post["comments"])) $data["comments"] = @$post["comments"];
                if(!empty($post["title"])) $data["title"] = @$post["title"];
                if(!empty($post["saludation"])) $data["saludation"] = @$post["saludation"];
                if(!empty($post["news_letter_status"])) $data["news_letter_status"] = @$post["news_letter_status"];
                //$data['id']= @$post["id"];
                $response = $this->common_model->CommonInsertOrUpdate('employees',$post['uuid'], $data);
                $response_data['data'] = $data;
                $response_data['status'] = 'success';
                echo json_encode($response_data); die;
            }

        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'employee uuid cannot be empty!!';
            echo json_encode($response_data); die;         
        }
    }

    public function purchase_invoices($id = false)
    {   
        $data['data'] = $id!=false?$this->purchase_invoice_model->getApiInvoice($id):$this->purchase_invoice_model->getApiInvoice();
        $data['status'] = 'success';
        echo json_encode($data); die;
    }

    public function addPurchaseInvoice()
    {
        // $post = $this->request->getPost(); 
        if(!empty($this->request->getPost('terms')) && !empty($this->request->getPost('date')) && !empty($this->request->getPost('due_date')) && !empty($this->request->getPost('project_code')) && !empty($this->request->getPost('supplier')) && !empty($this->request->getPost('uuid_business_id'))){            
                
            $post = $this->request->getPost(); 
            $data["uuid_business_id"] = @$post['uuid_business_id'];
            $data['uuid'] = UUID::v5(UUID::v4(), 'purchase_invoices');
            $data["terms"] = @$post["terms"];            
            $data["date"] = strtotime(@$post["date"]);
            $data["due_date"] = strtotime(@$post["due_date"]);
            $data["client_id"] = @$post["supplier"];
            $data["is_locked"] = !empty($post["is_locked"])?@$post["is_locked"]:0;
            $data["project_code"] = @$post["project_code"];
            if(!empty($post["custom_invoice_number"])) $data['custom_invoice_number'] = @$post['custom_invoice_number'];
            if(!empty($post["bill_to"])) $data["bill_to"] = @$post["bill_to"];
            if(!empty($post["order_by"])) $data["order_by"] = @$post["order_by"];
            if(!empty($post["short_notes"])) $data["notes"] = @$post["short_notes"];


            $data['invoice_number'] = $this->common_model->CommonfindMaxFieldValue('purchase_invoices', "invoice_number");
            if (empty($data['invoice_number'])) {
                $data['invoice_number'] = 1001;
            } else {
                $data['invoice_number'] += 1;
            }


            if(!empty($post["balance_due"])) $data["balance_due"] = @$post["balance_due"];
            $data["status"] = !empty($post["status"])?@$post["status"]:'Invoiced';
            if(!empty($post["total"])) $data["total"] = @$post["total"];
            if(!empty($post["total_paid"])) $data["total_paid"] = @$post["total_paid"];
            if(!empty($post["paid_date"])) $data["paid_date"] = strtotime(@$post["paid_date"]);
            if(!empty($post["inv_tax_code"])) $data["inv_tax_code"] = @$post["inv_tax_code"];
            if(!empty($post["total_hours"])) $data["total_hours"] = @$post["total_hours"];
            if(!empty($post["total_tax"])) $data["total_tax"] = @$post["total_tax"];
            if(!empty($post["total_due_with_tax"])) $data["total_due_with_tax"] = @$post["total_due_with_tax"];
            if(!empty($post["invoice_pin"])) $data["payment_pin_or_passcode"] = @$post["invoice_pin"];
            if(!empty($post["tax_rate"])) $data["invoice_tax_rate"] = @$post["tax_rate"];
            if(!empty($post["inv_template"])) $data["inv_template"] = @$post["inv_template"];
            if(!empty($post["print_template_code"])) $data["print_template_code"] = @$post["print_template_code"];
            if(!empty($post["internal_notes"])) $data["internal_notes"] = @$post["internal_notes"];
            if(!empty($post["inv_customer_ref_po"])) $data["inv_customer_ref_po"] = @$post["inv_customer_ref_po"];
            if(!empty($post["customer_currency_code"])) $data["currency_code"] = @$post["customer_currency_code"];
            if(!empty($post["base_currency_code"])) $data["base_currency_code"] = @$post["base_currency_code"];
            if(!empty($post["inv_exchange_rate"])) $data["inv_exchange_rate"] = @$post["inv_exchange_rate"];
            
            //$data['id']= @$post["id"];
            $response = $this->common_model->CommonInsertOrUpdate('purchase_invoices','', $data);

            if(!empty($post["amount"])){
                foreach( $post["amount"] as $key => $amount){
                    $c_data = [];
                    $c_data['amount'] = $amount>0?$amount:0.00;
                    $c_data['description'] = @$post['description'][$key];
                    $c_data['rate'] = !empty($post['rate'][$key])?$post['rate'][$key]:0.00;
                    $c_data['hours'] = !empty($post['hours'][$key])?$post['hours'][$key]:0.00;
                    $c_data['purchase_invoices_uuid'] = @$data['uuid'];
                    $c_data['uuid'] = UUID::v5(UUID::v4(), 'purchase_invoice_items');
                    $c_data['uuid_business_id'] = @$post['uuid_business_id'];
                    //print_r($c_data); die;

                    $this->common_model->CommonInsertOrUpdate("purchase_invoice_items",'',$c_data);
                }
            }

            //echo '<pre>';print_r($data); die;
            if(!empty($post["notes"])){
                foreach( $post["notes"] as $key => $note){
                    $notes = [];
                    $notes['notes'] = $note;
                    $notes['created_by'] = !empty($post['created_by'])?@$post['created_by']:1;
                    $notes['purchase_invoices_uuid'] = @$data['uuid'];
                    $notes['uuid'] = UUID::v5(UUID::v4(), 'purchase_invoice_notes');
                    $notes['uuid_business_id'] = @$post['uuid_business_id'];
                    //print_r($notes); die;

                    $this->common_model->CommonInsertOrUpdate("purchase_invoice_notes",'',$notes);
                }
            }


            $response_data['data'] = $data;
            $response_data['status'] = 'success';
            echo json_encode($response_data); die;

        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'terms, date, due_date, supplier and uuid_business_id cannot be empty!!';
            echo json_encode($response_data); die;         
        }

    }

    public function updatePurchaseInvoice()
    {
        // $post = $this->request->getPost(); 
        // echo '<pre>';print_r($post); die;
        if(!empty($this->request->getPost('uuid'))){            
                
            $post = $this->request->getPost(); 
            if(!empty($post["uuid_business_id"])) $data["uuid_business_id"] = @$post['uuid_business_id'];
            if(!empty($post["terms"])) $data["terms"] = @$post["terms"];            
            if(!empty($post["date"])) $data["date"] = strtotime(@$post["date"]);
            if(!empty($post["due_date"])) $data["due_date"] = strtotime(@$post["due_date"]);
            if(!empty($post["project_code"])) $data["project_code"] = @$post["project_code"];
            if(!empty($post["custom_invoice_number"])) $data['custom_invoice_number'] = $post['custom_invoice_number'];
            if(!empty($post["supplier"])) $data["client_id"] = @$post["supplier"];
            if(!empty($post["bill_to"])) $data["bill_to"] = @$post["bill_to"];
            if(!empty($post["order_by"])) $data["order_by"] = @$post["order_by"];
            if(!empty($post["invoice_notes"])) $data["notes"] = @$post["invoice_notes"];


            if(!empty($post["balance_due"])) $data["balance_due"] = @$post["balance_due"];
            if(!empty($post["status"])) $data["status"] = @$post["status"];
            if(!empty($post["total"])) $data["total"] = @$post["total"];
            if(!empty($post["total_paid"])) $data["total_paid"] = @$post["total_paid"];
            if(!empty($post["paid_date"])) $data["paid_date"] = strtotime(@$post["paid_date"]);
            if(!empty($post["inv_tax_code"])) $data["inv_tax_code"] = @$post["inv_tax_code"];
            if(!empty($post["total_hours"])) $data["total_hours"] = @$post["total_hours"];
            if(!empty($post["total_tax"])) $data["total_tax"] = @$post["total_tax"];
            if(!empty($post["total_due_with_tax"])) $data["total_due_with_tax"] = @$post["total_due_with_tax"];
            if(!empty($post["invoice_pin"])) $data["payment_pin_or_passcode"] = @$post["invoice_pin"];
            if(!empty($post["tax_rate"])) $data["invoice_tax_rate"] = @$post["tax_rate"];
            if(!empty($post["inv_template"])) $data["inv_template"] = @$post["inv_template"];
            if(!empty($post["print_template_code"])) $data["print_template_code"] = @$post["print_template_code"];
            if(!empty($post["internal_notes"])) $data["internal_notes"] = @$post["internal_notes"];
            if(!empty($post["inv_customer_ref_po"])) $data["inv_customer_ref_po"] = @$post["inv_customer_ref_po"];
            if(!empty($post["customer_currency_code"])) $data["customer_currency_code"] = @$post["customer_currency_code"];
            if(!empty($post["base_currency_code"])) $data["base_currency_code"] = @$post["base_currency_code"];
            if(!empty($post["inv_exchange_rate"])) $data["inv_exchange_rate"] = @$post["inv_exchange_rate"];
            if(!empty($post["is_locked"])) $data["is_locked"] = @$post["is_locked"];
            if(!empty($post["short_notes"])) $data["notes"] = @$post["short_notes"];
            
            //$data['id']= @$post["id"];
            $response = $this->common_model->CommonInsertOrUpdate('purchase_invoices',$this->request->getPost('uuid'), $data);

            if(!empty($post["amount"])){
                foreach( $post["amount"] as $key => $amount){
                    $c_data = [];
                    $c_data['amount'] = $amount>0?$amount:0.00;
                    $c_data['description'] = @$post['description'][$key];
                    $c_data['rate'] = !empty($post['rate'][$key])?$post['rate'][$key]:0.00;
                    $c_data['hours'] = !empty($post['hours'][$key])?$post['hours'][$key]:0.00;
                    $c_data['purchase_invoices_uuid'] = @$post['uuid'];
                    $c_data['uuid'] = UUID::v5(UUID::v4(), 'purchase_invoice_items');
                    $c_data['uuid_business_id'] = @$post['uuid_business_id'];
                    //print_r($c_data); die;

                    $this->common_model->CommonInsertOrUpdate("purchase_invoice_items",'',$c_data);
                }
            }

            //echo '<pre>';print_r($data); die;
            if(!empty($post["notes"])){
                foreach( $post["notes"] as $key => $note){
                    $notes = [];
                    $notes['notes'] = $note;
                    $notes['created_by'] = !empty($post['created_by'])?@$post['created_by']:1;
                    $notes['purchase_invoices_uuid'] = @$post['uuid'];
                    $notes['uuid'] = UUID::v5(UUID::v4(), 'purchase_invoice_notes');
                    $notes['uuid_business_id'] = @$post['uuid_business_id'];
                    //print_r($notes); die;

                    $this->common_model->CommonInsertOrUpdate("purchase_invoice_notes",'',$notes);
                }
            }

            $response_data['data'] = $data;
            $response_data['status'] = 'success';
            echo json_encode($response_data); die;

        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'uuid cannot be empty!!';
            echo json_encode($response_data); die;         
        }

    }

    public function sales_invoices($id = false)
    {   
        $data['data'] = $id!=false?$this->sales_invoice_model->getApiInvoice($id):$this->sales_invoice_model->getApiInvoice();
        $data['status'] = 'success';
        echo json_encode($data); die;
    }

    public function addSalesInvoice()
    {
        // $post = $this->request->getPost(); 
        if(!empty($this->request->getPost('terms')) && !empty($this->request->getPost('date')) && !empty($this->request->getPost('due_date')) && !empty($this->request->getPost('project_code')) && !empty($this->request->getPost('supplier')) && !empty($this->request->getPost('uuid_business_id'))){            
                
            $post = $this->request->getPost(); 
            $data["uuid_business_id"] = @$post['uuid_business_id'];
            $data['uuid'] = UUID::v5(UUID::v4(), 'sales_invoices');
            $data["terms"] = @$post["terms"];            
            $data["date"] = strtotime(@$post["date"]);
            $data["due_date"] = strtotime(@$post["due_date"]);
            $data["client_id"] = @$post["supplier"];
            $data["is_locked"] = !empty($post["is_locked"])?@$post["is_locked"]:0;
            $data["project_code"] = @$post["project_code"];
            if(!empty($post["custom_invoice_number"])) $data['custom_invoice_number'] = @$post['custom_invoice_number'];
            if(!empty($post["bill_to"])) $data["bill_to"] = @$post["bill_to"];
            if(!empty($post["order_by"])) $data["order_by"] = @$post["order_by"];
            if(!empty($post["short_notes"])) $data["notes"] = @$post["short_notes"];


            $data['invoice_number'] = $this->common_model->CommonfindMaxFieldValue('sales_invoices', "invoice_number");
            if (empty($data['invoice_number'])) {
                $data['invoice_number'] = 1001;
            } else {
                $data['invoice_number'] += 1;
            }


            if(!empty($post["balance_due"])) $data["balance_due"] = @$post["balance_due"];
            $data["status"] = !empty($post["status"])?@$post["status"]:'Invoiced';
            if(!empty($post["total"])) $data["total"] = @$post["total"];
            if(!empty($post["total_paid"])) $data["total_paid"] = @$post["total_paid"];
            if(!empty($post["paid_date"])) $data["paid_date"] = strtotime(@$post["paid_date"]);
            if(!empty($post["inv_tax_code"])) $data["inv_tax_code"] = @$post["inv_tax_code"];
            if(!empty($post["total_hours"])) $data["total_hours"] = @$post["total_hours"];
            if(!empty($post["total_tax"])) $data["total_tax"] = @$post["total_tax"];
            if(!empty($post["total_due_with_tax"])) $data["total_due_with_tax"] = @$post["total_due_with_tax"];
            if(!empty($post["invoice_pin"])) $data["payment_pin_or_passcode"] = @$post["invoice_pin"];
            if(!empty($post["tax_rate"])) $data["invoice_tax_rate"] = @$post["tax_rate"];
            if(!empty($post["inv_template"])) $data["inv_template"] = @$post["inv_template"];
            if(!empty($post["print_template_code"])) $data["print_template_code"] = @$post["print_template_code"];
            if(!empty($post["internal_notes"])) $data["internal_notes"] = @$post["internal_notes"];
            if(!empty($post["inv_customer_ref_po"])) $data["inv_customer_ref_po"] = @$post["inv_customer_ref_po"];
            if(!empty($post["customer_currency_code"])) $data["currency_code"] = @$post["customer_currency_code"];
            if(!empty($post["base_currency_code"])) $data["base_currency_code"] = @$post["base_currency_code"];
            if(!empty($post["inv_exchange_rate"])) $data["inv_exchange_rate"] = @$post["inv_exchange_rate"];
            
            //$data['id']= @$post["id"];
            $response = $this->common_model->CommonInsertOrUpdate('sales_invoices','', $data);

            if(!empty($post["amount"])){
                foreach( $post["amount"] as $key => $amount){
                    $c_data = [];
                    $c_data['amount'] = $amount>0?$amount:0.00;
                    $c_data['description'] = @$post['description'][$key];
                    $c_data['rate'] = !empty($post['rate'][$key])?$post['rate'][$key]:0.00;
                    $c_data['hours'] = !empty($post['hours'][$key])?$post['hours'][$key]:0.00;
                    $c_data['sales_invoices_uuid'] = @$data['uuid'];
                    $c_data['uuid'] = UUID::v5(UUID::v4(), 'sales_invoice_items');
                    $c_data['uuid_business_id'] = @$post['uuid_business_id'];
                    //print_r($c_data); die;

                    $this->common_model->CommonInsertOrUpdate("sales_invoice_items",'',$c_data);
                }
            }

            //echo '<pre>';print_r($data); die;
            if(!empty($post["notes"])){
                foreach( $post["notes"] as $key => $note){
                    $notes = [];
                    $notes['notes'] = $note;
                    $notes['created_by'] = !empty($post['created_by'])?@$post['created_by']:1;
                    $notes['sales_invoices_uuid'] = @$data['uuid'];
                    $notes['uuid'] = UUID::v5(UUID::v4(), 'sales_invoice_notes');
                    $notes['uuid_business_id'] = @$post['uuid_business_id'];
                    //print_r($notes); die;

                    $this->common_model->CommonInsertOrUpdate("sales_invoice_notes",'',$notes);
                }
            }


            $response_data['data'] = $data;
            $response_data['status'] = 'success';
            echo json_encode($response_data); die;

        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'terms, date, due_date, supplier and uuid_business_id cannot be empty!!';
            echo json_encode($response_data); die;         
        }

    }

    public function updateSalesInvoice()
    {
        // $post = $this->request->getPost(); 
        // echo '<pre>';print_r($post); die;
        if(!empty($this->request->getPost('uuid'))){            
                
            $post = $this->request->getPost(); 
            if(!empty($post["uuid_business_id"])) $data["uuid_business_id"] = @$post['uuid_business_id'];
            if(!empty($post["terms"])) $data["terms"] = @$post["terms"];            
            if(!empty($post["date"])) $data["date"] = strtotime(@$post["date"]);
            if(!empty($post["due_date"])) $data["due_date"] = strtotime(@$post["due_date"]);
            if(!empty($post["project_code"])) $data["project_code"] = @$post["project_code"];
            if(!empty($post["custom_invoice_number"])) $data['custom_invoice_number'] = $post['custom_invoice_number'];
            if(!empty($post["supplier"])) $data["client_id"] = @$post["supplier"];
            if(!empty($post["bill_to"])) $data["bill_to"] = @$post["bill_to"];
            if(!empty($post["order_by"])) $data["order_by"] = @$post["order_by"];
            if(!empty($post["invoice_notes"])) $data["notes"] = @$post["invoice_notes"];


            if(!empty($post["balance_due"])) $data["balance_due"] = @$post["balance_due"];
            if(!empty($post["status"])) $data["status"] = @$post["status"];
            if(!empty($post["total"])) $data["total"] = @$post["total"];
            if(!empty($post["total_paid"])) $data["total_paid"] = @$post["total_paid"];
            if(!empty($post["paid_date"])) $data["paid_date"] = strtotime(@$post["paid_date"]);
            if(!empty($post["inv_tax_code"])) $data["inv_tax_code"] = @$post["inv_tax_code"];
            if(!empty($post["total_hours"])) $data["total_hours"] = @$post["total_hours"];
            if(!empty($post["total_tax"])) $data["total_tax"] = @$post["total_tax"];
            if(!empty($post["total_due_with_tax"])) $data["total_due_with_tax"] = @$post["total_due_with_tax"];
            if(!empty($post["invoice_pin"])) $data["payment_pin_or_passcode"] = @$post["invoice_pin"];
            if(!empty($post["tax_rate"])) $data["invoice_tax_rate"] = @$post["tax_rate"];
            if(!empty($post["inv_template"])) $data["inv_template"] = @$post["inv_template"];
            if(!empty($post["print_template_code"])) $data["print_template_code"] = @$post["print_template_code"];
            if(!empty($post["internal_notes"])) $data["internal_notes"] = @$post["internal_notes"];
            if(!empty($post["inv_customer_ref_po"])) $data["inv_customer_ref_po"] = @$post["inv_customer_ref_po"];
            if(!empty($post["customer_currency_code"])) $data["customer_currency_code"] = @$post["customer_currency_code"];
            if(!empty($post["base_currency_code"])) $data["base_currency_code"] = @$post["base_currency_code"];
            if(!empty($post["inv_exchange_rate"])) $data["inv_exchange_rate"] = @$post["inv_exchange_rate"];
            if(!empty($post["is_locked"])) $data["is_locked"] = @$post["is_locked"];
            if(!empty($post["short_notes"])) $data["notes"] = @$post["short_notes"];
            
            //$data['id']= @$post["id"];
            $response = $this->common_model->CommonInsertOrUpdate('sales_invoices',$this->request->getPost('uuid'), $data);

            if(!empty($post["amount"])){
                foreach( $post["amount"] as $key => $amount){
                    $c_data = [];
                    $c_data['amount'] = $amount>0?$amount:0.00;
                    $c_data['description'] = @$post['description'][$key];
                    $c_data['rate'] = !empty($post['rate'][$key])?$post['rate'][$key]:0.00;
                    $c_data['hours'] = !empty($post['hours'][$key])?$post['hours'][$key]:0.00;
                    $c_data['sales_invoices_uuid'] = @$post['uuid'];
                    $c_data['uuid'] = UUID::v5(UUID::v4(), 'sales_invoice_items');
                    $c_data['uuid_business_id'] = @$post['uuid_business_id'];
                    //print_r($c_data); die;

                    $this->common_model->CommonInsertOrUpdate("sales_invoice_items",'',$c_data);
                }
            }

            //echo '<pre>';print_r($data); die;
            if(!empty($post["notes"])){
                foreach( $post["notes"] as $key => $note){
                    $notes = [];
                    $notes['notes'] = $note;
                    $notes['created_by'] = !empty($post['created_by'])?@$post['created_by']:1;
                    $notes['sales_invoices_uuid'] = @$post['uuid'];
                    $notes['uuid'] = UUID::v5(UUID::v4(), 'sales_invoice_notes');
                    $notes['uuid_business_id'] = @$post['uuid_business_id'];
                    //print_r($notes); die;

                    $this->common_model->CommonInsertOrUpdate("sales_invoice_notes",'',$notes);
                }
            }

            $response_data['data'] = $data;
            $response_data['status'] = 'success';
            echo json_encode($response_data); die;

        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'uuid cannot be empty!!';
            echo json_encode($response_data); die;         
        }

    }

    public function work_orders($id = false)
    {   
        $data['data'] = $id!=false?$this->work_orders_model->getApiInvoice($id):$this->work_orders_model->getApiInvoice();
        $data['status'] = 'success';
        echo json_encode($data); die;
    }

    public function addWorkOrder()
    {
        if(!empty($this->request->getPost('client_id')) && !empty($this->request->getPost('uuid_business_id'))){            
                
            $post = $this->request->getPost(); 
            $data["uuid_business_id"] = @$post['uuid_business_id'];
            $data['uuid'] = UUID::v5(UUID::v4(), 'work_orders');            
            $data["date"] = !empty($post["date"])?strtotime(@$post["date"]):strtotime(date('m/d/Y'));
            $data["client_id"] = @$post["client_id"];
            $data["is_locked"] = !empty($post["is_locked"])?@$post["is_locked"]:0;
            $data["project_code"] = @$post["project_code"];
            if(!empty($post["custom_order_number"])) $data['custom_order_number'] = @$post['custom_order_number'];
            if(empty($post["bill_to"]) && !empty($data["client_id"])) {
                $billto = $this->common_model->loadBillToData($data["client_id"]);
                $data["bill_to"] = !empty($billto['value'])?$billto['value']:'';
            } else  $data["bill_to"] = @$post["bill_to"];

            if(!empty($post["order_by"])) $data["order_by"] = @$post["order_by"];


            $data['order_number'] = $this->common_model->CommonfindMaxFieldValue('work_orders', "order_number");
            if (empty($data['order_number'])) {
                $data['order_number'] = 1001;
            } else {
                $data['order_number'] += 1;
            }


            if(!empty($post["balance_due"])) $data["balance_due"] = @$post["balance_due"];
            $data["status"] = !empty($post["status"])?@$post["status"]:0;
            if(!empty($post["total"])) $data["total"] = @$post["total"];
            if(!empty($post["total_paid"])) $data["total_paid"] = @$post["total_paid"];
            if(!empty($post["paid_date"])) $data["paid_date"] = strtotime(@$post["paid_date"]);
            if(!empty($post["total_tax"])) $data["total_tax"] = @$post["total_tax"];
            if(!empty($post["total_due_with_tax"])) $data["total_due_with_tax"] = @$post["total_due_with_tax"];
            if(!empty($post["invoice_tax_rate"])) $data["invoice_tax_rate"] = @$post["invoice_tax_rate"];
            if(!empty($post["tax_rate"])) $data["tax_rate"] = @$post["tax_rate"];
            if(!empty($post["tax_code"])) $data["tax_code"] = @$post["tax_code"];
            if(!empty($post["total_qty"])) $data["total_qty"] = @$post["total_qty"];
            if(!empty($post["subtotal"])) $data["subtotal"] = @$post["subtotal"];
            if(!empty($post["discount"])) $data["discount"] = @$post["discount"];
            if(!empty($post["total_due"])) $data["total_due"] = @$post["total_due"];
            if(!empty($post["template"])) $data["template"] = @$post["template"];
            if(!empty($post["customer_ref_po"])) $data["customer_ref_po"] = @$post["customer_ref_po"];
            if(!empty($post["currency_code"])) $data["currency_code"] = @$post["currency_code"];
            if(!empty($post["base_currency_code"])) $data["base_currency_code"] = @$post["base_currency_code"];
            if(!empty($post["exchange_rate"])) $data["exchange_rate"] = @$post["exchange_rate"];
            if(!empty($post["comments"])) $data["comments"] = @$post["comments"];
            
            //$data['id']= @$post["id"];
            $response = $this->common_model->CommonInsertOrUpdate('work_orders','', $data);

            if(!empty($post["amount"])){
                foreach( $post["amount"] as $key => $amount){
                    $c_data = [];
                    $c_data['amount'] = $amount>0?$amount:0.00;
                    $c_data['qty'] = !empty($post['qty'][$key])?$post['qty'][$key]:0;
                    $c_data['discount'] = !empty($post['discount'][$key])?$post['discount'][$key]:0.00;
                    $c_data['description'] = @$post['description'][$key];
                    $c_data['rate'] = !empty($post['rate'][$key])?$post['rate'][$key]:0.00;
                    $c_data['work_orders_uuid'] = @$data['uuid'];
                    $c_data['uuid'] = UUID::v5(UUID::v4(), 'work_order_items');
                    $c_data['uuid_business_id'] = @$post['uuid_business_id'];
                    //print_r($c_data); die;

                    $this->common_model->CommonInsertOrUpdate("work_order_items",'',$c_data);
                }
            }          


            $response_data['data'] = $data;
            $response_data['status'] = 'success';
            echo json_encode($response_data); die;

        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'client_id and uuid_business_id cannot be empty!!';
            echo json_encode($response_data); die;         
        }

    }

    public function updateWorkOrder()
    {
        if(!empty($this->request->getPost('uuid')) && !empty($this->request->getPost('uuid_business_id'))){            
                
            $post = $this->request->getPost(); 
            if(!empty($post["uuid_business_id"])) $data["uuid_business_id"] = @$post['uuid_business_id'];            
            if(!empty($post["date"])) $data["date"] = strtotime(@$post["date"]);
            if(!empty($post["client_id"])) $data["client_id"] = @$post["client_id"];
            if(!empty($post["is_locked"])) $data["is_locked"] = !empty($post["is_locked"])?@$post["is_locked"]:0;
            if(!empty($post["project_code"])) $data["project_code"] = @$post["project_code"];
            if(!empty($post["custom_order_number"])) $data['custom_order_number'] = @$post['custom_order_number'];
            if(!empty($post["bill_to"])) $data["bill_to"] = @$post["bill_to"];
            if(!empty($post["order_by"])) $data["order_by"] = @$post["order_by"];


            $data['order_number'] = $this->common_model->CommonfindMaxFieldValue('work_orders', "order_number");
            if (empty($data['order_number'])) {
                $data['order_number'] = 1001;
            } else {
                $data['order_number'] += 1;
            }


            if(!empty($post["balance_due"])) $data["balance_due"] = @$post["balance_due"];
            if(!empty($post["status"])) $data["status"] = @$post["status"];
            if(!empty($post["total"])) $data["total"] = @$post["total"];
            if(!empty($post["total_paid"])) $data["total_paid"] = @$post["total_paid"];
            if(!empty($post["paid_date"])) $data["paid_date"] = strtotime(@$post["paid_date"]);
            if(!empty($post["total_tax"])) $data["total_tax"] = @$post["total_tax"];
            if(!empty($post["total_due_with_tax"])) $data["total_due_with_tax"] = @$post["total_due_with_tax"];
            if(!empty($post["invoice_tax_rate"])) $data["invoice_tax_rate"] = @$post["invoice_tax_rate"];
            if(!empty($post["tax_rate"])) $data["tax_rate"] = @$post["tax_rate"];
            if(!empty($post["tax_code"])) $data["tax_code"] = @$post["tax_code"];
            if(!empty($post["total_qty"])) $data["total_qty"] = @$post["total_qty"];
            if(!empty($post["subtotal"])) $data["subtotal"] = @$post["subtotal"];
            if(!empty($post["discount"])) $data["discount"] = @$post["discount"];
            if(!empty($post["total_due"])) $data["total_due"] = @$post["total_due"];
            if(!empty($post["template"])) $data["template"] = @$post["template"];
            if(!empty($post["customer_ref_po"])) $data["customer_ref_po"] = @$post["customer_ref_po"];
            if(!empty($post["currency_code"])) $data["currency_code"] = @$post["currency_code"];
            if(!empty($post["base_currency_code"])) $data["base_currency_code"] = @$post["base_currency_code"];
            if(!empty($post["exchange_rate"])) $data["exchange_rate"] = @$post["exchange_rate"];
            if(!empty($post["comments"])) $data["comments"] = @$post["comments"];
            
            //$data['id']= @$post["id"];
            $response = $this->common_model->CommonInsertOrUpdate('work_orders',$this->request->getPost('uuid'), $data);

            if(!empty($post["amount"])){
                foreach( $post["amount"] as $key => $amount){
                    $c_data = [];
                    $c_data['amount'] = $amount>0?$amount:0.00;
                    $c_data['qty'] = !empty($post['qty'][$key])?$post['qty'][$key]:0;
                    $c_data['discount'] = !empty($post['discount'][$key])?$post['discount'][$key]:0.00;
                    $c_data['description'] = @$post['description'][$key];
                    $c_data['rate'] = !empty($post['rate'][$key])?$post['rate'][$key]:0.00;
                    $c_data['work_orders_uuid'] = @$this->request->getPost('uuid');
                    $c_data['uuid_business_id'] = @$post['uuid_business_id'];
                    //print_r($c_data); die;
                    if(!empty($post['item_uuid'][$key])){
                        $this->common_model->CommonInsertOrUpdate("work_order_items",$post['item_uuid'][$key],$c_data);
                    }else{
                        $c_data['uuid'] = UUID::v5(UUID::v4(), 'work_order_items');
                        $this->common_model->CommonInsertOrUpdate("work_order_items",'',$c_data);
                    }

                }
            }          


            $response_data['data'] = $data;
            $response_data['status'] = 'success';
            echo json_encode($response_data); die;

        }else{
            $response_data['status'] = 'error';
            $response_data['msg']    = 'uuid, uuid_business_id cannot be empty!!';
            echo json_encode($response_data); die;         
        }

    }

    public function businesses($id = false)
    {   
        $data['data'] = $this->common_model->getCommonData("businesses");
        $data['status'] = 'success';
        echo json_encode($data); die;
    }

    public function addBusiness()
    { 
        if(!empty($this->request->getPost('business_code')) && !empty($this->request->getPost('name'))){		

            $uuidNamespace = UUID::v4();
            $uuid = UUID::v5($uuidNamespace, 'businesses');
            $data_array = array(
                'name'  => $this->request->getPost('name'),
                'business_code'  => $this->request->getPost('business_code'),
                'language_code' => !empty($this->request->getPost('language_code'))?$this->request->getPost('language_code'):'en',
                'uuid' => $uuid
            );
            $data_array["uuid_business_id"] = !empty($post["uuid_business_id"])?@$post["uuid_business_id"]:$uuid;
            if(!empty($post["email"])) $data_array["email"] = @$post["email"];
            if(!empty($post["company_address"])) $data_array["company_address"] = @$post["company_address"];
            if(!empty($post["company_number"])) $data_array["company_number"] = @$post["company_number"];
            if(!empty($post["vat_number"])) $data_array["vat_number"] = @$post["vat_number"];
            if(!empty($post["no_of_shares"])) $data_array["no_of_shares"] = @$post["no_of_shares"];
            if(!empty($post["web_site"])) $data_array["web_site"] = @$post["web_site"];
            if(!empty($post["payment_page_url"])) $data_array["payment_page_url"] = @$post["payment_page_url"];
            if(!empty($post["country_code"])) $data_array["country_code"] = @$post["country_code"];
            if(!empty($post["telephone_no"])) $data_array["telephone_no"] = @$post["telephone_no"];
            if(!empty($post["trading_as"])) $data_array["trading_as"] = @$post["trading_as"];
            if(!empty($post["business_contacts"])) $data_array["business_contacts"] = json_encode(@$post["business_contacts"]);
            if(!empty($post["default_business"])) $data_array["default_business"] = @$post["default_business"];
            //echo json_encode($data_array); die;
            $this->common_model->CommonInsertOrUpdate('businesses','',$data_array);
            $data['data'] = $data_array;
            $data['status'] = 'success';
            echo json_encode($data); die;           

        }else {
            $data['status'] = 'error';
            $data['msg']    = 'name, business_code could not be empty!!';
            echo json_encode($data); die;  
        }
    }

    public function updateBusiness()
    { 
        if(!empty($this->request->getPost('uuid'))){		

            $uuidNamespace = UUID::v4();
            $uuid = UUID::v5($uuidNamespace, 'businesses');
            $data_array = array();
            if(!empty($post["name"])) $data_array["name"] = @$post["name"];
            if(!empty($post["language_code"])) $data_array["language_code"] = @$post["language_code"];
            if(!empty($post["uuid_business_id"])) $data_array["uuid_business_id"] = @$post["uuid_business_id"];
            if(!empty($post["email"])) $data_array["email"] = @$post["email"];
            if(!empty($post["company_address"])) $data_array["company_address"] = @$post["company_address"];
            if(!empty($post["company_number"])) $data_array["company_number"] = @$post["company_number"];
            if(!empty($post["vat_number"])) $data_array["vat_number"] = @$post["vat_number"];
            if(!empty($post["no_of_shares"])) $data_array["no_of_shares"] = @$post["no_of_shares"];
            if(!empty($post["web_site"])) $data_array["web_site"] = @$post["web_site"];
            if(!empty($post["payment_page_url"])) $data_array["payment_page_url"] = @$post["payment_page_url"];
            if(!empty($post["country_code"])) $data_array["country_code"] = @$post["country_code"];
            if(!empty($post["telephone_no"])) $data_array["telephone_no"] = @$post["telephone_no"];
            if(!empty($post["trading_as"])) $data_array["trading_as"] = @$post["trading_as"];
            if(!empty($post["business_contacts"])) $data_array["business_contacts"] = json_encode(@$post["business_contacts"]);
            if(!empty($post["default_business"])) $data_array["default_business"] = @$post["default_business"];
            //echo json_encode($data_array); die;
            $this->common_model->CommonInsertOrUpdate('businesses',$this->request->getPost('uuid'), $data_array);
            $data['data'] = $data_array;
            $data['status'] = 'success';
            echo json_encode($data); die;           

        }else {
            $data['status'] = 'error';
            $data['msg']    = 'uuid could not be empty!!';
            echo json_encode($data); die;  
        }
    }

}
