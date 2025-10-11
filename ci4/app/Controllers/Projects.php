<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController; 
use App\Models\Projects_model;
use App\Models\Tasks_model;
use App\Models\Core\Common_model;
use App\Libraries\UUID;
use App\Models\Customers_model;
use CodeIgniter\API\ResponseTrait;


class Projects extends CommonController
{	
    use ResponseTrait;

	protected $projects_model;
	protected $tasks_model;
    protected $customers_model;

    function __construct()
    {
        parent::__construct();

        $this->projects_model = new Projects_model();
        $this->tasks_model = new Tasks_model;
        $this->customers_model = new Customers_model();

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

    public function projectsList()
	{
		$limit = (int)$this->request->getVar('limit');
		$offset = (int)$this->request->getVar('offset');
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "name";
		$dir = $this->request->getVar('dir') ?? "asc";

		// Use view to get projects with tags and related data
		$sqlQuery = $this->db->table('view_projects_with_tags')
            ->select("uuid, id, name, budget, rate, currency, start_date, active, status, priority, progress, deadline_date, project_manager_name, customer_name, tag_names, tag_colors, color, actual_hours")
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

    public function edit($uuid = 0)
    {
        $projectData = $uuid ? $this->model->getRowsByUUID($uuid)->getRow() : "";
		$data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
		$data["users"] = $this->model->getUser();
		$data[$this->rawTblName] = $projectData;
		// if there any special cause we can overried this function and pass data to add or edit view
		$data['additional_data'] = $projectData ? $projectData->id : $uuid;
        
        if(!empty($data[$this->rawTblName]->customers_id)) {
            $data['customers'] = $this->customers_model
                ->where('id', $data[$this->rawTblName]->customers_id)
                ->where("uuid_business_id", session('uuid_business'))
                ->get()
                ->getResultArray();
        } else {
            $data['customers'] = $this->customers_model
                ->where("uuid_business_id", session('uuid_business'))
                ->limit(1)
                ->get()
                ->getResultArray();
        }

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

    public function companyCustomerAjax()
    {
        $q = $this->request->getVar('q');

        // Filter by current business UUID from session
        $data = $this->customers_model
            ->where('uuid_business_id', session('uuid_business'));

        // Add search filter if query provided
        if(!empty($q)) {
            $data = $data->like('company_name', $q);
        }

        // Order by company name and limit results
        $data = $data
            ->orderBy('company_name', 'ASC')
            ->limit(500)
            ->get()
            ->getResult();

        return $this->respond($data);
    }
}