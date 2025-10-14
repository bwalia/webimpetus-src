<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\ProjectJobs_model;
use App\Models\ProjectJobPhases_model;
use App\Models\Projects_model;
use App\Models\Users_model;
use App\Models\Employees_model;
use App\Libraries\UUID;
use CodeIgniter\API\ResponseTrait;

class ProjectJobs extends CommonController
{
    use ResponseTrait;

    protected $projectJobs_model;
    protected $projectJobPhases_model;
    protected $projects_model;
    protected $users_model;
    protected $employees_model;

    function __construct()
    {
        parent::__construct();

        $this->projectJobs_model = new ProjectJobs_model();
        $this->projectJobPhases_model = new ProjectJobPhases_model();
        $this->projects_model = new Projects_model();
        $this->users_model = new Users_model();
        $this->employees_model = new Employees_model();
    }

    public function index()
    {
        $data['tableName'] = 'project_jobs';
        $data['rawTblName'] = 'project_job';
        $data['is_add_permission'] = 1;

        // Get summary stats
        $summary = $this->projectJobs_model->getJobsSummary(session('uuid_business'));
        $data['summary'] = $summary;

        echo view('project_jobs/list', $data);
    }

    public function jobsList()
    {
        $limit = (int)$this->request->getVar('limit');
        $offset = (int)$this->request->getVar('offset');
        $query = $this->request->getVar('query');
        $order = $this->request->getVar('order') ?? "created_at";
        $dir = $this->request->getVar('dir') ?? "desc";

        $filters = [];
        if ($projectUuid = $this->request->getVar('project_uuid')) {
            $filters['project_uuid'] = $projectUuid;
        }
        if ($status = $this->request->getVar('status')) {
            $filters['status'] = $status;
        }
        if ($jobType = $this->request->getVar('job_type')) {
            $filters['job_type'] = $jobType;
        }

        $jobs = $this->projectJobs_model->getJobsWithDetails(session('uuid_business'), $filters);

        // Filter by query if provided
        if ($query) {
            $jobs = array_filter($jobs, function ($job) use ($query) {
                return stripos($job->job_name, $query) !== false ||
                       stripos($job->job_number, $query) !== false ||
                       stripos($job->project_name, $query) !== false;
            });
        }

        $total = count($jobs);
        $jobs = array_slice($jobs, $offset, $limit);

        $data = [
            'rawTblName' => 'project_job',
            'tableName' => 'project_jobs',
            'data' => $jobs,
            'recordsTotal' => $total,
        ];

        return $this->response->setJSON($data);
    }

    public function edit($uuid = 0)
    {
        $jobData = $uuid ? $this->projectJobs_model->getJobByUuid($uuid) : null;

        $data['tableName'] = 'project_jobs';
        $data['rawTblName'] = 'project_job';
        $data['project_job'] = $jobData;
        $data['projects'] = $this->projects_model->where('uuid_business_id', session('uuid_business'))->findAll();
        $data['users'] = $this->users_model->findAll();
        $data['employees'] = $this->employees_model->where('uuid_business_id', session('uuid_business'))->findAll();

        // If editing, get phases
        if ($jobData) {
            $data['phases'] = $this->projectJobPhases_model->getPhasesByJob($uuid);
            $data['timeline_summary'] = $this->projectJobs_model->getJobTimelineSummary($uuid);
        }

        echo view('project_jobs/edit', $data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $isNew = empty($uuid);

        if ($isNew) {
            $uuid = UUID::v5(time());
            $jobNumber = $this->projectJobs_model->getNextJobNumber(
                session('uuid_business'),
                $this->request->getPost('uuid_project_id')
            );
        } else {
            $jobNumber = $this->request->getPost('job_number');
        }

        $data = [
            'uuid' => $uuid,
            'uuid_business_id' => session('uuid_business'),
            'uuid_project_id' => $this->request->getPost('uuid_project_id'),
            'job_number' => $jobNumber,
            'job_name' => $this->request->getPost('job_name'),
            'job_description' => $this->request->getPost('job_description'),
            'job_type' => $this->request->getPost('job_type'),
            'priority' => $this->request->getPost('priority'),
            'status' => $this->request->getPost('status'),
            'assigned_to_user_id' => $this->request->getPost('assigned_to_user_id') ?: null,
            'assigned_to_employee_id' => $this->request->getPost('assigned_to_employee_id') ?: null,
            'planned_start_date' => $this->request->getPost('planned_start_date') ?: null,
            'planned_end_date' => $this->request->getPost('planned_end_date') ?: null,
            'actual_start_date' => $this->request->getPost('actual_start_date') ?: null,
            'actual_end_date' => $this->request->getPost('actual_end_date') ?: null,
            'estimated_hours' => $this->request->getPost('estimated_hours') ?: null,
            'estimated_cost' => $this->request->getPost('estimated_cost') ?: null,
            'billable' => $this->request->getPost('billable') ? 1 : 0,
            'hourly_rate' => $this->request->getPost('hourly_rate') ?: null,
            'completion_percentage' => $this->request->getPost('completion_percentage') ?: 0,
            'notes' => $this->request->getPost('notes'),
            'modified_at' => date('Y-m-d H:i:s'),
        ];

        if ($isNew) {
            $data['created_by'] = session('uuid');
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['assigned_by'] = session('uuid');
            $data['assigned_at'] = date('Y-m-d H:i:s');
        }

        if ($isNew) {
            $result = $this->projectJobs_model->insert($data);
        } else {
            $result = $this->projectJobs_model->where('uuid', $uuid)->set($data)->update();
        }

        if ($result) {
            session()->setFlashdata('message', 'Job ' . ($isNew ? 'created' : 'updated') . ' successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        } else {
            session()->setFlashdata('message', 'Failed to ' . ($isNew ? 'create' : 'update') . ' job.');
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to('/project_jobs');
    }

    public function delete($uuid)
    {
        $result = $this->projectJobs_model->where('uuid', $uuid)->delete();

        if ($result) {
            session()->setFlashdata('message', 'Job deleted successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        } else {
            session()->setFlashdata('message', 'Failed to delete job.');
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to('/project_jobs');
    }

    public function byProject($projectUuid)
    {
        $jobs = $this->projectJobs_model->getJobsByProject($projectUuid);
        return $this->response->setJSON($jobs);
    }

    public function assign($uuid)
    {
        $userId = $this->request->getPost('user_id');
        $employeeId = $this->request->getPost('employee_id');

        $data = [
            'assigned_to_user_id' => $userId ?: null,
            'assigned_to_employee_id' => $employeeId ?: null,
            'assigned_by' => session('uuid'),
            'assigned_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->projectJobs_model->where('uuid', $uuid)->set($data)->update();

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Job assigned successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to assign job'], 500);
        }
    }

    public function updateProgress($uuid)
    {
        $percentage = (int)$this->request->getPost('completion_percentage');

        $result = $this->projectJobs_model->updateCompletionPercentage($uuid, $percentage);

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Progress updated']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update progress'], 500);
        }
    }
}
