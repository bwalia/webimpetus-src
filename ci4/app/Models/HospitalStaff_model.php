<?php

namespace App\Models;

use CodeIgniter\Model;

class HospitalStaff_model extends Model
{
    protected $table = 'hospital_staff';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'uuid', 'uuid_business_id', 'staff_number',
        'user_id', 'contact_id', 'employee_id',
        'department', 'job_title', 'specialization', 'grade', 'qualification',
        'gmc_number', 'nmc_number', 'professional_registration', 'registration_expiry',
        'employment_type', 'contract_start_date', 'contract_end_date',
        'shift_pattern', 'work_hours_per_week',
        'security_clearance', 'access_areas', 'can_prescribe', 'can_authorize_procedures',
        'mandatory_training_status', 'last_training_date', 'next_training_due',
        'dbs_check_date', 'dbs_check_expiry', 'occupational_health_clearance',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
        'status', 'leave_type', 'leave_start_date', 'leave_end_date', 'notes',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'modified_at';

    /**
     * Get next staff number
     */
    public function getNextStaffNumber($businessUuid, $prefix = 'HS')
    {
        $lastStaff = $this->where('uuid_business_id', $businessUuid)
            ->where('staff_number LIKE', $prefix . '%')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastStaff) {
            $number = (int)str_replace($prefix . '-', '', $lastStaff['staff_number']);
            return $prefix . '-' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        }

        return $prefix . '-001';
    }

    /**
     * Get staff with joined user, contact, and employee details
     */
    public function getStaffWithDetails($businessUuid, $filters = [])
    {
        $builder = $this->db->table($this->table . ' hs');
        $builder->select('
            hs.*,
            u.name as user_name, u.email as user_email,
            c.name as contact_name, c.phone as contact_phone, c.email as contact_email,
            e.name as employee_name, e.department as employee_department
        ');
        $builder->join('users u', 'u.id = hs.user_id', 'LEFT');
        $builder->join('contacts c', 'c.id = hs.contact_id', 'LEFT');
        $builder->join('employees e', 'e.id = hs.employee_id', 'LEFT');
        $builder->where('hs.uuid_business_id', $businessUuid);

        if (!empty($filters['status'])) {
            $builder->where('hs.status', $filters['status']);
        }

        if (!empty($filters['department'])) {
            $builder->where('hs.department', $filters['department']);
        }

        if (!empty($filters['job_title'])) {
            $builder->where('hs.job_title', $filters['job_title']);
        }

        return $builder->orderBy('hs.staff_number', 'ASC')->get()->getResultArray();
    }

    /**
     * Get staff by UUID with details
     */
    public function getStaffByUuid($uuid)
    {
        $builder = $this->db->table($this->table . ' hs');
        $builder->select('
            hs.*,
            u.name as user_name, u.email as user_email, u.role as user_role,
            c.name as contact_name, c.phone as contact_phone, c.email as contact_email, c.address as contact_address,
            e.name as employee_name, e.department as employee_department, e.start_date as employee_start_date
        ');
        $builder->join('users u', 'u.id = hs.user_id', 'LEFT');
        $builder->join('contacts c', 'c.id = hs.contact_id', 'LEFT');
        $builder->join('employees e', 'e.id = hs.employee_id', 'LEFT');
        $builder->where('hs.uuid', $uuid);

        return $builder->get()->getRowArray();
    }

    /**
     * Get staff by department
     */
    public function getStaffByDepartment($businessUuid, $department)
    {
        return $this->where('uuid_business_id', $businessUuid)
            ->where('department', $department)
            ->where('status', 'Active')
            ->findAll();
    }

    /**
     * Get staff with expiring registrations
     */
    public function getExpiringRegistrations($businessUuid, $days = 90)
    {
        $builder = $this->db->table($this->table . ' hs');
        $builder->select('
            hs.*,
            u.name as user_name,
            c.name as contact_name
        ');
        $builder->join('users u', 'u.id = hs.user_id', 'LEFT');
        $builder->join('contacts c', 'c.id = hs.contact_id', 'LEFT');
        $builder->where('hs.uuid_business_id', $businessUuid);
        $builder->where('hs.registration_expiry >=', date('Y-m-d'));
        $builder->where('hs.registration_expiry <=', date('Y-m-d', strtotime("+{$days} days")));
        $builder->orderBy('hs.registration_expiry', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get staff with overdue training
     */
    public function getOverdueTraining($businessUuid)
    {
        $builder = $this->db->table($this->table . ' hs');
        $builder->select('
            hs.*,
            u.name as user_name,
            c.name as contact_name
        ');
        $builder->join('users u', 'u.id = hs.user_id', 'LEFT');
        $builder->join('contacts c', 'c.id = hs.contact_id', 'LEFT');
        $builder->where('hs.uuid_business_id', $businessUuid);
        $builder->where('hs.mandatory_training_status', 'Overdue');
        $builder->orderBy('hs.next_training_due', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get staff on leave
     */
    public function getStaffOnLeave($businessUuid)
    {
        return $this->where('uuid_business_id', $businessUuid)
            ->where('status', 'On Leave')
            ->where('leave_end_date >=', date('Y-m-d'))
            ->orderBy('leave_end_date', 'ASC')
            ->findAll();
    }

    /**
     * Get departments list
     */
    public function getDepartments($businessUuid)
    {
        $builder = $this->db->table($this->table);
        $builder->select('department, COUNT(*) as staff_count');
        $builder->where('uuid_business_id', $businessUuid);
        $builder->where('department IS NOT NULL');
        $builder->groupBy('department');
        $builder->orderBy('department', 'ASC');

        return $builder->get()->getResultArray();
    }

    private function generateUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
