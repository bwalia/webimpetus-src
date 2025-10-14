<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\ProjectJobScheduler_model;
use App\Models\ProjectJobs_model;
use App\Models\ProjectJobPhases_model;
use App\Models\Users_model;
use App\Models\Employees_model;
use App\Libraries\UUID;
use CodeIgniter\API\ResponseTrait;

class ProjectJobScheduler extends CommonController
{
    use ResponseTrait;

    protected $projectJobScheduler_model;
    protected $projectJobs_model;
    protected $projectJobPhases_model;
    protected $users_model;
    protected $employees_model;

    function __construct()
    {
        parent::__construct();

        $this->projectJobScheduler_model = new ProjectJobScheduler_model();
        $this->projectJobs_model = new ProjectJobs_model();
        $this->projectJobPhases_model = new ProjectJobPhases_model();
        $this->users_model = new Users_model();
        $this->employees_model = new Employees_model();
    }

    public function calendar()
    {
        $data['tableName'] = 'project_job_scheduler';
        $data['rawTblName'] = 'project_job_schedule';
        $data['users'] = $this->users_model->findAll();
        $data['employees'] = $this->employees_model->where('uuid_business_id', session('uuid_business'))->findAll();

        echo view('project_job_scheduler/calendar', $data);
    }

    public function getEvents()
    {
        $filters = [
            'start_date' => $this->request->getVar('start'),
            'end_date' => $this->request->getVar('end'),
            'assigned_to_user_id' => $this->request->getVar('user_id'),
            'assigned_to_employee_id' => $this->request->getVar('employee_id'),
            'job_uuid' => $this->request->getVar('job_uuid'),
        ];

        $events = $this->projectJobScheduler_model->getCalendarEvents(session('uuid_business'), $filters);

        return $this->response->setJSON($events);
    }

    public function createEvent()
    {
        $uuid = UUID::v5(time());

        $data = [
            'uuid' => $uuid,
            'uuid_business_id' => session('uuid_business'),
            'uuid_project_job_id' => $this->request->getPost('job_uuid'),
            'uuid_phase_id' => $this->request->getPost('phase_uuid') ?: null,
            'assigned_to_user_id' => $this->request->getPost('user_id') ?: null,
            'assigned_to_employee_id' => $this->request->getPost('employee_id') ?: null,
            'schedule_date' => $this->request->getPost('date'),
            'start_time' => $this->request->getPost('start_time') ?: null,
            'end_time' => $this->request->getPost('end_time') ?: null,
            'all_day' => $this->request->getPost('all_day') ? 1 : 0,
            'duration_hours' => $this->request->getPost('duration_hours') ?: null,
            'title' => $this->request->getPost('title'),
            'color' => $this->request->getPost('color') ?: '#667eea',
            'notes' => $this->request->getPost('notes'),
            'status' => 'Scheduled',
            'created_by' => session('uuid'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->projectJobScheduler_model->insert($data);

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Event created', 'uuid' => $uuid]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to create event'], 500);
        }
    }

    public function updateEvent($uuid)
    {
        $data = [
            'schedule_date' => $this->request->getPost('date'),
            'start_time' => $this->request->getPost('start_time') ?: null,
            'end_time' => $this->request->getPost('end_time') ?: null,
            'all_day' => $this->request->getPost('all_day') ? 1 : 0,
            'title' => $this->request->getPost('title'),
            'notes' => $this->request->getPost('notes'),
            'modified_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->projectJobScheduler_model->where('uuid', $uuid)->set($data)->update();

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Event updated']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update event'], 500);
        }
    }

    public function deleteEvent($uuid)
    {
        $result = $this->projectJobScheduler_model->where('uuid', $uuid)->delete();

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Event deleted']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete event'], 500);
        }
    }

    public function dragDrop()
    {
        $uuid = $this->request->getPost('uuid');
        $newDate = $this->request->getPost('new_date');
        $newAssignment = [
            'user_id' => $this->request->getPost('user_id'),
            'employee_id' => $this->request->getPost('employee_id'),
        ];

        $result = $this->projectJobScheduler_model->dragDropUpdate($uuid, $newDate, $newAssignment);

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Event updated']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update event'], 500);
        }
    }
}
