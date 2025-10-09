<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;

use App\Libraries\UUID;

use App\Models\Tasks_model;
use App\Models\Users_model;
use App\Models\Email_model;
use App\Models\Sprints_model;
use App\Models\Customers_model;
use CodeIgniter\API\ResponseTrait;

class Tasks extends CommonController
{
    use ResponseTrait;

    public $Tasks_model;
    public $Users_model;
    public $Email_model;
    public $sprintModel;
    protected $customers_model;

    function __construct()
    {
        parent::__construct();

        $this->Tasks_model = new Tasks_model();
        $this->Users_model = new Users_model();
        $this->Email_model = new Email_model();
        $this->sprintModel = new Sprints_model();
        $this->customers_model = new Customers_model();
    }

    public function index()
    {
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;

        $taskStatusList = $this->Tasks_model->allTaskStatus();
        $keyword = $this->request->getVar('query');
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 10;

        $blank_item = array("key" => "", "value" => "--Choose Status--");
        array_unshift($taskStatusList, $blank_item);
        $backlog_item = array("key" => "backlog", "value" => "Backlog");
        array_push($taskStatusList, $backlog_item);

        $data['taskStatusList'] = $taskStatusList;
        $status = $_GET['status'] ?? "";

        $condition = array();
        if ($status === "") {
        } elseif ($status === "backlog") {
            $current_sprint = $this->sprintModel->getCurrentSprint();
            $next_sprint = $this->sprintModel->getNextSprint($current_sprint);

            $sprintCondition = $current_sprint > 0 ? "sprint_id < $current_sprint AND" : "sprint_id < $next_sprint AND";
            $condition = "(sprint_id = null OR (" . $sprintCondition . " tasks.status != 'done'))";
        } else {
            $condition = [$this->table . ".status" => $status];
        }
        $taskListData = $this->Tasks_model->getTaskList($condition, $page, $perPage, $keyword);
        
        $data[$this->table] = $taskListData['data'];
        $pager = \Config\Services::pager();
        $data['is_add_permission'] = 1;
        $data['pager'] = $pager->makeLinks($page, $perPage, $taskListData['count']);
        $data['count'] = $taskListData['count'];
        
        echo view($this->table . "/list", $data);
    }

    public function tasksList()
	{
		$limit = (int)$this->request->getVar('limit');
		$offset = (int)$this->request->getVar('offset');
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "name";
		$dir = $this->request->getVar('dir') ?? "asc";

        $taskStatusList = $this->Tasks_model->allTaskStatus();

        $blank_item = array("key" => "", "value" => "--Choose Status--");
        array_unshift($taskStatusList, $blank_item);
        $backlog_item = array("key" => "backlog", "value" => "Backlog");
        array_push($taskStatusList, $backlog_item);

        $data['taskStatusList'] = $taskStatusList;
        $status = $_GET['status'] ?? "";

        $condition = array();
        if ($status === "") {
        } elseif ($status === "backlog") {
            $current_sprint = $this->sprintModel->getCurrentSprint();
            $next_sprint = $this->sprintModel->getNextSprint($current_sprint);

            $sprintCondition = $current_sprint > 0 ? "sprint_id < $current_sprint AND" : "sprint_id < $next_sprint AND";
            $condition = "(sprint_id = null OR (" . $sprintCondition . " tasks.status != 'done'))";
        } else {
            $condition = [$this->table . ".status" => $status];
        }
        $taskListData = $this->Tasks_model->getTaskRows($condition, $limit, $offset, $order, $dir, $query);

		$data = [
			'rawTblName' => $this->rawTblName,
			'tableName' => $this->table,
			'data' => $taskListData['data'],
            'is_add_permission' => 1,
			'recordsTotal' => $taskListData['count'],
		];
		return $this->response->setJSON($data);
	}

    public function clone($uuid = null)
    {
        $data = $this->model->getRowsByUUID($uuid)->getRowArray();
        unset($data['id'], $data['created_at']);
        $data['start_date'] = strtotime(date("Y-m-d", strtotime("+ 1 day")));
        $data['end_date'] = strtotime(date("Y-m-d", strtotime("+ 1 day")));

        $data['task_id'] = findMaxFieldValue($this->table, "task_id");

        if (empty($data['task_id'])) {
            $data['task_id'] = 1001;
        } else {
            $data['task_id'] += 1;
        }

        $data['uuid'] = UUID::v5(UUID::v4(), 'tasks');
        $this->model->insertTableData($data, $this->table);

        session()->setFlashdata('message', 'Data cloned Successfully!');
        session()->setFlashdata('alert-class', 'alert-success');

        return redirect()->to($this->table . "/editrow/" . $data['uuid']);
    }


    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $data = $this->request->getPost();
        $data['start_date'] = strtotime($data['start_date']);
        $data['end_date'] = strtotime($data['end_date']);

