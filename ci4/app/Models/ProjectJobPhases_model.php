<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectJobPhases_model extends Model
{
    protected $table = 'project_job_phases';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'uuid',
        'uuid_business_id',
        'uuid_project_job_id',
        'phase_number',
        'phase_name',
        'phase_description',
        'phase_order',
        'status',
        'assigned_to_user_id',
        'assigned_to_employee_id',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'estimated_hours',
        'actual_hours',
        'depends_on_phase_uuid',
        'completion_percentage',
        'notes',
        'deliverables',
        'acceptance_criteria',
        'created_by',
        'created_at',
        'modified_at',
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'modified_at';

    protected $validationRules = [
        'uuid' => 'required|max_length[64]',
        'uuid_business_id' => 'required|max_length[64]',
        'uuid_project_job_id' => 'required|max_length[64]',
        'phase_name' => 'required|max_length[255]',
        'status' => 'required|in_list[Not Started,In Progress,Completed,Blocked]',
    ];

    /**
     * Generate next phase number for a job
     */
    public function getNextPhaseNumber($businessUuid, $jobUuid)
    {
        $lastPhase = $this->where('uuid_business_id', $businessUuid)
            ->where('uuid_project_job_id', $jobUuid)
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastPhase && !empty($lastPhase->phase_number)) {
            preg_match('/PHASE-(\d+)/', $lastPhase->phase_number, $matches);
            $nextNum = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
        } else {
            $nextNum = 1;
        }

        return 'PHASE-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get phases by job UUID
     */
    public function getPhasesByJob($jobUuid)
    {
        return $this->where('uuid_project_job_id', $jobUuid)
            ->orderBy('phase_order', 'ASC')
            ->findAll();
    }

    /**
     * Get phases with dependencies
     */
    public function getPhasesWithDependencies($jobUuid)
    {
        $builder = $this->db->table('project_job_phases as p1');
        $builder->select('p1.*,
            p2.phase_name as dependency_phase_name,
            p2.status as dependency_status,
            users.name as assigned_user_name,
            employees.first_name as assigned_employee_first_name,
            employees.surname as assigned_employee_surname');
        $builder->join('project_job_phases as p2', 'p2.uuid = p1.depends_on_phase_uuid', 'left');
        $builder->join('users', 'users.id = p1.assigned_to_user_id', 'left');
        $builder->join('employees', 'employees.id = p1.assigned_to_employee_id', 'left');
        $builder->where('p1.uuid_project_job_id', $jobUuid);
        $builder->orderBy('p1.phase_order', 'ASC');

        return $builder->get()->getResult();
    }

    /**
     * Update phase progress
     */
    public function updatePhaseProgress($phaseUuid, $percentage)
    {
        $data = ['completion_percentage' => $percentage];

        // Auto-update status based on percentage
        if ($percentage == 0) {
            $data['status'] = 'Not Started';
        } elseif ($percentage == 100) {
            $data['status'] = 'Completed';
            $data['actual_end_date'] = date('Y-m-d');
        } else {
            $data['status'] = 'In Progress';
            if (!$this->where('uuid', $phaseUuid)->first()->actual_start_date) {
                $data['actual_start_date'] = date('Y-m-d');
            }
        }

        return $this->where('uuid', $phaseUuid)->set($data)->update();
    }

    /**
     * Check if dependencies are completed
     */
    public function checkDependenciesCompleted($phaseUuid)
    {
        $phase = $this->where('uuid', $phaseUuid)->first();
        if (!$phase || !$phase->depends_on_phase_uuid) {
            return true; // No dependencies
        }

        $dependency = $this->where('uuid', $phase->depends_on_phase_uuid)->first();
        return $dependency && $dependency->status === 'Completed';
    }

    /**
     * Get blocked phases
     */
    public function getBlockedPhases($jobUuid)
    {
        return $this->where('uuid_project_job_id', $jobUuid)
            ->where('status', 'Blocked')
            ->orderBy('phase_order', 'ASC')
            ->findAll();
    }

    /**
     * Get phase by UUID
     */
    public function getPhaseByUuid($uuid)
    {
        return $this->where('uuid', $uuid)->first();
    }

    /**
     * Reorder phases
     */
    public function reorderPhases($jobUuid, $phaseOrders)
    {
        foreach ($phaseOrders as $uuid => $order) {
            $this->where('uuid', $uuid)
                ->where('uuid_project_job_id', $jobUuid)
                ->set('phase_order', $order)
                ->update();
        }
        return true;
    }

    /**
     * Get phases summary
     */
    public function getPhasesSummary($jobUuid)
    {
        $builder = $this->db->table('project_job_phases');
        $builder->select('
            COUNT(*) as total_phases,
            SUM(CASE WHEN status = "Not Started" THEN 1 ELSE 0 END) as not_started,
            SUM(CASE WHEN status = "In Progress" THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status = "Completed" THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = "Blocked" THEN 1 ELSE 0 END) as blocked,
            SUM(estimated_hours) as total_estimated_hours,
            SUM(actual_hours) as total_actual_hours,
            AVG(completion_percentage) as avg_completion
        ');
        $builder->where('uuid_project_job_id', $jobUuid);

        return $builder->get()->getRow();
    }
}
