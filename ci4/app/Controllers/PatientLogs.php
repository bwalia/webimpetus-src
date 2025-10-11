<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Core\Common_model;
use App\Models\PatientLogs_model;
use App\Models\HospitalStaff_model;
use stdClass;

class PatientLogs extends CommonController
{
    private $patientLogs_model;
    private $hospitalStaff_model;

    function __construct()
    {
        parent::__construct();

        $this->patientLogs_model = new PatientLogs_model();
        $this->hospitalStaff_model = new HospitalStaff_model();
        $this->model = new Common_model();
        $this->table = "patient_logs";
        $this->rawTblName = "patient_log";
    }

    public function index()
    {
        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;
        $this->data['is_add_permission'] = 1;

        // Get summary stats
        $this->data['total_logs'] = $this->patientLogs_model
            ->where('uuid_business_id', session('uuid_business'))
            ->countAllResults();

        $this->data['flagged_logs'] = $this->patientLogs_model
            ->where('uuid_business_id', session('uuid_business'))
            ->where('is_flagged', 1)
            ->countAllResults();

        $this->data['today_logs'] = $this->patientLogs_model
            ->where('uuid_business_id', session('uuid_business'))
            ->where('DATE(performed_datetime)', date('Y-m-d'))
            ->countAllResults();

        // Get categories
        $this->data['log_categories'] = $this->patientLogs_model
            ->getLogsByCategory(session('uuid_business'));

        echo view($this->table . "/list", $this->data);
    }

