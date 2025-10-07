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

	public function index()
	{
		$keyword = $this->request->getVar('query');
        $pager = \Config\Services::pager();
        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
			'is_add_permission' => 1,
            $this->table => $this->catModel->where(["uuid_business_id" => session('uuid_business')])->search($keyword)->paginate(10),
            'pager'     => $this->catModel->pager,
        ];

		echo view($this->table . "/list", $data);
	}

	public function categoriesList()
    {
        $limit = (int)$this->request->getVar('limit');
        $offset = (int)$this->request->getVar('offset');
        $query = $this->request->getVar('query');
        $order = $this->request->getVar('order') ?? "name";
        $dir = $this->request->getVar('dir') ?? "asc";

        $sqlQuery = $this->catModel
            ->select("uuid, id, name, sort_order, notes")
            ->where(['uuid_business_id' => session('uuid_business')]);
        if ($query) {
            $sqlQuery = $sqlQuery->like("name", $query);
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
		if(isset($file) && strlen($file ?? "") > 0){
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