<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController; 
use App\Models\Documents_model;
use App\Libraries\UUID;
class Documents extends CommonController
{	
	public $documents_model;
    function __construct()
    {
        parent::__construct();

        $this->documents_model = new Documents_model();
        

	}
    
    public function index()
    {        
        $curretntBusiness = $this->model->getExistsTableRowsByUUID("businesses", session('uuid_business'));
        $frontDomain = base_url();
        if (!empty($curretntBusiness) && isset($curretntBusiness['frontend_domain'])) {
            $frontDomain = $curretntBusiness['frontend_domain'];
        }
        $data[$this->table] = $this->documents_model->getList();
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;
        $data['front_domain'] = $frontDomain;

        echo view($this->table."/list",$data);
    }
    public function edit($id = 0)
    {
		$data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
		$data["users"] = $this->model->getUser();
		$data[$this->rawTblName] = $this->model->getRows($id)->getRow();
		// if there any special cause we can overried this function and pass data to add or edit view
		$data['additional_data'] = $this->getAdditionalData($id);

        echo view($this->table."/edit",$data);
    }
    public function update()
    {        
        $id = $this->request->getPost('id');

		$data = $this->request->getPost();

        $data['uuid'] = UUID::v5(UUID::v4(), 'contacts_saving');

        $data['document_date'] = strtotime($data['document_date']);

        if( isset($_FILES['file']['tmp_name']) && strlen($_FILES['file']['tmp_name']) > 0) {	

            $response = $this->Amazon_s3_model->doUpload("file", "category-file");						
            $data['file'] = $response["filePath"];
        }
        
		$response = $this->model->insertOrUpdate($id, $data);
		if(!$response){
			session()->setFlashdata('message', 'Something wrong!');
			session()->setFlashdata('alert-class', 'alert-danger');	
		}

        return redirect()->to('/'.$this->table);
    }

    public function getfile()
    {
        $rowId = $this->request->getPost('rowid');
        $data = $this->db->table($this->table)->select('file')->getWhere(array('id' => $rowId))->getRowArray();
        echo json_encode(array('file' => @$data['file']));
    }

    public function delete_task($id,$url)
	{       
		
		if(!empty($id)) {
			$this->db->table($this->table)->where(array('id' => $id))->delete();		
			session()->setFlashdata('message', 'Data deleted Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}
		
		return redirect()->to(base64_decode($url));
	}
}