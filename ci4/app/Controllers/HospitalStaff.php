<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Core\Common_model;
use App\Models\HospitalStaff_model;
use stdClass;

class HospitalStaff extends CommonController
{
    private $hospitalStaff_model;

    function __construct()
    {
        parent::__construct();

        $this->hospitalStaff_model = new HospitalStaff_model();
        $this->model = new Common_model();
        $this->table = "hospital_staff";
        $this->rawTblName = "hospital_staff";
    }

    public function index()
    {
        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;
        $this->data['is_add_permission'] = 1;

        // Get departments for filter
        $this->data['departments'] = $this->hospitalStaff_model->getDepartments(session('uuid_business'));

        // Get summary stats
        $this->data['total_staff'] = $this->hospitalStaff_model
            ->where('uuid_business_id', session('uuid_business'))
            ->countAllResults();

        $this->data['active_staff'] = $this->hospitalStaff_model
            ->where('uuid_business_id', session('uuid_business'))
            ->where('status', 'Active')
            ->countAllResults();

        $this->data['on_leave'] = $this->hospitalStaff_model
            ->where('uuid_business_id', session('uuid_business'))
            ->where('status', 'On Leave')
            ->countAllResults();

        $this->data['expiring_soon'] = $this->hospitalStaff_model
            ->getExpiringRegistrations(session('uuid_business'), 90);

        echo view($this->table . "/list", $this->data);
    }

    public function edit($id = '')
    {
        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;

        if (!empty($id)) {
            $this->data[$this->rawTblName] = $this->hospitalStaff_model->getStaffByUuid($id);
            if (empty($this->data[$this->rawTblName])) {
                session()->setFlashdata('message', 'Hospital staff not found!');
                session()->setFlashdata('alert-class', 'alert-danger');
                return redirect()->to('/' . $this->table);
            }
            $this->data[$this->rawTblName] = (object) $this->data[$this->rawTblName];
        } else {
            $this->data[$this->rawTblName] = new stdClass();
            $this->data[$this->rawTblName]->staff_number = $this->hospitalStaff_model->getNextStaffNumber(session('uuid_business'));
            $this->data[$this->rawTblName]->status = 'Active';
            $this->data[$this->rawTblName]->employment_type = 'Full-time';
            $this->data[$this->rawTblName]->mandatory_training_status = 'Up to Date';
            $this->data[$this->rawTblName]->can_prescribe = 0;
            $this->data[$this->rawTblName]->can_authorize_procedures = 0;
        }

        // Get users for dropdown
        $this->data['users'] = $this->model->getAllDataFromTable('users');

        // Get contacts for dropdown
        $this->data['contacts'] = $this->model->getAllDataFromTable('contacts');

        // Get employees for dropdown
        $this->data['employees'] = $this->model->getAllDataFromTable('employees');

        echo view($this->table . "/edit", $this->data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');

        // Check permissions: update for existing records, create for new records
        if (!empty($uuid) && !$this->checkPermission('update')) {
            session()->setFlashdata('message', 'You do not have permission to update records in this module!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        if (empty($uuid) && !$this->checkPermission('create')) {
            session()->setFlashdata('message', 'You do not have permission to create records in this module!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $data = $this->request->getPost();

        // Generate UUID for new staff
        if (empty($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'hospital_staff');
            $data['uuid_business_id'] = session('uuid_business');
            $data['created_by'] = session('uuid');
        }

        // Convert date formats if needed
        $dateFields = ['contract_start_date', 'contract_end_date', 'registration_expiry',
                       'last_training_date', 'next_training_due', 'dbs_check_date',
                       'dbs_check_expiry', 'leave_start_date', 'leave_end_date'];

        foreach ($dateFields as $field) {
            if (!empty($data[$field]) && strpos($data[$field], '/') !== false) {
                $data[$field] = date('Y-m-d', strtotime(str_replace('/', '-', $data[$field])));
            }
        }

        // Handle checkboxes
        $data['can_prescribe'] = isset($data['can_prescribe']) ? 1 : 0;
        $data['can_authorize_procedures'] = isset($data['can_authorize_procedures']) ? 1 : 0;

        $response = $this->model->insertOrUpdateByUUID($uuid, $data, $this->table);

        if (!$response) {
            session()->setFlashdata('message', 'Something went wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        } else {
            session()->setFlashdata('message', 'Hospital staff saved successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        }

        return redirect()->to('/' . $this->table);
    }

    public function delete($uuid)
    {
        $staff = $this->hospitalStaff_model->where('uuid', $uuid)->first();

        if (!$staff) {
            session()->setFlashdata('message', 'Hospital staff not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $this->model->deleteTableData($this->table, $uuid, 'uuid');

        session()->setFlashdata('message', 'Hospital staff deleted successfully!');
        session()->setFlashdata('alert-class', 'alert-success');

        return redirect()->to('/' . $this->table);
    }

    public function staffList()
    {
        $filters = [];

        if ($this->request->getGet('status')) {
            $filters['status'] = $this->request->getGet('status');
        }

        if ($this->request->getGet('department')) {
            $filters['department'] = $this->request->getGet('department');
        }

        if ($this->request->getGet('job_title')) {
            $filters['job_title'] = $this->request->getGet('job_title');
        }

        $staff = $this->hospitalStaff_model->getStaffWithDetails(session('uuid_business'), $filters);

        echo json_encode(['data' => $staff]);
    }

    public function dashboard()
    {
        $this->data['tableName'] = $this->table;

        // Get departments with staff count
        $this->data['departments'] = $this->hospitalStaff_model->getDepartments(session('uuid_business'));

        // Get staff on leave
        $this->data['staff_on_leave'] = $this->hospitalStaff_model->getStaffOnLeave(session('uuid_business'));

        // Get expiring registrations
        $this->data['expiring_registrations'] = $this->hospitalStaff_model->getExpiringRegistrations(session('uuid_business'), 90);

        // Get overdue training
        $this->data['overdue_training'] = $this->hospitalStaff_model->getOverdueTraining(session('uuid_business'));

        echo view($this->table . "/dashboard", $this->data);
    }

    public function byDepartment($department)
    {
        $staff = $this->hospitalStaff_model->getStaffByDepartment(session('uuid_business'), urldecode($department));

        echo json_encode(['data' => $staff]);
    }
}
