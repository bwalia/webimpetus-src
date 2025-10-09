<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Tasks_model;
use App\Models\Sprints_model;

class Kanban_board extends CommonController
{
    protected $timeSlipsModel;
    protected $taskModel;
    protected $sprintModel;

    public function __construct()
    {
        parent::__construct();
        $this->taskModel = new Tasks_model();
        $this->sprintModel = new Sprints_model();
    }

    /**
     * Display kanban board - redirect to modern tasks board view
     */
    public function index()
    {
        // Redirect to the new JIRA-style tasks board
        return redirect()->to('/tasks/board');
    }

    /**
     * [POST] Update task category
     */
    public function update_task()
    {
        $task_id = $this->request->getPost('task_id');
        $data_category = $this->request->getPost('data_category');
        $this->taskModel->updateData($task_id, ['category' => $data_category]);
        echo json_encode(array("status" => true, "message" => "Successfully Updated!"));
    }
}
