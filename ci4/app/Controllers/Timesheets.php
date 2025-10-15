<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Core\Common_model;
use App\Models\Timesheets_model;
use App\Traits\PermissionTrait;

class Timesheets extends CommonController
{
    use PermissionTrait;

    private $timesheet_model;

    function __construct()
    {
        parent::__construct();

        $this->timesheet_model = new Timesheets_model();
        $this->model = new Common_model();
        $this->table = "timesheets";
        $this->rawTblName = "timesheet";
    }

    public function index()
    {
        $this->requireReadPermission();

        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;
        $this->data['is_add_permission'] = 1;

        // Pass granular permissions to view
        $this->addPermissionsToView($this->data);

        // Get summary stats
        $businessUuid = session('uuid_business');
        $this->data['stats'] = $this->getStats($businessUuid);

        echo view($this->table . "/list", $this->data);
    }

    public function edit($id = '')
    {
        $this->requireEditPermission($id);

        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;

        if (!empty($id)) {
            $this->data[$this->rawTblName] = $this->timesheet_model->where('uuid', $id)->first();
            if (empty($this->data[$this->rawTblName])) {
                session()->setFlashdata('message', 'Timesheet not found!');
                session()->setFlashdata('alert-class', 'alert-danger');
                return redirect()->to('/' . $this->table);
            }
            $this->data[$this->rawTblName] = (object) $this->data[$this->rawTblName];
        } else {
            $this->data[$this->rawTblName] = (object) [
                'start_time' => date('Y-m-d H:i:s'),
                'status' => 'draft',
                'is_billable' => 1,
                'is_running' => 0,
                'hourly_rate' => 0,
            ];
        }

        // Load selected records only for edit mode (for pre-populating selects)
        if (!empty($id) && !empty($this->data[$this->rawTblName])) {
            $timesheet = $this->data[$this->rawTblName];

            // Load selected employee
            if (!empty($timesheet->employee_id)) {
                $this->data['selected_employee'] = $this->db->table('employees')
                    ->where('id', $timesheet->employee_id)
                    ->get()->getRowArray();
            }

            // Load selected customer
            if (!empty($timesheet->customer_id)) {
                $this->data['selected_customer'] = $this->db->table('customers')
                    ->where('id', $timesheet->customer_id)
                    ->get()->getRowArray();
            }

            // Load selected project
            if (!empty($timesheet->project_id)) {
                $this->data['selected_project'] = $this->db->table('projects')
                    ->where('id', $timesheet->project_id)
                    ->get()->getRowArray();
            }

            // Load selected task
            if (!empty($timesheet->task_id)) {
                $this->data['selected_task'] = $this->db->table('tasks')
                    ->where('id', $timesheet->task_id)
                    ->get()->getRowArray();
            }
        }

        echo view($this->table . "/edit", $this->data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $this->requireEditPermission($uuid, true);

        $data = $this->request->getPost();

        // Generate UUID for new timesheet
        if (empty($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'timesheets');
            $data['uuid_business_id'] = session('uuid_business');
            $data['created_by'] = session('uuid');
        }

        // Handle datetime fields
        if (!empty($data['start_time']) && strpos($data['start_time'], '/') !== false) {
            $data['start_time'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data['start_time'])));
        }

