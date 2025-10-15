<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectJobs_model extends Model
{
    protected $table = 'project_jobs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'uuid',
        'uuid_business_id',
        'uuid_project_id',
        'job_number',
        'job_name',
        'job_description',
        'job_type',
        'priority',
        'status',
        'assigned_to_user_id',
        'assigned_to_employee_id',
        'assigned_by',
        'assigned_at',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'estimated_hours',
        'actual_hours',
        'estimated_cost',
        'actual_cost',
        'billable',
        'hourly_rate',
        'completion_percentage',
        'notes',
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
        'uuid_project_id' => 'required|max_length[64]',
        'job_name' => 'required|max_length[255]',
        'job_type' => 'required|in_list[Development,Design,Testing,Deployment,Support,Research,Other]',
        'priority' => 'required|in_list[Low,Normal,High,Urgent]',
        'status' => 'required|in_list[Planning,In Progress,On Hold,Completed,Cancelled]',
    ];

    /**
     * Generate next job number for a project
     */
    public function getNextJobNumber($businessUuid, $projectUuid)
    {
        $lastJob = $this->where('uuid_business_id', $businessUuid)
            ->where('uuid_project_id', $projectUuid)
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastJob && !empty($lastJob->job_number)) {
            preg_match('/JOB-(\d+)/', $lastJob->job_number, $matches);
            $nextNum = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
        } else {
            $nextNum = 1;
        }

        return 'JOB-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get jobs with project and assignment details
     */
    public function getJobsWithDetails($businessUuid, $filters = [])
    {
        $builder = $this->db->table('project_jobs');
        $builder->select('project_jobs.*,
            projects.name as project_name,
            users.name as assigned_user_name,
            employees.first_name as assigned_employee_first_name,
            employees.surname as assigned_employee_surname');
        $builder->join('projects', 'projects.uuid = project_jobs.uuid_project_id', 'left');
        $builder->join('users', 'users.id = project_jobs.assigned_to_user_id', 'left');
        $builder->join('employees', 'employees.id = project_jobs.assigned_to_employee_id', 'left');
        $builder->where('project_jobs.uuid_business_id', $businessUuid);

        // Apply filters
        if (!empty($filters['project_uuid'])) {
            $builder->where('project_jobs.uuid_project_id', $filters['project_uuid']);
        }
        if (!empty($filters['status'])) {
            $builder->where('project_jobs.status', $filters['status']);
        }
        if (!empty($filters['job_type'])) {
            $builder->where('project_jobs.job_type', $filters['job_type']);
        }
        if (!empty($filters['assigned_to_user_id'])) {
            $builder->where('project_jobs.assigned_to_user_id', $filters['assigned_to_user_id']);
        }
        if (!empty($filters['assigned_to_employee_id'])) {
            $builder->where('project_jobs.assigned_to_employee_id', $filters['assigned_to_employee_id']);
        }

        $builder->orderBy('project_jobs.created_at', 'DESC');
        return $builder->get()->getResult();
    }

    /**
     * Get jobs by project UUID
     */
    public function getJobsByProject($projectUuid)
    {
        return $this->where('uuid_project_id', $projectUuid)
            ->orderBy('job_number', 'ASC')
            ->findAll();
    }

    /**
     * Get jobs by user
     */
    public function getJobsByUser($userId)
    {
        return $this->where('assigned_to_user_id', $userId)
            ->whereIn('status', ['Planning', 'In Progress'])
            ->orderBy('priority', 'DESC')
            ->findAll();
    }

    /**
     * Get jobs by employee
     */
    public function getJobsByEmployee($employeeId)
    {
        return $this->where('assigned_to_employee_id', $employeeId)
            ->whereIn('status', ['Planning', 'In Progress'])
            ->orderBy('priority', 'DESC')
            ->findAll();
    }

    /**
     * Update actual hours for a job
     */
    public function updateActualHours($jobUuid, $hours)
    {
        $job = $this->where('uuid', $jobUuid)->first();
        if (!$job) {
            return false;
        }

        $newHours = $job->actual_hours + $hours;
        return $this->where('uuid', $jobUuid)
            ->set('actual_hours', $newHours)
            ->update();
    }

    /**
     * Update completion percentage
     */
    public function updateCompletionPercentage($jobUuid, $percentage)
    {
        return $this->where('uuid', $jobUuid)
            ->set('completion_percentage', $percentage)
            ->update();
    }

    /**
     * Get job timeline summary
     */
    public function getJobTimelineSummary($jobUuid)
    {
        $builder = $this->db->table('project_jobs');
        $builder->select('project_jobs.*,
            COUNT(DISTINCT project_job_phases.id) as total_phases,
            SUM(CASE WHEN project_job_phases.status = "Completed" THEN 1 ELSE 0 END) as completed_phases,
            COUNT(DISTINCT tasks.id) as total_tasks,
            SUM(CASE WHEN tasks.status = "Completed" THEN 1 ELSE 0 END) as completed_tasks');
        $builder->join('project_job_phases', 'project_job_phases.uuid_project_job_id = project_jobs.uuid', 'left');
        $builder->join('tasks', 'tasks.uuid_project_job_id = project_jobs.uuid', 'left');
        $builder->where('project_jobs.uuid', $jobUuid);
        $builder->groupBy('project_jobs.id');

        return $builder->get()->getRow();
    }

    /**
     * Get overdue jobs
     */
    public function getOverdueJobs($businessUuid)
    {
        return $this->where('uuid_business_id', $businessUuid)
            ->where('planned_end_date <', date('Y-m-d'))
            ->whereIn('status', ['Planning', 'In Progress'])
            ->orderBy('planned_end_date', 'ASC')
            ->findAll();
    }

    /**
     * Get job by UUID with project details
     */
    public function getJobByUuid($uuid)
    {
        $builder = $this->db->table('project_jobs');
        $builder->select('project_jobs.*, projects.name as project_name');
        $builder->join('projects', 'projects.uuid = project_jobs.uuid_project_id', 'left');
        $builder->where('project_jobs.uuid', $uuid);
        return $builder->get()->getRow();
    }

    /**
     * Get jobs summary for dashboard
     */
    public function getJobsSummary($businessUuid)
    {
        $builder = $this->db->table('project_jobs');
        $builder->select('
            COUNT(*) as total_jobs,
            SUM(CASE WHEN status = "Planning" THEN 1 ELSE 0 END) as planning,
            SUM(CASE WHEN status = "In Progress" THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status = "On Hold" THEN 1 ELSE 0 END) as on_hold,
            SUM(CASE WHEN status = "Completed" THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = "Cancelled" THEN 1 ELSE 0 END) as cancelled,
            SUM(estimated_hours) as total_estimated_hours,
            SUM(actual_hours) as total_actual_hours,
            SUM(estimated_cost) as total_estimated_cost,
            SUM(actual_cost) as total_actual_cost
        ');
        $builder->where('uuid_business_id', $businessUuid);

        return $builder->get()->getRow();
    }
}