        if (empty($uuid)) {
            $data['task_id'] = findMaxFieldValue($this->table, "task_id");
            $data['uuid'] = UUID::v5(UUID::v4(), 'tasks');
            if (empty($data['task_id'])) {
                $data['task_id'] = 1001;
            } else {
                $data['task_id'] += 1;
            }
        }

        $file = $this->request->getPost('file');
		if($file && !empty($file) && strlen($file) > 0){
			

            $tokens = explode('.', $file);
            $extension = $tokens[count($tokens)-1]; 
            $varray = ['doc', 'docx', 'xls', 'xlsx', 'pdf', 'ppt'];

            //print_r($varray);

                        
            if(in_array(trim($extension),$varray)){ 
                $file_array['file'] = $file;
                $file_array['uuid_linked_table'] = $uuid;
                $file_array['uuid_business_id'] = session('uuid_business');
                $this->model->insertTableData($file_array, 'documents');
            }else {
                $file_array['name'] = $file;
                $file_array['uuid_linked_table'] = $uuid;
                $file_array['uuid_business_id'] = session('uuid_business');
                $this->model->insertTableData($file_array, 'media_list');
            }
		}

        $response = $this->model->insertOrUpdateByUUID($uuid, $data);
        if (!$response) {
            session()->setFlashdata('message', 'Something wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        // Send an email when assign task to user
        $user = $this->Users_model->getUser($data['assigned_to'])->getRow();
        if (isset($user->email) && !empty($user->email)) {
            $from_email = "info@odincm.com";
            $from_name = "Web Impetus";
            $message = "<p><b>Hi " . $user->name . ",</b></p>";
            $message .= "<p>A task has been assigned to you on Webimpetus. Please login to system for more details.</p>";
            $message .= "<p><b>Thanks, Webimpetus Team</b></p>";
            $subject = "Webimpetus Task Update";
            $is_send = $this->Email_model->phpmailer_send_mail($user->email, $from_name, $from_email, $message, $subject);
        }

        return redirect()->to('/' . $this->table);
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

    /**
     * Kanban Board View
     */
    public function board()
    {
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;

        // Get all necessary data for filters
        $projectsBuilder = $this->db->table('projects');
        $projectsBuilder->where('uuid_business_id', session('uuid_business'));
        $projectsBuilder->orderBy('name', 'ASC');
        $data['projects'] = $projectsBuilder->get()->getResultArray();

        $sprintsBuilder = $this->db->table('sprints');
        $sprintsBuilder->where('uuid_business_id', session('uuid_business'));
        $sprintsBuilder->orderBy('sprint_name', 'ASC');
        $data['sprints'] = $sprintsBuilder->get()->getResultArray();

        $usersBuilder = $this->db->table('users');
        $usersBuilder->orderBy('name', 'ASC');
        $data['users'] = $usersBuilder->get()->getResultArray();

        echo view('tasks/board', $data);
    }

    /**
     * Get board data as JSON for kanban view
     */
    public function boardData()
    {
        $builder = $this->db->table('tasks');
        $builder->select('tasks.*, projects.name as project_name, users.name as assigned_to_name');
        $builder->join('projects', 'tasks.projects_id = projects.id', 'left');
        $builder->join('users', 'tasks.assigned_to = users.id', 'left');
        $builder->where('tasks.uuid_business_id', session('uuid_business'));
        $builder->orderBy('tasks.id', 'DESC');

        $tasks = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data' => $tasks
        ]);
    }

    /**
     * Update task status via AJAX (for drag and drop)
     */
    public function updateStatus()
    {
        $uuid = $this->request->getPost('uuid');
        $category = $this->request->getPost('category');

        if (!$uuid || !$category) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid parameters'
            ]);
        }

        $builder = $this->db->table('tasks');
        $builder->where('uuid', $uuid);
        $builder->where('uuid_business_id', session('uuid_business'));
        $builder->update(['category' => $category]);

        if ($this->db->affectedRows() > 0) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Task status updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => false,
            'message' => 'Failed to update task status'
        ]);
    }
}
