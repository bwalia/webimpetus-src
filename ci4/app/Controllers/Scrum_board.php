<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Tasks_model;
use App\Models\Sprints_model;

class Scrum_board extends CommonController
{
    protected $taskModel;
    protected $sprintModel;

    public function __construct()
    {
        parent::__construct();
        $this->taskModel = new Tasks_model();
        $this->sprintModel = new Sprints_model();
    }

    /**
     * Display scrum board with sprint-based view
     */
    public function index()
    {
        $data['tableName'] = 'scrum_board';
        $data['sprints_list'] = $this->sprintModel->getSprintList();
        
        // Get selected sprint or use current sprint
        $sprint = $_GET['sprint'] ?? "";
        
        if (empty($sprint)) {
            $current_sprint = $this->sprintModel->getCurrentSprint();
            $sprint = $current_sprint;
        }

        // Get sprint details
        if ($sprint && is_numeric($sprint)) {
            $data['selected_sprint'] = $this->sprintModel->getSprintById($sprint);
        } else {
            $data['selected_sprint'] = null;
        }

        // Categories for scrum board
        $categories = ['backlog', 'sprint-ready', 'in-sprint', 'completed'];

        foreach ($categories as $category) {
            if ($sprint === "all") {
                // Show all tasks regardless of sprint
                $data['tasks'][$category] = $this->taskModel->getTaskList(['category' => $category])['data'];
            } elseif ($sprint && is_numeric($sprint)) {
                // Show tasks for specific sprint
                $data['tasks'][$category] = $this->taskModel->getTaskList(['category' => $category, 'sprint_id' => $sprint])['data'];
            } else {
                // Show tasks without sprint assignment (backlog)
                if ($category === 'backlog') {
                    $data['tasks'][$category] = $this->taskModel->getTaskList("category = '$category' AND (sprint_id IS NULL OR sprint_id = '')")['data'];
                } else {
                    $data['tasks'][$category] = [];
                }
            }
        }

        return view('scrum_board/list', $data);
    }

    /**
     * Update task category for scrum board
     */
    public function update_task()
    {
        $task_id = $this->request->getPost('task_id');
        $data_category = $this->request->getPost('data_category');
        
        $updateData = ['category' => $data_category];
        
        // If moving to in-sprint, ensure sprint is assigned
        if ($data_category === 'in-sprint' && isset($_POST['sprint_id'])) {
            $updateData['sprint_id'] = $this->request->getPost('sprint_id');
        }
        
        $this->taskModel->updateData($task_id, $updateData);
        echo json_encode(array("status" => true, "message" => "Successfully Updated!"));
    }

    /**
     * Move tasks to sprint
     */
    public function move_to_sprint()
    {
        $task_ids = $this->request->getPost('task_ids');
        $sprint_id = $this->request->getPost('sprint_id');
        
        if (!empty($task_ids) && !empty($sprint_id)) {
            foreach ($task_ids as $task_id) {
                $this->taskModel->updateData($task_id, [
                    'sprint_id' => $sprint_id,
                    'category' => 'sprint-ready'
                ]);
            }
            echo json_encode(array("status" => true, "message" => "Tasks moved to sprint successfully!"));
        } else {
            echo json_encode(array("status" => false, "message" => "Invalid data provided."));
        }
    }
}
