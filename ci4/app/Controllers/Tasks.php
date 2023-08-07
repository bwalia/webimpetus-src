<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;

use App\Libraries\UUID;

use App\Models\Tasks_model;
use App\Models\Users_model;
use App\Models\Email_model;
use App\Models\Sprints_model;

class Tasks extends CommonController
{
    public $Tasks_model;
    public $Users_model;
    public $Email_model;
    public $sprintModel;
    function __construct()
    {
        parent::__construct();

        $this->Tasks_model = new Tasks_model();
        $this->Users_model = new Users_model();
        $this->Email_model = new Email_model();
        $this->sprintModel = new Sprints_model();
    }

    public function index()
    {
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;

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

        $data[$this->table] = $this->Tasks_model->getTaskList($condition);
        $data['is_add_permission'] = 1;

        echo view($this->table . "/list", $data);
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

        $response = $this->model->insertOrUpdate($uuid, $data);
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
}
