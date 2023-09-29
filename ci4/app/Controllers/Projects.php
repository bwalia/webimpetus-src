<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController; 
use App\Models\Projects_model;
use App\Models\Tasks_model;
use App\Models\Core\Common_model;
use App\Libraries\UUID;
 
class Projects extends CommonController
{	
	protected $projects_model;
	protected $tasks_model;
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
    public function edit($uuid = 0)
    {
        $projectData = $this->model->getRowsByUUID($uuid)->getRow();
		$data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
		$data["users"] = $this->model->getUser();
		$data[$this->rawTblName] = $projectData;
		// if there any special cause we can overried this function and pass data to add or edit view
		$data['additional_data'] = $projectData->id;

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
        $uuid = $this->request->getPost('uuid');

		$data = $this->request->getPost();

        $data['start_date'] = strtotime($data['start_date']);
        $data['deadline_date'] = strtotime($data['deadline_date']);
        if (!$uuid || empty($uuid) || !isset($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'projects');
        }
		$response = $this->model->insertOrUpdateByUUID($uuid, $data);
		if(!$response){
			session()->setFlashdata('message', 'Something wrong!');
			session()->setFlashdata('alert-class', 'alert-danger');	
		}

        return redirect()->to('/'.$this->table);
    }
}