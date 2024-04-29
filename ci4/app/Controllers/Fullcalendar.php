<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Tasks_model;
use App\Models\TimeslipsModel;

class Fullcalendar extends CommonController
{
    protected $timeSlipsModel;
    protected $taskModel;

    public function __construct()
    {
        parent::__construct();
        $this->timeSlipsModel = new TimeslipsModel();
        if (property_exists($this, 'taskModel'))
        {
            $this->taskModel = new Tasks_model();
        }
    }

    public function index()
    {
        $is_show = $this->request->getGet('show');

        if ($is_show == 'tasks') {
            $table = 'tasks';
            $data['columns'] = $this->db->getFieldNames($table);
            $data['fields'] = array_diff($data['columns'], $this->notAllowedFields);
            $data[$table] = $this->taskModel->getTaskList()['data'];
            $data['tableName'] = $table;

            $viewPath = "common/list";
            if (file_exists(APPPATH . 'Views/' . $this->table . "/list-task.php")) {
                $viewPath = $this->table . "/list-task";
            }
            return view($viewPath, $data);
        } else {
            $table = 'timeslips';
            $data['columns'] = $this->db->getFieldNames($table);
            $data['fields'] = array_diff($data['columns'], $this->notAllowedFields);
            $data[$table] = $this->timeSlipsModel->getRows();
            foreach ($data[$table] as &$record) {
                $record['slip_start_date'] = render_date($record['slip_start_date']);
                $record['slip_end_date'] = render_date($record['slip_end_date']);
            }
            $data['tableName'] = $table;
            $data['viewName'] = 'timeslips calendar';
            $data['viewPath'] = 'fullcalendar';
            $data["tasks"] = $this->timeSlipsModel->getTaskData();
            $data["employees"] = $this->timeSlipsModel->getEmployeesData();
            $data['rawTblName'] = $this->rawTblName;
            $data['is_add_permission'] = 1;
            $data['identifierKey'] = 'uuid';

            $viewPath = "common/list";
            if (file_exists(APPPATH . 'Views/' . $this->table . "/list.php")) {
                $viewPath = $this->table . "/list";
            }

            return view($viewPath, $data);
        }
    }
}
