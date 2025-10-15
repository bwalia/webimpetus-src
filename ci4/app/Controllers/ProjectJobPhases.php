<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\ProjectJobPhases_model;
use App\Models\ProjectJobs_model;
use App\Models\Users_model;
use App\Libraries\UUID;
use CodeIgniter\API\ResponseTrait;

class ProjectJobPhases extends CommonController
{
    use ResponseTrait;

    protected $projectJobPhases_model;
    protected $projectJobs_model;
    protected $users_model;
    protected $db;

    function __construct()
    {
        parent::__construct();

        $this->projectJobPhases_model = new ProjectJobPhases_model();
        $this->projectJobs_model = new ProjectJobs_model();
        $this->users_model = new Users_model();
        $this->db = \Config\Database::connect();
    }

    public function index($jobUuid = null)
    {
        $data['tableName'] = 'project_job_phases';
        $data['rawTblName'] = 'project_job_phase';
        $data['is_add_permission'] = 1;
        $data['jobUuid'] = $jobUuid;

        if ($jobUuid) {
            $data['job'] = $this->projectJobs_model->getJobByUuid($jobUuid);
            $data['phases'] = $this->projectJobPhases_model->getPhasesWithDependencies($jobUuid);
            $data['summary'] = $this->projectJobPhases_model->getPhasesSummary($jobUuid);
        }

        echo view('project_job_phases/list', $data);
    }

    public function phasesList($jobUuid)
    {
        try {
            $businessUuid = session('uuid_business');
            if (!$businessUuid) {
                return $this->response->setJSON([
                    'error' => 'No business UUID in session',
                    'data' => []
                ]);
            }

            $phases = $this->projectJobPhases_model->getPhasesWithDependencies($jobUuid);

            // Convert to array with reindexed keys
            $phases = array_values($phases);
            $total = count($phases);

            $data = [
                'rawTblName' => 'project_job_phase',
                'tableName' => 'project_job_phases',
                'data' => $phases,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
            ];

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'ProjectJobPhases::phasesList - Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data' => []
            ]);
        }
    }

    public function edit($uuid = 0, $jobUuid = null)
    {
        $phaseData = $uuid ? $this->projectJobPhases_model->getPhaseByUuid($uuid) : null;

        $data['tableName'] = 'project_job_phases';
        $data['rawTblName'] = 'project_job_phase';
        $data['phase'] = $phaseData;
        $data['jobUuid'] = $jobUuid ?: ($phaseData ? $phaseData->uuid_project_job_id : null);

        if ($data['jobUuid']) {
            $data['job'] = $this->projectJobs_model->getJobByUuid($data['jobUuid']);
            $data['availablePhases'] = $this->projectJobPhases_model->getPhasesByJob($data['jobUuid']);
        }

        $data['users'] = $this->users_model->findAll();
        $data['employees'] = $this->db->table('employees')
            ->where('uuid_business_id', session('uuid_business'))
            ->get()
            ->getResult();

        echo view('project_job_phases/edit', $data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $jobUuid = $this->request->getPost('uuid_project_job_id');
        $isNew = empty($uuid);

        if ($isNew) {
            $uuid = UUID::v5(UUID::v4(), 'project_job_phases');
            $phaseNumber = $this->projectJobPhases_model->getNextPhaseNumber(
                session('uuid_business'),
                $jobUuid
            );
        } else {
            $phaseNumber = $this->request->getPost('phase_number');
        }

        // Helper function to convert date from m/d/Y to Y-m-d
        $convertDate = function($dateStr) {
            if (empty($dateStr)) return null;
            // Try parsing as m/d/Y first
            $date = \DateTime::createFromFormat('m/d/Y', $dateStr);
            if ($date) {
                return $date->format('Y-m-d');
            }
            // If already in Y-m-d format, return as is
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
                return $dateStr;
            }
            return null;
        };

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
            'planned_start_date' => $convertDate($this->request->getPost('planned_start_date')),
            'planned_end_date' => $convertDate($this->request->getPost('planned_end_date')),
            'actual_start_date' => $convertDate($this->request->getPost('actual_start_date')),
            'actual_end_date' => $convertDate($this->request->getPost('actual_end_date')),
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
