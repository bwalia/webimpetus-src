<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController; 
use App\Models\Projects_model;
use App\Models\Tasks_model;
use App\Models\Core\Common_model;
 
class Projects extends CommonController
{	
	
    function __construct()
    {
        parent::__construct();

        $this->projects_model = new Projects_model();
        $this->tasks_model = new Tasks_model;

	}
    
    public function index()
    {        

        $data[$this->table] = $this->projects_model->getProjectList();
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;
        $data['task_progress'] = $this->tasks_model->progress();
        
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
    public function getAdditionalData($id)
    {
        $model = new Common_model();
        $data["customers"] = $model->getAllDataFromTable("customers");

        return  $data;

    }

    public function update()
    {        
        $id = $this->request->getPost('id');

		$data = $this->request->getPost();

        $data['start_date'] = strtotime($data['start_date']);
        $data['deadline_date'] = strtotime($data['deadline_date']);
        
		$response = $this->model->insertOrUpdate($id, $data);
		if(!$response){
			session()->setFlashdata('message', 'Something wrong!');
			session()->setFlashdata('alert-class', 'alert-danger');	
		}

        return redirect()->to('/'.$this->table);
    }
}