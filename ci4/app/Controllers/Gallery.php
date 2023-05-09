<?php 
namespace App\Controllers;

use App\Models\Gallery_model;
use App\Models\Users_model;
use App\Controllers\Core\CommonController; 
ini_set('display_errors', 1);

class Gallery extends CommonController
{	
	public function __construct()
	{
		parent::__construct();
		$this->gallery_model = new Gallery_model();
		$this->user_model = new Users_model();
		$this->table = 'media_list';
		$this->gallery = 'gallery';
		$menucode = $this->getMenuCode("/gallery");
		$this->session->set("menucode", $menucode);
	}

	public function index()
	{        

		$data[$this->table] = $this->gallery_model->where([ "uuid_business_id" => $this->businessUuid])->findAll();
		$data['tableName'] = $this->gallery;
		$data['rawTblName'] = "Galary";
		$data['is_add_permission'] = 1;

		echo view($this->table."/list",$data);
	}

	public function getMenuCode($value)
{
	$result = $this->db->table("menu")->getWhere([
		"link" => $value
	])->getRowArray();

	return @$result['id'];
}

	public function edit($id = 0)
	{
		$data['rawTblName'] = "Galary";
		$data['tableName'] = $this->gallery;
		$data[$this->table] = $this->gallery_model->getRows($id)->getRow();
		$data['users'] = $this->user_model->getUser();
		echo view($this->table.'/edit',$data);
	}

	
	public function update()
	{     
		$data = array(
			'code' => $this->request->getPost('code'),
			'status' => $this->request->getPost('status'),
			"uuid_business_id" => $this->businessUuid,
		);

		$id = $this->request->getPost('id');
		$file = $this->request->getPost('file');
		if($file && !empty($file) && strlen($file) > 0){
			$data['name'] = $file;
		}
		if( $id > 0 ){
			
			$this->gallery_model->updateData($id, $data);
			
			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');

		}else {


			if(!empty($file) && strlen($file) > 0) {
				
				session()->setFlashdata('message', 'Data entered Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');

				$this->gallery_model->saveData($data);

			}else{

				session()->setFlashdata('message', 'File not uploaded.');
				session()->setFlashdata('alert-class', 'alert-danger');
			}			   
		}
		return redirect()->to('/'.$this->gallery);
	}
	
	
	public function delete($id)
	{       
		
		if(!empty($id)) {
			$this->gallery_model->deleteData($id);		
			session()->setFlashdata('message', 'Data deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}
		
		return redirect()->to('/'.$this->gallery);
	}
	public function delete_task($id,$url)
	{       
		
		if(!empty($id)) {
			$this->gallery_model->deleteData($id);		
			session()->setFlashdata('message', 'Data deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}
		
		return redirect()->to(base64_decode($url));
	}
	
	
}