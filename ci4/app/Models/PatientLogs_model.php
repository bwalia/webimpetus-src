<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientLogs_model extends Model
{
    protected $table = 'patient_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'uuid', 'uuid_business_id', 'log_number',
        'patient_contact_id', 'staff_uuid',
        'log_category', 'log_type',
        // Medication fields
        'medication_name', 'dosage', 'route', 'frequency', 'medication_status',
        // Vital signs
        'blood_pressure_systolic', 'blood_pressure_diastolic', 'heart_rate',
        'temperature', 'respiratory_rate', 'oxygen_saturation', 'blood_glucose',
        'weight', 'height',
        // General
        'title', 'description',
        // Treatment/Procedure
        'procedure_name', 'treatment_plan', 'outcome',
        // Lab results
        'test_name', 'test_result', 'reference_range', 'abnormal_flag',
        // Admission/Discharge
        'admission_type', 'ward', 'bed_number', 'diagnosis', 'discharge_summary',
        // Timing
        'scheduled_datetime', 'performed_datetime', 'administered_at',
        // Attachments
        'attachment_url', 'reference_number',
        // Status
        'priority', 'status', 'is_flagged', 'flag_reason',
        // Compliance
        'verified_by', 'verified_at', 'digital_signature',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'modified_at';

    /**
     * Get next log number
     */
    public function getNextLogNumber($businessUuid, $prefix = 'LOG')
    {
        $lastLog = $this->where('uuid_business_id', $businessUuid)
            ->where('log_number LIKE', $prefix . '%')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastLog) {
            $number = (int)str_replace($prefix . '-', '', $lastLog['log_number']);
            return $prefix . '-' . str_pad($number + 1, 6, '0', STR_PAD_LEFT);
        }

        return $prefix . '-000001';
    }

    /**
     * Get patient logs with staff and patient details
     */
    public function getLogsWithDetails($businessUuid, $filters = [])
    {
        $builder = $this->db->table($this->table . ' pl');
        $builder->select('
            pl.*,
            c.name as patient_name, c.phone as patient_phone,
            hs.staff_number, hs.job_title,
            COALESCE(u.name, co.name) as staff_name
        ');
        $builder->join('contacts c', 'c.id = pl.patient_contact_id', 'LEFT');
        $builder->join('hospital_staff hs', 'hs.uuid = pl.staff_uuid', 'LEFT');
        $builder->join('users u', 'u.id = hs.user_id', 'LEFT');
        $builder->join('contacts co', 'co.id = hs.contact_id', 'LEFT');
        $builder->where('pl.uuid_business_id', $businessUuid);

        if (!empty($filters['patient_contact_id'])) {
            $builder->where('pl.patient_contact_id', $filters['patient_contact_id']);
        }

        if (!empty($filters['log_category'])) {
            $builder->where('pl.log_category', $filters['log_category']);
        }

        if (!empty($filters['from_date'])) {
            $builder->where('pl.performed_datetime >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $builder->where('pl.performed_datetime <=', $filters['to_date']);
        }

        if (!empty($filters['staff_uuid'])) {
            $builder->where('pl.staff_uuid', $filters['staff_uuid']);
        }

        if (!empty($filters['is_flagged'])) {
            $builder->where('pl.is_flagged', 1);
        }

        return $builder->orderBy('pl.performed_datetime', 'DESC')->get()->getResultArray();
    }

    /**
     * Get patient timeline (all logs for a patient)
     */
    public function getPatientTimeline($patientContactId, $businessUuid)
    {
        $builder = $this->db->table($this->table . ' pl');
        $builder->select('
            pl.*,
            hs.staff_number, hs.job_title, hs.department,
            COALESCE(u.name, co.name) as staff_name
        ');
        $builder->join('hospital_staff hs', 'hs.uuid = pl.staff_uuid', 'LEFT');
        $builder->join('users u', 'u.id = hs.user_id', 'LEFT');
        $builder->join('contacts co', 'co.id = hs.contact_id', 'LEFT');
        $builder->where('pl.patient_contact_id', $patientContactId);
        $builder->where('pl.uuid_business_id', $businessUuid);
        $builder->orderBy('pl.performed_datetime', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get medication history for patient
     */
    public function getMedicationHistory($patientContactId, $businessUuid)
    {
        return $this->where('patient_contact_id', $patientContactId)
            ->where('uuid_business_id', $businessUuid)
            ->where('log_category', 'Medication')
            ->orderBy('administered_at', 'DESC')
            ->findAll();
    }

    /**
     * Get vital signs for patient
     */
    public function getVitalSigns($patientContactId, $businessUuid, $days = 7)
    {
        $builder = $this->db->table($this->table);
        $builder->where('patient_contact_id', $patientContactId);
        $builder->where('uuid_business_id', $businessUuid);
        $builder->where('log_category', 'Vital Signs');
        $builder->where('performed_datetime >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $builder->orderBy('performed_datetime', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get lab results for patient
     */
    public function getLabResults($patientContactId, $businessUuid)
    {
        return $this->where('patient_contact_id', $patientContactId)
            ->where('uuid_business_id', $businessUuid)
            ->where('log_category', 'Lab Result')
            ->orderBy('performed_datetime', 'DESC')
            ->findAll();
    }

    /**
     * Get flagged logs
     */
    public function getFlaggedLogs($businessUuid)
    {
        $builder = $this->db->table($this->table . ' pl');
        $builder->select('
            pl.*,
            c.name as patient_name,
            hs.staff_number,
            COALESCE(u.name, co.name) as staff_name
        ');
        $builder->join('contacts c', 'c.id = pl.patient_contact_id', 'LEFT');
        $builder->join('hospital_staff hs', 'hs.uuid = pl.staff_uuid', 'LEFT');
        $builder->join('users u', 'u.id = hs.user_id', 'LEFT');
        $builder->join('contacts co', 'co.id = hs.contact_id', 'LEFT');
        $builder->where('pl.uuid_business_id', $businessUuid);
        $builder->where('pl.is_flagged', 1);
        $builder->orderBy('pl.priority', 'DESC');
        $builder->orderBy('pl.performed_datetime', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get logs by category grouped
     */
    public function getLogsByCategory($businessUuid, $fromDate = null, $toDate = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('log_category, COUNT(*) as count');
        $builder->where('uuid_business_id', $businessUuid);

        if ($fromDate) {
            $builder->where('performed_datetime >=', $fromDate);
        }

        if ($toDate) {
            $builder->where('performed_datetime <=', $toDate);
        }

        $builder->groupBy('log_category');
        $builder->orderBy('count', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get scheduled logs (upcoming appointments, treatments)
     */
    public function getScheduledLogs($businessUuid, $days = 7)
    {
        $builder = $this->db->table($this->table . ' pl');
        $builder->select('
            pl.*,
            c.name as patient_name, c.phone as patient_phone,
            hs.staff_number,
            COALESCE(u.name, co.name) as staff_name
        ');
        $builder->join('contacts c', 'c.id = pl.patient_contact_id', 'LEFT');
        $builder->join('hospital_staff hs', 'hs.uuid = pl.staff_uuid', 'LEFT');
        $builder->join('users u', 'u.id = hs.user_id', 'LEFT');
        $builder->join('contacts co', 'co.id = hs.contact_id', 'LEFT');
        $builder->where('pl.uuid_business_id', $businessUuid);
        $builder->where('pl.status', 'Scheduled');
        $builder->where('pl.scheduled_datetime >=', date('Y-m-d H:i:s'));
        $builder->where('pl.scheduled_datetime <=', date('Y-m-d H:i:s', strtotime("+{$days} days")));
        $builder->orderBy('pl.scheduled_datetime', 'ASC');

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
