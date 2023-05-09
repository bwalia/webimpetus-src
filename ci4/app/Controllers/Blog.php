<?php 
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Content_model;
use App\Models\Users_model;
use App\Models\Cat_model;
use App\Controllers\Core\CommonController; 
ini_set('display_errors', 1);
class Blog extends CommonController
{	
	public function __construct()
	{
		parent::__construct();
		$this->content_model = new Content_model();		
		$this->user_model = new Users_model();
		$this->cat_model = new Cat_model();
	}
	public function index()
	{        
		$data['content'] = $this->content_model->where(['type' => 2, "uuid_business_id" => $this->businessUuid])->findAll();
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['menucode'] = 8;
		echo view($this->table."/list", $data);
	}
	
	public function edit($id = 0)
	{
		$data['menucode'] = 8;
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['content'] = $this->content_model->getRows($id)->getRow();
		$data['users'] = $this->user_model->getUser();
		$data['cats'] = $this->cat_model->getRows();

		$data['images'] = [];
		if( $id> 0 ){

			$data['images'] = $this->model->getDataWhere("blog_images", $id, "blog_id");
		}
		
		$array1 = $this->cat_model->getCatIds($id);
		
		$arr = array_map (function($value){
			return $value['categoryid'];
		} , $array1);
		$data['selected_cats'] = $arr;
		
		echo view($this->table."/edit", $data);
	}
	
	public function rmimg($id, $blogId)
	{
		if(!empty($id)){

			$this->model->deleteTableData("blog_images", $id);
			session()->setFlashdata('message', 'Image deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
			
		}
		return redirect()->to('//blog/edit/'.$blogId);
		
	}
	
	public function update()
	{     
		
		$id = $this->request->getPost('id');

		$data = array(
			'title'  => $this->request->getPost('title'),				
			'sub_title' => $this->request->getPost('sub_title'),
			'content' => $this->request->getPost('content'),
			'code' => $this->request->getPost('code')?$this->content_model->format_uri($this->request->getPost('code'),'-',@$id):$this->content_model->format_uri($this->request->getPost('title'),'-',@$id),
			'meta_keywords' => $this->request->getPost('meta_keywords'),
			'meta_title' => $this->request->getPost('meta_title'),
			'meta_description' => $this->request->getPost('meta_description'),
			'status' => $this->request->getPost('status'),
			'publish_date' => ($this->request->getPost('publish_date')?strtotime($this->request->getPost('publish_date')):strtotime(date('Y-m-d H:i:s'))),
			'type' => ($this->request->getPost('type')?$this->request->getPost('type'):1),
					//'image_logo' => $filepath
		);
		if(!empty($this->request->getPost('uuid'))){
			$data['uuid'] = $this->request->getPost('uuid');
		}
		
		$files = $this->request->getPost("file");

		
		if(!empty($id)){

			$row = $this->content_model->getRows($id)->getRow();

			if(is_array($files)){
				foreach(@$files as $key => $filePath) {	

					$blog_images = [];
					$blog_images['uuid_business_id'] =  session('uuid_business');
					$blog_images['image'] = $filePath;				
					$blog_images['blog_id'] = $id;

					$this->content_model->saveDataInTable($blog_images, "blog_images"); 						
				}
			}
			

			$this->content_model->updateData($id, $data);
			
			if(!empty($id) && !empty($this->request->getPost('catid'))){
				$this->cat_model->deleteCatData($id);		
				foreach($this->request->getPost('catid') as $val) {
					$cat_data = [];
					$cat_data['categoryid'] = $val;
					$cat_data['contentid'] = $id;
					$cat_data['uuid_business_id'] =  session('uuid_business');

					$this->cat_model->saveData2($cat_data);					
				}			
				
			}
			
			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}else {

			if(!empty($this->request->getPost('title'))){
				
				if($this->request->getPost('title')) {
					
					// Set Session
					session()->setFlashdata('message', 'Data entered Successfully!');
					session()->setFlashdata('alert-class', 'alert-success');
					
					
					$bid = $this->content_model->saveData($data); 
					
					if($bid){
						if(is_array($files)){
					
							foreach($files as $key => $filePath) {	

								$blog_images['uuid_business_id'] =  session('uuid_business');
								$blog_images['image'] = $filePath;				
								$blog_images['blog_id'] = $id;
				
								$this->content_model->saveDataInTable($blog_images, "blog_images"); 						
							}
						}
							
					}
					if(!empty($bid) && !empty($this->request->getPost('catid'))){
						
						foreach($this->request->getPost('catid') as $val) {
							$cat_data = [];
							$cat_data['categoryid'] = $val;
							$cat_data['contentid'] = $bid;
							$cat_data['uuid_business_id'] =  session('uuid_business');
							$this->cat_model->saveData2($cat_data);
							
						}
					}
					
				}else{
					   // Set Session
					session()->setFlashdata('message', 'File not uploaded.');
					session()->setFlashdata('alert-class', 'alert-danger');
				}
			}
		}
		return redirect()->to('/'.$this->table);
	}

	public function delete($id, $redirect='')
    {       
		
        if(!empty($id)) {
			
			$response = $this->content_model->deleteData($id);
			
			if($response){
				session()->setFlashdata('message', 'Data deleted Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
			}else{
				session()->setFlashdata('message', 'Something wrong delete failed!');
				session()->setFlashdata('alert-class', 'alert-danger');		
			}

		}
		
        return redirect()->to('/'.$this->table);
    }
	

}