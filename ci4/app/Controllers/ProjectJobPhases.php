<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\ProjectJobPhases_model;
use App\Models\ProjectJobs_model;
use App\Models\Users_model;
use App\Models\Employees_model;
use App\Libraries\UUID;
use CodeIgniter\API\ResponseTrait;

class ProjectJobPhases extends CommonController
{
    use ResponseTrait;

    protected $projectJobPhases_model;
    protected $projectJobs_model;
    protected $users_model;
    protected $employees_model;

    function __construct()
    {
        parent::__construct();

        $this->projectJobPhases_model = new ProjectJobPhases_model();
        $this->projectJobs_model = new ProjectJobs_model();
        $this->users_model = new Users_model();
        $this->employees_model = new Employees_model();
    }

    public function index($jobUuid = null)
    {
        $data['tableName'] = 'project_job_phases';
        $data['rawTblName'] = 'project_job_phase';
        $data['is_add_permission'] = 1;
        $data['job_uuid'] = $jobUuid;

        if ($jobUuid) {
            $data['job'] = $this->projectJobs_model->getJobByUuid($jobUuid);
            $data['phases'] = $this->projectJobPhases_model->getPhasesWithDependencies($jobUuid);
            $data['summary'] = $this->projectJobPhases_model->getPhasesSummary($jobUuid);
        }

        echo view('project_job_phases/list', $data);
    }

    public function edit($uuid = 0, $jobUuid = null)
    {
        $phaseData = $uuid ? $this->projectJobPhases_model->getPhaseByUuid($uuid) : null;

        $data['tableName'] = 'project_job_phases';
        $data['rawTblName'] = 'project_job_phase';
        $data['project_job_phase'] = $phaseData;
        $data['job_uuid'] = $jobUuid ?: ($phaseData ? $phaseData->uuid_project_job_id : null);

        if ($data['job_uuid']) {
            $data['job'] = $this->projectJobs_model->getJobByUuid($data['job_uuid']);
            $data['phases'] = $this->projectJobPhases_model->getPhasesByJob($data['job_uuid']);
        }

        $data['users'] = $this->users_model->findAll();
        $data['employees'] = $this->employees_model->where('uuid_business_id', session('uuid_business'))->findAll();

        echo view('project_job_phases/edit', $data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $jobUuid = $this->request->getPost('uuid_project_job_id');
        $isNew = empty($uuid);

        if ($isNew) {
            $uuid = UUID::v5(time());
            $phaseNumber = $this->projectJobPhases_model->getNextPhaseNumber(
                session('uuid_business'),
                $jobUuid
            );
        } else {
            $phaseNumber = $this->request->getPost('phase_number');
        }

        $data = [
            'uuid' => $uuid,
            'uuid_business_id' => session('uuid_business'),
            'uuid_project_job_id' => $jobUuid,
            'phase_number' => $phaseNumber,
            'phase_name' => $this->request->getPost('phase_name'),
            'phase_description' => $this->request->getPost('phase_description'),
            'phase_order' => $this->request->getPost('phase_order') ?: 1,
            'status' => $this->request->getPost('status'),
            'assigned_to_user_id' => $this->request->getPost('assigned_to_user_id') ?: null,
            'assigned_to_employee_id' => $this->request->getPost('assigned_to_employee_id') ?: null,
            'planned_start_date' => $this->request->getPost('planned_start_date') ?: null,
            'planned_end_date' => $this->request->getPost('planned_end_date') ?: null,
            'actual_start_date' => $this->request->getPost('actual_start_date') ?: null,
            'actual_end_date' => $this->request->getPost('actual_end_date') ?: null,
            'estimated_hours' => $this->request->getPost('estimated_hours') ?: null,
            'depends_on_phase_uuid' => $this->request->getPost('depends_on_phase_uuid') ?: null,
            'completion_percentage' => $this->request->getPost('completion_percentage') ?: 0,
            'notes' => $this->request->getPost('notes'),
            'deliverables' => $this->request->getPost('deliverables'),
            'acceptance_criteria' => $this->request->getPost('acceptance_criteria'),
            'modified_at' => date('Y-m-d H:i:s'),
        ];

        if ($isNew) {
            $data['created_by'] = session('uuid');
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        if ($isNew) {
            $result = $this->projectJobPhases_model->insert($data);
        } else {
            $result = $this->projectJobPhases_model->where('uuid', $uuid)->set($data)->update();
        }

        if ($result) {
            session()->setFlashdata('message', 'Phase ' . ($isNew ? 'created' : 'updated') . ' successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        } else {
            session()->setFlashdata('message', 'Failed to ' . ($isNew ? 'create' : 'update') . ' phase.');
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to('/project_job_phases/index/' . $jobUuid);
    }

    public function delete($uuid)
    {
        $phase = $this->projectJobPhases_model->getPhaseByUuid($uuid);
        $jobUuid = $phase ? $phase->uuid_project_job_id : null;

        $result = $this->projectJobPhases_model->where('uuid', $uuid)->delete();

        if ($result) {
            session()->setFlashdata('message', 'Phase deleted successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        } else {
            session()->setFlashdata('message', 'Failed to delete phase.');
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to('/project_job_phases/index/' . $jobUuid);
    }

    public function phasesByJob($jobUuid)
    {
        $phases = $this->projectJobPhases_model->getPhasesByJob($jobUuid);
        return $this->response->setJSON($phases);
    }

    public function updateStatus($uuid)
    {
        $status = $this->request->getPost('status');

        $result = $this->projectJobPhases_model->where('uuid', $uuid)->set('status', $status)->update();

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }

    public function checkDependencies($uuid)
    {
        $canStart = $this->projectJobPhases_model->checkDependenciesCompleted($uuid);

        return $this->response->setJSON([
            'can_start' => $canStart,
            'message' => $canStart ? 'Dependencies completed' : 'Dependencies not yet completed'
        ]);
    }

    public function reorder()
    {
        $jobUuid = $this->request->getPost('job_uuid');
        $phaseOrders = $this->request->getPost('phase_orders'); // ['uuid' => order]

        $result = $this->projectJobPhases_model->reorderPhases($jobUuid, $phaseOrders);

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Phases reordered']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to reorder'], 500);
        }
    }
}
