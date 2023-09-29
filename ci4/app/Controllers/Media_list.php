<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController; 
use App\Models\Users_model;

ini_set('display_errors', 1);
 
class Media_list extends CommonController
{	
	
    function __construct()
    {
      
        parent::__construct();

        $this->table = strtolower($this->getTableNameFromUri());
		$this->rawTblName =  $this->table;

	}

    public function update()
    {        
        $id = $this->request->getPost('id');
        $uuid = $this->request->getPost('uuid');
		if(!empty($uuid)){
			$data = array(
                'code' => $this->request->getPost('code'),
                'status' => $this->request->getPost('status'),
            );
                
            if($_FILES['file']['tmp_name']) {	
                
                $imgData = $this->upload('file');
        
                $data['name'] = $imgData;
                
                }
            // if($_FILES['file']['tmp_name']) {				
            // 	$response = $this->Amazon_s3_model->doUpload("file", "category-file");							
            // 	$data['name'] = $response["filePath"];
            // }
			$this->model->updateDataByUUID($uuid, $data);
			
			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}else {
			session()->setFlashdata('message', 'Something wrong!');
			session()->setFlashdata('alert-class', 'alert-danger');				   
		}
        return redirect()->to('/'. $this->table);
    }

    public function upload($filename = null){
		//echo $filename; die;
		$input = $this->validate([
			$filename => "uploaded[$filename]|max_size[$filename,1024]|ext_in[$filename,jpg,png,jpeg,docx,pdf],"
		 ]);

		 if (!$input) { // Not valid
			return '';
		 }else{ // Valid

			 if($file = $this->request->getFile($filename)) {
				if ($file->isValid() && ! $file->hasMoved()) {
				   // Get file name and extension
				   $name = $file->getName();
				   $ext = $file->getClientExtension();

				   // Get random file name
				   $newName = $file->getRandomName(); 

				   // Store file in public/uploads/ folder
				   $file->move('../public/ckfinder/userfiles/files/', $newName);

				   // File path to display preview
				   return $filepath = base_url()."/ckfinder/userfiles/files/".$newName;
				   
				}
				
			 }
			 
		 }
		 
	}
    

}