    public function edit($id = '')
    {
        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;

        if (!empty($id)) {
            $this->data[$this->rawTblName] = $this->patientLogs_model->where('uuid', $id)->first();
            if (empty($this->data[$this->rawTblName])) {
                session()->setFlashdata('message', 'Patient log not found!');
                session()->setFlashdata('alert-class', 'alert-danger');
                return redirect()->to('/' . $this->table);
            }
            $this->data[$this->rawTblName] = (object) $this->data[$this->rawTblName];
        } else {
            $this->data[$this->rawTblName] = new stdClass();
            $this->data[$this->rawTblName]->log_number = $this->patientLogs_model->getNextLogNumber(session('uuid_business'));
            $this->data[$this->rawTblName]->log_category = 'General';
            $this->data[$this->rawTblName]->status = 'Draft';
            $this->data[$this->rawTblName]->priority = 'Normal';
            $this->data[$this->rawTblName]->performed_datetime = date('Y-m-d H:i:s');
            $this->data[$this->rawTblName]->is_flagged = 0;
        }

        // Get patients (from contacts)
        $this->data['patients'] = $this->model->getAllDataFromTable('contacts');

        // Get hospital staff
        $this->data['staff'] = $this->hospitalStaff_model
            ->where('uuid_business_id', session('uuid_business'))
            ->where('status', 'Active')
            ->findAll();

        echo view($this->table . "/edit", $this->data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $data = $this->request->getPost();

        // Generate UUID for new log
        if (empty($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'patient_logs');
            $data['uuid_business_id'] = session('uuid_business');
            $data['created_by'] = session('uuid');
        }

        // Convert date formats if needed
        $dateFields = ['scheduled_datetime', 'performed_datetime', 'administered_at', 'verified_at'];

        foreach ($dateFields as $field) {
            if (!empty($data[$field]) && strpos($data[$field], '/') !== false) {
                $data[$field] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data[$field])));
            }
        }

        // Handle checkboxes
        $data['is_flagged'] = isset($data['is_flagged']) ? 1 : 0;

        $response = $this->model->insertOrUpdateByUUID($uuid, $data, $this->table);

        if (!$response) {
            session()->setFlashdata('message', 'Something went wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        } else {
            session()->setFlashdata('message', 'Patient log saved successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        }

        return redirect()->to('/' . $this->table);
    }

    public function delete($uuid)
    {
        $log = $this->patientLogs_model->where('uuid', $uuid)->first();

        if (!$log) {
            session()->setFlashdata('message', 'Patient log not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $this->model->deleteTableData($this->table, $uuid, 'uuid');

        session()->setFlashdata('message', 'Patient log deleted successfully!');
        session()->setFlashdata('alert-class', 'alert-success');

        return redirect()->to('/' . $this->table);
    }

    public function logsList()
    {
        $filters = [];

        if ($this->request->getGet('patient_contact_id')) {
            $filters['patient_contact_id'] = $this->request->getGet('patient_contact_id');
        }

        if ($this->request->getGet('log_category')) {
            $filters['log_category'] = $this->request->getGet('log_category');
        }

        if ($this->request->getGet('from_date')) {
            $filters['from_date'] = $this->request->getGet('from_date');
        }

        if ($this->request->getGet('to_date')) {
            $filters['to_date'] = $this->request->getGet('to_date');
        }

        if ($this->request->getGet('staff_uuid')) {
            $filters['staff_uuid'] = $this->request->getGet('staff_uuid');
        }

        if ($this->request->getGet('is_flagged')) {
            $filters['is_flagged'] = 1;
        }

        $logs = $this->patientLogs_model->getLogsWithDetails(session('uuid_business'), $filters);

        echo json_encode(['data' => $logs]);
    }

    public function timeline($patientContactId)
    {
        $this->data['tableName'] = $this->table;

        // Get patient details
        $patient = getRowArray('contacts', ['id' => $patientContactId], false);
        if (!$patient) {
            session()->setFlashdata('message', 'Patient not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $this->data['patient'] = (object) $patient;
        $this->data['patient_contact_id'] = $patientContactId;

        // Get patient timeline
        $this->data['timeline'] = $this->patientLogs_model->getPatientTimeline($patientContactId, session('uuid_business'));

        // Get medication history
        $this->data['medications'] = $this->patientLogs_model->getMedicationHistory($patientContactId, session('uuid_business'));

        // Get vital signs (last 7 days)
        $this->data['vital_signs'] = $this->patientLogs_model->getVitalSigns($patientContactId, session('uuid_business'), 7);

        // Get lab results
        $this->data['lab_results'] = $this->patientLogs_model->getLabResults($patientContactId, session('uuid_business'));

        echo view($this->table . "/timeline", $this->data);
    }

    public function flagged()
    {
        $this->data['tableName'] = $this->table;
        $this->data['flagged_logs'] = $this->patientLogs_model->getFlaggedLogs(session('uuid_business'));

        echo view($this->table . "/flagged", $this->data);
    }

    public function scheduled()
    {
        $this->data['tableName'] = $this->table;

        $days = $this->request->getGet('days') ?? 7;
        $this->data['scheduled_logs'] = $this->patientLogs_model->getScheduledLogs(session('uuid_business'), $days);

        echo view($this->table . "/scheduled", $this->data);
    }

    public function quickLog()
    {
        // Quick log entry for common activities
        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;

        // Get patients
        $this->data['patients'] = $this->model->getAllDataFromTable('contacts');

        // Get hospital staff
        $this->data['staff'] = $this->hospitalStaff_model
            ->where('uuid_business_id', session('uuid_business'))
            ->where('status', 'Active')
            ->findAll();

        echo view($this->table . "/quick_log", $this->data);
    }

    public function saveQuickLog()
    {
        $data = $this->request->getPost();
        $data['uuid'] = UUID::v5(UUID::v4(), 'patient_logs');
        $data['log_number'] = $this->patientLogs_model->getNextLogNumber(session('uuid_business'));
        $data['uuid_business_id'] = session('uuid_business');
        $data['created_by'] = session('uuid');
        $data['performed_datetime'] = date('Y-m-d H:i:s');
        $data['status'] = 'Completed';

        $response = $this->model->insertData($this->table, $data);

        if ($response) {
            echo json_encode(['status' => true, 'message' => 'Log entry saved successfully!']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to save log entry!']);
        }
    }
}
