<?php namespace App\Controllers;
 
use CodeIgniter\Controller;
use App\Models\Service_model;
use App\Models\Users_model;
use App\Models\Tenant_model;
use App\Models\Cat_model;
use App\Models\Secret_model;
use App\Models\Template_model;
use App\Models\Meta_model;
use App\Models\Amazon_s3_model;

class Services extends Api
{	
	public function __construct()
	{
		parent::__construct(); 
		$this->session = \Config\Services::session();
		$this->serviceModel = new Service_model();
		$this->user_model = new Users_model();
		$this->tmodel = new Tenant_model();
		$this->cmodel = new Cat_model();
		$this->secret_model = new Secret_model();
		$this->template_model = new Template_model();
		$this->meta_model = new Meta_model();
		$this->Amazon_s3_model = new Amazon_s3_model();
	}
    public function index()
    {        
        $data['services'] = $this->serviceModel->getRows();
		$data['tableName'] = "services";
        $data['rawTblName'] = "service";
		echo view('services/list',$data);
    }
	 

	public function edit($id=0)
    {        
		$data['tableName'] = "services";
        $data['rawTblName'] = "service";
        $data['service'] = $this->serviceModel->getRows($id)->getRow();
		$data['tenants'] = $this->tmodel->getRows();
		$data['category'] = $this->cmodel->getRows();
		$data['users'] = $this->user_model->getUser();
		$data['secret_services'] = $this->secret_model->getSecrets($id);
        
		
        echo view('services/edit', $data);
    }
	

    public function update()
    {
        $id = $this->request->getPost('id');

	
        $data = array(
			'name'  => $this->request->getPost('name'),
			'code' => $this->request->getPost('code'),				
			'notes' => $this->request->getPost('notes'),	
			'uuid' => $this->request->getPost('uuid'),
			//'nginx_config' => $this->request->getPost('nginx_config'),
			//'varnish_config' => $this->request->getPost('varnish_config'),
			'cid' => $this->request->getPost('cid'),
			'tid' => $this->request->getPost('tid'),
		);
		
		if($_FILES['file']['tmp_name']) {		
			//echo '<pre>';print_r($_FILES['file']); die;	
			$response = $this->Amazon_s3_model->doUpload("file", "service-logo");													
			$data['image_logo'] = $response['filePath'];
		 }
		 
		 if($_FILES['file2']['tmp_name']) {		
			//echo '<pre>';print_r($_FILES['file']); die;		
			$response = $this->Amazon_s3_model->doUpload("file2", "service-brand");															
			$data['image_brand'] =  $response['filePath'];
		 }
		 
        $id = $this->serviceModel->insertOrUpdate("services", $id,$data);
		
		$this->secret_model->deleteServiceFromServiceID($id);
		
		$key_name = $this->request->getPost('key_name');
		$key_value = $this->request->getPost('key_value');
		
		foreach ($key_name as $key => $value) {
			//$address_data['service_id'] = $id;
			$address_data['key_name'] = $key_name[$key];
			$address_data['key_value'] = $key_value[$key];
			$address_data['status'] = 1;

		
			$secret_id = $this->secret_model->saveOrUpdateData($id , $address_data);

			if($secret_id > 0){
				$dataRelated['secret_id'] = $secret_id;
				$dataRelated['service_id'] = $id;
				$this->secret_model->saveSecretRelatedData($dataRelated);
			}
	
		}

		
        return redirect()->to('/services');
    }
	

	
	
	public function deploy_service($uuid=0)
    {
		if(!empty($uuid)) {

			$this->export_service_json($uuid);
			$this->gen_service_env_file($uuid);
			$this->push_service_env_vars($uuid);
			$this->gen_service_yaml_file($uuid);
						
			//exec('/bin/bash /var/www/html/writable/tizohub_deploy_service.sh', $output, $return);
			$output = shell_exec('/bin/bash /var/www/html/writable/tizohub_deploy_service.sh');
			//echo $output;
			echo "Service deployment process started OK.";
			
		} else { echo "Uuid is empty!!"; }
		
    }

