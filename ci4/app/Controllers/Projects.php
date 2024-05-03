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
		$limit = $this->request->getVar('limit');
		$offset = $this->request->getVar('offset');
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "name";
		$dir = $this->request->getVar('dir') ?? "asc";

		$sqlQuery = $this->projects_model
			->where(['uuid_business_id' => session('uuid_business')])
			->limit($limit, $offset)
			->orderBy($order, $dir)
			->get()
			->getResultArray();
		if ($query) {
			$sqlQuery = $this->projects_model
				->where(['uuid_business_id' => session('uuid_business')])
				->like("name", $query)
				->limit($limit, $offset)
				->orderBy($order, $dir)
				->get()
				->getResultArray();
		}

		$countQuery = $this->projects_model
			->where(["uuid_business_id" => session("uuid_business")])
			->countAllResults();
		if ($query) {
			$countQuery = $this->projects_model
				->where(["uuid_business_id" => session("uuid_business")])
				->like("name", $query)
				->countAllResults();
		}

		$data = [
			'rawTblName' => $this->rawTblName,
			'tableName' => $this->table,
			'data' => $sqlQuery,
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
        
        if(!empty($data[$this->rawTblName]->customer_id)) {
            $data['customers'] = $this->customers_model
                ->where('id', $data[$this->rawTblName]->customer_id)
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
        $data = $this->customers_model;
        if(!empty($q)) {
            $data = $data->like('company_name', $q);
        }
        
        $data = $data->limit(500)->get()->getResult();

        return $this->respond($data);
    }
}