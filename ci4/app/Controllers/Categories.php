<?php namespace App\Controllers;

use App\Models\Cat_model;
use App\Models\Users_model;
use App\Controllers\Core\CommonController; 
use App\Models\Amazon_s3_model; 
use App\Libraries\UUID;

class Categories extends CommonController
{
	public $catModel;
	public $user_model;
	function __construct()
	{
		parent::__construct();
		$this->session = \Config\Services::session();
		$this->catModel = new Cat_model();
		$this->user_model = new Users_model();
		$this->Amazon_s3_model = new Amazon_s3_model();
		$this->rawTblName =  "category"; 
	}



	
    public function update()
    {        
        $id = $this->request->getPost('id');
		$data = array(
			'name'  => $this->request->getPost('name'),				
			'notes' => $this->request->getPost('notes'),
			'sort_order' => $this->request->getPost('sort_order'),
			'user_uuid' => $this->request->getPost('uuid'),
			'contact_uuid' => $this->request->getPost('contact_uuid'),
			'uuid_business_id' => $this->session->get('uuid_business'),
		);

		if(empty($id)){
			$data['uuid'] = UUID::v5(UUID::v4(), 'categories');
		}		

		$file = $this->request->getPost('file');
		if(isset($file) && strlen($file) > 0){
			$data['image_logo'] = $file;
		}

		$response = $this->model->insertOrUpdate($id, $data);
		if(!$response){
		session()->setFlashdata('message', 'Something wrong!');
		session()->setFlashdata('alert-class', 'alert-danger');		
		}


		return redirect()->to('/'.$this->table);

    }
	
	public function status()
    {  
		if(!empty($id = $this->request->getPost('id'))){
			$data = array(            
				'status' => $this->request->getPost('status')
			);
			$this->model->updateUser($data, $id);
		}
		echo '1';
	}
	

}