	public function delete_service($uuid=0)
    {
		if(!empty($uuid)) {

			$this->export_service_json($uuid);
			$this->gen_service_env_file($uuid);
			$this->push_service_env_vars($uuid);
			$this->gen_service_yaml_file($uuid);
						
			//exec('/bin/bash /var/www/html/writable/tizohub_deploy_service.sh', $output, $return);
			$output = shell_exec('/bin/bash /var/www/html/writable/tizohub_delete_service.sh');
			//echo $output;
			echo "Service deletion process started OK.";
			
		} else { echo "Uuid is empty!!"; }
		
    }
	
	public function export_service_json($uuid) 
	{
		//export service json same format as provided by the api
		// url/api/service/uuid.json -> json
		// write json to to file	
		
		$myfile = fopen(WRITEPATH . "tizohub_deployments/service-".$uuid.".json", "w") or die("Unable to open file!");
		
		fwrite($myfile, $this->services($uuid,true));
		fclose($myfile);
	}
	
	public function push_service_env_vars($uuid) 
	{
		// loop through all secrets of this service 
		//putenv("SERVICE_UUID", $id);
		putenv("SERVICE_ID=".$uuid);
		$secrets = $this->secret_model->getRows();
		if(!empty($secrets)){
				foreach($secrets as $key=>$val){
					if ($val['key_name'] == 'KUBECONFIG') {
						$myfile = fopen(WRITEPATH . "kube_config_auth", "w") or die("Unable to open file!");
						fwrite($myfile, $val['key_value']);
						fclose($myfile);
					}
					
					if ($val['key_name'] == 'KUBENETES_CLUSTER_NAME' || $val['key_name'] == 'AWS_ACCESS_KEY_ID' || $val['key_name'] == 'AWS_SECRET_ACCESS_KEY' || $val['key_name'] == 'AWS_DEFAULT_REGION') {
					putenv($val['key_name']."=".$val['key_value']);
					}
			}
		}

		$secrets = $this->secret_model->getSecrets($uuid);
		if(!empty($secrets)){
			foreach($secrets as $key=>$val){
				putenv($val['key_name']."=".$val['key_value']);
			}
		}
		
	}
	

public function gen_service_env_file($uuid)
{

	$service_data = file_get_contents(WRITEPATH. 'tizohub.env.template');
	$secrets = $this->secret_model->getSecrets($uuid);
	if(!empty($secrets)){
		foreach($secrets as $key=>$val){
			$pattern = "/{{".$val['key_name']."}}/i";
			$service_data = preg_replace($pattern, $val['key_value'], $service_data);
	
		}
	}

	$myfile = fopen(WRITEPATH . "tizohub_deployments/service-".$uuid.".env", "w") or die("Unable to open file!");
	fwrite($myfile, $service_data);
	fclose($myfile);

	//create php seed
	// $myfile = fopen(WRITEPATH . "tizohub_deployments/service-".$uuid.".php", "w") or die("Unable to open file!");
	// fwrite($myfile, $service_data);
	// fclose($myfile);

}

public function gen_service_yaml_file($uuid)
{

	$service_data = file_get_contents(WRITEPATH. 'tizohub.yaml.template');
	$secrets = $this->secret_model->getSecrets($uuid);
	if(!empty($secrets)){
		foreach($secrets as $key=>$val){
			$pattern = "/{{".$val['key_name']."}}/i";
			$service_data = preg_replace($pattern, $val['key_value'], $service_data);
	
		}
	}

	$myfile = fopen(WRITEPATH."tizohub_deployments/service-".$uuid.".yaml", "w") or die("Unable to open file!");
	fwrite($myfile, $service_data);
	fclose($myfile);

}

public function delete($id)
{       
	//echo $id; die;
	if(!empty($id)) {
		$response = $this->serviceModel->deleteData($id);		
		if($response){
			session()->setFlashdata('message', 'Data deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}else{
			session()->setFlashdata('message', 'Something wrong delete failed!');
			session()->setFlashdata('alert-class', 'alert-danger');		
		}

	}
	
	return redirect()->to('/services');
}


}