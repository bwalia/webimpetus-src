<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectJobScheduler_model extends Model
{
    protected $table = 'project_job_scheduler';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'uuid',
        'uuid_business_id',
        'uuid_project_job_id',
        'uuid_phase_id',
        'assigned_to_user_id',
        'assigned_to_employee_id',
        'schedule_date',
        'start_time',
        'end_time',
        'all_day',
        'duration_hours',
        'title',
        'color',
        'notes',
        'status',
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
        'schedule_date' => 'required|valid_date',
        'title' => 'required|max_length[255]',
        'status' => 'required|in_list[Scheduled,In Progress,Completed,Cancelled]',
    ];

    /**
     * Get schedule by date range
     */
    public function getScheduleByDateRange($businessUuid, $startDate, $endDate)
    {
        $builder = $this->db->table('project_job_scheduler');
        $builder->select('project_job_scheduler.*,
            project_jobs.job_name,
            project_jobs.job_number,
            project_job_phases.phase_name,
            users.name as assigned_user_name,
            employees.first_name as assigned_employee_first_name,
            employees.surname as assigned_employee_surname,
            projects.name as project_name');
        $builder->join('project_jobs', 'project_jobs.uuid = project_job_scheduler.uuid_project_job_id', 'left');
        $builder->join('project_job_phases', 'project_job_phases.uuid = project_job_scheduler.uuid_phase_id', 'left');
        $builder->join('users', 'users.id = project_job_scheduler.assigned_to_user_id', 'left');
        $builder->join('employees', 'employees.id = project_job_scheduler.assigned_to_employee_id', 'left');
        $builder->join('projects', 'projects.uuid = project_jobs.uuid_project_id', 'left');
        $builder->where('project_job_scheduler.uuid_business_id', $businessUuid);
        $builder->where('project_job_scheduler.schedule_date >=', $startDate);
        $builder->where('project_job_scheduler.schedule_date <=', $endDate);
        $builder->orderBy('project_job_scheduler.schedule_date', 'ASC');
        $builder->orderBy('project_job_scheduler.start_time', 'ASC');

        return $builder->get()->getResult();
    }

    /**
     * Get schedule by user
     */
    public function getScheduleByUser($userId, $startDate, $endDate)
    {
        $builder = $this->db->table('project_job_scheduler');
        $builder->select('project_job_scheduler.*,
            project_jobs.job_name,
            project_jobs.job_number,
            project_job_phases.phase_name');
        $builder->join('project_jobs', 'project_jobs.uuid = project_job_scheduler.uuid_project_job_id', 'left');
        $builder->join('project_job_phases', 'project_job_phases.uuid = project_job_scheduler.uuid_phase_id', 'left');
        $builder->where('project_job_scheduler.assigned_to_user_id', $userId);
        $builder->where('project_job_scheduler.schedule_date >=', $startDate);
        $builder->where('project_job_scheduler.schedule_date <=', $endDate);
        $builder->orderBy('project_job_scheduler.schedule_date', 'ASC');

        return $builder->get()->getResult();
    }

    /**
     * Get schedule by employee
     */
    public function getScheduleByEmployee($employeeId, $startDate, $endDate)
    {
        $builder = $this->db->table('project_job_scheduler');
        $builder->select('project_job_scheduler.*,
            project_jobs.job_name,
            project_jobs.job_number,
            project_job_phases.phase_name');
        $builder->join('project_jobs', 'project_jobs.uuid = project_job_scheduler.uuid_project_job_id', 'left');
        $builder->join('project_job_phases', 'project_job_phases.uuid = project_job_scheduler.uuid_phase_id', 'left');
        $builder->where('project_job_scheduler.assigned_to_employee_id', $employeeId);
        $builder->where('project_job_scheduler.schedule_date >=', $startDate);
        $builder->where('project_job_scheduler.schedule_date <=', $endDate);
        $builder->orderBy('project_job_scheduler.schedule_date', 'ASC');

        return $builder->get()->getResult();
    }

    /**
     * Get calendar events for FullCalendar.js
     */
    public function getCalendarEvents($businessUuid, $filters = [])
    {
        $builder = $this->db->table('project_job_scheduler');
        $builder->select('project_job_scheduler.*,
            project_jobs.job_name,
            project_jobs.job_number,
            project_job_phases.phase_name,
            users.name as assigned_user_name,
            employees.first_name as assigned_employee_first_name,
            employees.surname as assigned_employee_surname');
        $builder->join('project_jobs', 'project_jobs.uuid = project_job_scheduler.uuid_project_job_id', 'left');
        $builder->join('project_job_phases', 'project_job_phases.uuid = project_job_scheduler.uuid_phase_id', 'left');
        $builder->join('users', 'users.id = project_job_scheduler.assigned_to_user_id', 'left');
        $builder->join('employees', 'employees.id = project_job_scheduler.assigned_to_employee_id', 'left');
        $builder->where('project_job_scheduler.uuid_business_id', $businessUuid);

        // Apply filters
        if (!empty($filters['start_date'])) {
            $builder->where('project_job_scheduler.schedule_date >=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $builder->where('project_job_scheduler.schedule_date <=', $filters['end_date']);
        }
        if (!empty($filters['assigned_to_user_id'])) {
            $builder->where('project_job_scheduler.assigned_to_user_id', $filters['assigned_to_user_id']);
        }
        if (!empty($filters['assigned_to_employee_id'])) {
            $builder->where('project_job_scheduler.assigned_to_employee_id', $filters['assigned_to_employee_id']);
        }
        if (!empty($filters['job_uuid'])) {
            $builder->where('project_job_scheduler.uuid_project_job_id', $filters['job_uuid']);
        }

        $results = $builder->get()->getResult();

        // Format for FullCalendar
        $events = [];
        foreach ($results as $row) {
            $events[] = [
                'id' => $row->uuid,
                'title' => $row->title,
                'start' => $row->schedule_date . ($row->start_time ? 'T' . $row->start_time : ''),
                'end' => $row->schedule_date . ($row->end_time ? 'T' . $row->end_time : ''),
                'allDay' => (bool)$row->all_day,
                'backgroundColor' => $row->color,
                'borderColor' => $row->color,
                'extendedProps' => [
                    'job_name' => $row->job_name,
                    'job_number' => $row->job_number,
                    'phase_name' => $row->phase_name,
                    'assigned_user' => $row->assigned_user_name,
                    'assigned_employee' => trim($row->assigned_employee_first_name . ' ' . $row->assigned_employee_surname),
                    'status' => $row->status,
                    'notes' => $row->notes,
                ],
            ];
        }

        return $events;
    }

    /**
     * Drag and drop update
     */
    public function dragDropUpdate($uuid, $newDate, $newAssignment = null)
    {
        $data = ['schedule_date' => $newDate];

        if (!empty($newAssignment['user_id'])) {
            $data['assigned_to_user_id'] = $newAssignment['user_id'];
            $data['assigned_to_employee_id'] = null;
        } elseif (!empty($newAssignment['employee_id'])) {
            $data['assigned_to_employee_id'] = $newAssignment['employee_id'];
            $data['assigned_to_user_id'] = null;
        }

        return $this->where('uuid', $uuid)->set($data)->update();
    }

    /**
     * Get schedule entry by UUID
     */
    public function getScheduleByUuid($uuid)
    {
        return $this->where('uuid', $uuid)->first();
    }
}