        if (!empty($data['end_time']) && strpos($data['end_time'], '/') !== false) {
            $data['end_time'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data['end_time'])));
        }

        // Calculate duration and totals
        if (!empty($data['start_time']) && !empty($data['end_time'])) {
            $start = strtotime($data['start_time']);
            $end = strtotime($data['end_time']);
            $data['duration_minutes'] = ($end - $start) / 60;
            $data['billable_hours'] = round($data['duration_minutes'] / 60, 2);

            if (!empty($data['hourly_rate'])) {
                $data['total_amount'] = round($data['billable_hours'] * $data['hourly_rate'], 2);
            }
        }

        $response = $this->model->insertOrUpdateByUUID($uuid, $data, $this->table);

        if (!$response) {
            session()->setFlashdata('message', 'Something went wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        } else {
            session()->setFlashdata('message', 'Timesheet saved successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        }

        return redirect()->to('/' . $this->table);
    }

    public function delete($uuid)
    {
        $this->requireDeletePermission(true);

        if (!empty($uuid)) {
            $response = $this->model->deleteTableData($this->table, $uuid, 'uuid');

            if ($response) {
                session()->setFlashdata('message', 'Timesheet deleted successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
            } else {
                session()->setFlashdata('message', 'Failed to delete timesheet!');
                session()->setFlashdata('alert-class', 'alert-danger');
            }
        }

        return redirect()->to('/' . $this->table);
    }

    /**
     * Get timesheets list for DataTables
     */
    public function timesheetsList()
    {
        // Check read permission
        $this->requireReadPermission();

        $filters = [];

        if ($this->request->getGet('status')) {
            $filters['status'] = $this->request->getGet('status');
        }

        if ($this->request->getGet('employee_id')) {
            $filters['employee_id'] = $this->request->getGet('employee_id');
        }

        if ($this->request->getGet('project_id')) {
            $filters['project_id'] = $this->request->getGet('project_id');
        }

        if ($this->request->getGet('from_date')) {
            $filters['from_date'] = $this->request->getGet('from_date');
        }

        if ($this->request->getGet('to_date')) {
            $filters['to_date'] = $this->request->getGet('to_date');
        }

        // Get business UUID from request parameter or session
        $businessUuid = $this->request->getGet('uuid_business_id') ?? session('uuid_business');

        $timesheets = $this->timesheet_model->getTimesheetsWithDetails($businessUuid, $filters);

        return $this->respond(['data' => $timesheets]);
    }

    /**
     * Start timer
     */
    public function startTimer()
    {
        $this->requireCreatePermission();

        $data = [
            'uuid' => UUID::v5(UUID::v4(), 'timesheets'),
            'uuid_business_id' => session('uuid_business'),
            'employee_id' => $this->request->getPost('employee_id'),
            'project_id' => $this->request->getPost('project_id'),
            'task_id' => $this->request->getPost('task_id'),
            'customer_id' => $this->request->getPost('customer_id'),
            'description' => $this->request->getPost('description'),
            'hourly_rate' => $this->request->getPost('hourly_rate'),
            'start_time' => date('Y-m-d H:i:s'),
            'is_running' => 1,
            'status' => 'running',
            'created_by' => session('uuid'),
        ];

        $result = $this->timesheet_model->insert($data);

        if ($result) {
            echo json_encode(['status' => true, 'message' => 'Timer started successfully!', 'id' => $result]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to start timer!']);
        }
    }

    /**
     * Stop timer
     */
    public function stopTimer($uuid)
    {
        $this->requireUpdatePermission();

        $result = $this->timesheet_model->stopTimer($uuid);

        if ($result) {
            echo json_encode(['status' => true, 'message' => 'Timer stopped successfully!']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to stop timer!']);
        }
    }

    /**
     * Create invoice from selected timesheets
     */
    public function createInvoice()
    {
        $this->requireCreatePermission();

        $timesheetIds = $this->request->getPost('timesheet_ids');

        if (empty($timesheetIds)) {
            echo json_encode(['status' => false, 'message' => 'No timesheets selected!']);
            return;
        }

        // Get timesheets
        $timesheets = $this->timesheet_model->whereIn('id', $timesheetIds)->findAll();

        if (empty($timesheets)) {
            echo json_encode(['status' => false, 'message' => 'Timesheets not found!']);
            return;
        }

        // Create invoice
        $invoiceData = [
            'uuid' => UUID::v5(UUID::v4(), 'sales_invoices'),
            'uuid_business_id' => session('uuid_business'),
            'client_id' => $timesheets[0]['customer_id'],
            'date' => time(),
            'due_date' => strtotime('+30 days'),
            'status' => 'Draft',
            'created_by' => session('uuid'),
        ];

        // Get next invoice number
        $lastInvoice = $this->db->table('sales_invoices')
            ->where('uuid_business_id', session('uuid_business'))
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $invoiceNumber = 1;
        if ($lastInvoice && !empty($lastInvoice['invoice_number'])) {
            $invoiceNumber = ((int)$lastInvoice['invoice_number']) + 1;
        }
        $invoiceData['invoice_number'] = $invoiceNumber;

        // Insert invoice
        $this->db->table('sales_invoices')->insert($invoiceData);
        $invoiceId = $this->db->insertID();

        // Create invoice items from timesheets
        foreach ($timesheets as $timesheet) {
            $itemData = [
                'uuid' => UUID::v5(UUID::v4(), 'sales_invoice_items'),
                'sales_invoices_uuid' => $invoiceData['uuid'],
                'description' => $timesheet['description'] ?? 'Time entry',
                'hours' => $timesheet['billable_hours'],
                'rate' => $timesheet['hourly_rate'],
                'amount' => $timesheet['total_amount'],
            ];

            $this->db->table('sales_invoice_items')->insert($itemData);
        }

        // Mark timesheets as invoiced
        $this->timesheet_model->markAsInvoiced($timesheetIds, $invoiceId);

        echo json_encode([
            'status' => true,
            'message' => 'Invoice created successfully!',
            'invoice_id' => $invoiceId,
            'invoice_uuid' => $invoiceData['uuid'],
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    private function getStats($businessUuid)
    {
        $stats = [];

        // Total hours this week
        $startOfWeek = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $stats['hours_this_week'] = $this->db->table('timesheets')
            ->selectSum('billable_hours')
            ->where('uuid_business_id', $businessUuid)
            ->where('start_time >=', $startOfWeek)
            ->where('deleted_at IS NULL')
            ->get()
            ->getRow()->billable_hours ?? 0;

        // Total hours this month
        $startOfMonth = date('Y-m-01 00:00:00');
        $stats['hours_this_month'] = $this->db->table('timesheets')
            ->selectSum('billable_hours')
            ->where('uuid_business_id', $businessUuid)
            ->where('start_time >=', $startOfMonth)
            ->where('deleted_at IS NULL')
            ->get()
            ->getRow()->billable_hours ?? 0;

        // Uninvoiced amount
        $stats['uninvoiced_amount'] = $this->db->table('timesheets')
            ->selectSum('total_amount')
            ->where('uuid_business_id', $businessUuid)
            ->where('is_billable', 1)
            ->where('is_invoiced', 0)
            ->where('deleted_at IS NULL')
            ->get()
            ->getRow()->total_amount ?? 0;

        // Running timers
        $stats['running_timers'] = $this->db->table('timesheets')
            ->where('uuid_business_id', $businessUuid)
            ->where('is_running', 1)
            ->where('deleted_at IS NULL')
            ->countAllResults();

        return $stats;
    }

    /**
     * AJAX endpoint for searching employees
     */
    public function searchEmployeesAjax()
    {
        $q = $this->request->getVar('q');
        $businessUuid = session('uuid_business');

        $builder = $this->db->table('employees');
        $builder->where('uuid_business_id', $businessUuid);

        if (!empty($q)) {
            $builder->groupStart();
            $builder->like('first_name', $q);
            $builder->orLike('surname', $q);
            $builder->orLike('email', $q);
            $builder->groupEnd();
        }

        $data = $builder->select('id, first_name, surname, email, job_title')
            ->limit(50)
            ->orderBy('first_name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->respond($data);
    }

    /**
     * AJAX endpoint for searching customers
     */
    public function searchCustomersAjax()
    {
        $q = $this->request->getVar('q');
        $businessUuid = session('uuid_business');

        $builder = $this->db->table('customers');
        $builder->where('uuid_business_id', $businessUuid);

        if (!empty($q)) {
            $builder->groupStart();
            $builder->like('company_name', $q);
            $builder->orLike('email', $q);
            $builder->groupEnd();
        }

        $data = $builder->select('id, company_name, email, phone')
            ->limit(50)
            ->orderBy('company_name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->respond($data);
    }

    /**
     * AJAX endpoint for searching projects
     */
    public function searchProjectsAjax()
    {
        $q = $this->request->getVar('q');
        $customerId = $this->request->getVar('customer_id');
        $businessUuid = session('uuid_business');

        $builder = $this->db->table('projects');
        $builder->where('uuid_business_id', $businessUuid);

        if (!empty($customerId)) {
            $builder->where('customers_id', $customerId);
        }

        if (!empty($q)) {
            $builder->groupStart();
            $builder->like('name', $q);
            $builder->orLike('project_code', $q);
            $builder->groupEnd();
        }

        $data = $builder->select('id, name, project_code, customers_id')
            ->limit(50)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->respond($data);
    }

    /**
     * AJAX endpoint for searching tasks
     */
    public function searchTasksAjax()
    {
        $q = $this->request->getVar('q');
        $projectId = $this->request->getVar('project_id');
        $businessUuid = session('uuid_business');

        $builder = $this->db->table('tasks');
        $builder->where('uuid_business_id', $businessUuid);

        if (!empty($projectId)) {
            $builder->where('projects_id', $projectId);
        }

        if (!empty($q)) {
            $builder->like('name', $q);
        }

        $data = $builder->select('id, name, projects_id, status')
            ->limit(50)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->respond($data);
    }
}
