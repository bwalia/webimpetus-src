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
     * [POST] Display kanban board based on task category
     */
    public function index()
    {

        $data['tableName'] = 'tasks';
        $data['sprints_list'] = $this->sprintModel->getSprintList();
        $sprint = $_GET['sprint'] ?? "";
        $current_sprint = $this->sprintModel->getCurrentSprint();
        $next_sprint = $this->sprintModel->getNextSprint($current_sprint);

        $categories = ['todo', 'in-progress', 'review', 'done'];

        foreach ($categories as $category) {

            if ($sprint === "backlog") {
                if ($category == "done") {
                    $data['tasks'][$category] = [];
                } else {
                    $sprintCondition = $current_sprint > 0 ? "sprint_id < $current_sprint AND" : "sprint_id < $next_sprint AND";
                    $data['tasks'][$category] = $this->taskModel->getTaskList("category = '$category' AND (sprint_id = null OR (" . $sprintCondition . " tasks.status != 'done'))")['data'];
                }
            } else {
                if ($sprint && is_numeric($sprint)) {
                    $data['tasks'][$category] = $this->taskModel->getTaskList(['category' => $category, 'sprint_id' => $sprint])['data'];
                } else {
                    $data['tasks'][$category] = $this->taskModel->getTaskList(['category' => $category])['data'];
                }
            }
        }

        return view(file_exists(APPPATH . 'Views/' . $this->table . '/list.php') ? $this->table . '/list' : 'common/list', $data);
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
