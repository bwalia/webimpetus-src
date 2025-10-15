<?php

namespace App\Models;

use CodeIgniter\Model;

class Timesheets_model extends Model
{
    protected $table = 'timesheets';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'uuid',
        'uuid_business_id',
        'employee_id',
        'project_id',
        'task_id',
        'customer_id',
        'description',
        'start_time',
        'end_time',
        'duration_minutes',
        'billable_hours',
        'hourly_rate',
        'total_amount',
        'is_billable',
        'is_running',
        'is_invoiced',
        'invoice_id',
        'status',
        'notes',
        'tags',
        'created_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['calculateTotals'];
    protected $beforeUpdate = ['calculateTotals'];

    /**
     * Calculate billable hours and total amount before saving
     */
    protected function calculateTotals(array $data)
    {
        if (isset($data['data'])) {
            // Calculate duration in minutes if start and end times are provided
            if (!empty($data['data']['start_time']) && !empty($data['data']['end_time'])) {
                $start = strtotime($data['data']['start_time']);
                $end = strtotime($data['data']['end_time']);
                $data['data']['duration_minutes'] = ($end - $start) / 60;
            }

            // Calculate billable hours from duration
            if (!empty($data['data']['duration_minutes'])) {
                $data['data']['billable_hours'] = round($data['data']['duration_minutes'] / 60, 2);
            }

            // Calculate total amount
            if (!empty($data['data']['billable_hours']) && !empty($data['data']['hourly_rate'])) {
                $data['data']['total_amount'] = round($data['data']['billable_hours'] * $data['data']['hourly_rate'], 2);
            }
        }

        return $data;
    }

    /**
     * Get timesheets with related data
     */
    public function getTimesheetsWithDetails($businessUuid, $filters = [])
    {
        $builder = $this->db->table('timesheets t')
            ->select('t.*,
                e.first_name as employee_first_name,
                e.surname as employee_surname,
                p.name as project_name,
                p.id as project_id_name,
                task.name as task_name,
                c.company_name as customer_name,
                CONCAT(e.first_name, " ", e.surname) as employee_full_name')
            ->join('employees e', 'e.id = t.employee_id', 'left')
            ->join('projects p', 'p.id = t.project_id', 'left')
            ->join('tasks task', 'task.id = t.task_id', 'left')
            ->join('customers c', 'c.id = t.customer_id', 'left')
            ->where('t.uuid_business_id', $businessUuid)
            ->where('t.deleted_at IS NULL', null, false);

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('t.status', $filters['status']);
        }

        if (!empty($filters['employee_id'])) {
            $builder->where('t.employee_id', $filters['employee_id']);
        }

        if (!empty($filters['project_id'])) {
            $builder->where('t.project_id', $filters['project_id']);
        }

        if (!empty($filters['customer_id'])) {
            $builder->where('t.customer_id', $filters['customer_id']);
        }

        if (!empty($filters['is_billable'])) {
            $builder->where('t.is_billable', 1);
        }

        if (!empty($filters['is_invoiced']) && $filters['is_invoiced'] === 'yes') {
            $builder->where('t.is_invoiced', 1);
        } elseif (!empty($filters['is_invoiced']) && $filters['is_invoiced'] === 'no') {
            $builder->where('t.is_invoiced', 0);
        }

        if (!empty($filters['from_date'])) {
            $builder->where('t.start_time >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $builder->where('t.start_time <=', $filters['to_date'] . ' 23:59:59');
        }

        $builder->orderBy('t.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get running timesheets for an employee
     */
    public function getRunningTimesheets($businessUuid, $employeeId = null)
    {
        $builder = $this->where('uuid_business_id', $businessUuid)
            ->where('is_running', 1)
            ->where('status', 'running');

        if ($employeeId) {
            $builder->where('employee_id', $employeeId);
        }

        return $builder->findAll();
    }

    /**
     * Stop a running timer
     */
    public function stopTimer($uuid)
    {
        $timesheet = $this->where('uuid', $uuid)->first();

        if ($timesheet && $timesheet['is_running'] == 1) {
            $data = [
                'end_time' => date('Y-m-d H:i:s'),
                'is_running' => 0,
                'status' => 'stopped',
            ];

            // Calculate duration
            if (!empty($timesheet['start_time'])) {
                $start = strtotime($timesheet['start_time']);
                $end = time();
                $duration_minutes = ($end - $start) / 60;
                $data['duration_minutes'] = $duration_minutes;
                $data['billable_hours'] = round($duration_minutes / 60, 2);

                if (!empty($timesheet['hourly_rate'])) {
                    $data['total_amount'] = round($data['billable_hours'] * $timesheet['hourly_rate'], 2);
                }
            }

            return $this->where('uuid', $uuid)->set($data)->update();
        }

        return false;
    }

    /**
     * Get uninvoiced billable timesheets
     */
    public function getUninvoicedBillable($businessUuid, $customerId = null, $projectId = null)
    {
        $builder = $this->where('uuid_business_id', $businessUuid)
            ->where('is_billable', 1)
            ->where('is_invoiced', 0)
            ->where('status !=', 'running');

        if ($customerId) {
            $builder->where('customer_id', $customerId);
        }

        if ($projectId) {
            $builder->where('project_id', $projectId);
        }

        return $builder->orderBy('start_time', 'ASC')->findAll();
    }

    /**
     * Mark timesheets as invoiced
     */
    public function markAsInvoiced($timesheetIds, $invoiceId)
    {
        return $this->whereIn('id', $timesheetIds)
            ->set([
                'is_invoiced' => 1,
                'invoice_id' => $invoiceId,
                'status' => 'invoiced',
            ])
            ->update();
    }
}
