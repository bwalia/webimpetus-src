<?php namespace App\Controllers;
use App\Controllers\BaseController;
 
use CodeIgniter\Controller;
use App\Models\Secret_model;
use App\Models\Service_model;
use App\Controllers\Core\CommonController; 
use App\Models\Core\Common_model;
 
class Secrets extends CommonController
{	
	public function __construct()
	{
		parent::__construct(); 

		$this->secretModel = new Secret_model();
		$this->service_model = new Service_model();
		$this->model = new Common_model();
	}

    public function index()
    {        
		$data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['content'] = $this->secretModel->getAllSecrets();
        return view('secrets/list',$data);
    }
	

	
	public function edit($id = 0)
    {
		$data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
		$data['secret'] = $this->secretModel->getRows($id)->getRow();
		$data['services'] = $this->service_model->getRows();
		$data['sservices'] = array_column($this->secretModel->getServices($id),'service_id');

        return view('secrets/edit',$data);
    }
	
    public function update()
    {        
        $id = $this->request->getPost('id');
		
		
			$data = array(
						'key_name' => $this->request->getPost('key_name'),
						'status' => $this->request->getPost('status')?$this->request->getPost('status'):0,
					);	

			if(strpos($this->request->getPost('key_value'), '********') === false){				
				$data['key_value'] = $this->request->getPost('key_value');
			}
			$businessUuid = $this->businessUuid;
			$data['uuid_business_id'] = $businessUuid;
			$id = $this->model->insertOrUpdate($id, $data);
	
			if($id){
			
				$secret_id = $id;
				$this->secretModel->deleteService($id);
				if(!empty($this->request->getPost('sid'))){
					foreach($this->request->getPost('sid') as $val){
						$this->secretModel->serviceData(array(
							'service_id'=>$val,
							'secret_id'=>$secret_id,
							'uuid_business_id' => $businessUuid,
						));
					}
				}	
			}else{
				session()->setFlashdata('message', 'Data updated Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
			}

	
        return redirect()->to('/secrets');
    }
	

}