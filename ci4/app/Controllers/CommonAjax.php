<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

/**
 * CommonAjax Controller
 * Provides centralized AJAX search endpoints for all modules
 * Usage: /common/searchEmployees?q=john
 */
class CommonAjax extends ResourceController
{
    protected $db;
    protected $format = 'json';

    public function __construct()
    {
        // Don't call parent constructor to avoid session issues
        $this->db = \Config\Database::connect();
    }

    /**
     * Test endpoint
     */
    public function test()
    {
        return $this->respond([
            'status' => 'ok',
            'message' => 'CommonAjax controller is working',
            'has_session' => !empty(session('uuid')),
            'uuid_business' => session('uuid_business')
        ]);
    }

    /**
     * Search employees by name, email
     */
    public function searchEmployees()
    {
        try {
            $q = $this->request->getVar('q');
            $businessUuid = session('uuid_business');

            if (empty($businessUuid)) {
                return $this->respond([
                    'error' => 'No business UUID in session'
                ]);
            }

            $builder = $this->db->table('employees');
            $builder->where('uuid_business_id', $businessUuid);

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('first_name', $q);
                $builder->orLike('surname', $q);
                $builder->orLike('email', $q);
                $builder->groupEnd();
            }

            $data = $builder->select('id, first_name, surname, email, title, direct_phone as phone')
                ->limit(50)
                ->orderBy('first_name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search customers by company name, email
     */
    public function searchCustomers()
    {
        try {
            $q = $this->request->getVar('q');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('customers');
            $builder->where('uuid_business_id', $businessUuid);

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('company_name', $q);
                $builder->orLike('email', $q);
                $builder->orLike('phone', $q);
                $builder->groupEnd();
            }

            $data = $builder->select('id, company_name, email, phone, address1, city, country')
                ->limit(50)
                ->orderBy('company_name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search contacts by name, email, company
     */
    public function searchContacts()
    {
        try {
            $q = $this->request->getVar('q');
            $customerId = $this->request->getVar('customer_id');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('contacts');
            $builder->select('contacts.id, contacts.first_name, contacts.surname, contacts.email, contacts.direct_phone as phone, customers.company_name');
            $builder->join('customers', 'customers.id = contacts.client_id', 'left');
            $builder->where('contacts.uuid_business_id', $businessUuid);

            if (!empty($customerId)) {
                $builder->where('contacts.client_id', $customerId);
            }

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('contacts.first_name', $q);
                $builder->orLike('contacts.surname', $q);
                $builder->orLike('contacts.email', $q);
                $builder->orLike('customers.company_name', $q);
                $builder->groupEnd();
            }

            $data = $builder->limit(50)
                ->orderBy('contacts.first_name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search projects by name, code, customer
     */
    public function searchProjects()
    {
        try {
            $q = $this->request->getVar('q');
            $customerId = $this->request->getVar('customer_id');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('projects');
            $builder->select('projects.id, projects.name, projects.customers_id, customers.company_name as customer_name');
            $builder->join('customers', 'customers.id = projects.customers_id', 'left');
            $builder->where('projects.uuid_business_id', $businessUuid);

            if (!empty($customerId)) {
                $builder->where('projects.customers_id', $customerId);
            }

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('projects.name', $q);
                $builder->orLike('customers.company_name', $q);
                $builder->groupEnd();
            }

            $data = $builder->limit(50)
                ->orderBy('projects.name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search tasks by name, project
     */
    public function searchTasks()
    {
        try {
            $q = $this->request->getVar('q');
            $projectId = $this->request->getVar('project_id');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('tasks');
            $builder->select('tasks.id, tasks.name, tasks.projects_id, tasks.status, projects.name as project_name');
            $builder->join('projects', 'projects.id = tasks.projects_id', 'left');
            $builder->where('tasks.uuid_business_id', $businessUuid);

            if (!empty($projectId)) {
                $builder->where('tasks.projects_id', $projectId);
            }

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('tasks.name', $q);
                $builder->orLike('projects.name', $q);
                $builder->groupEnd();
            }

            $data = $builder->limit(50)
                ->orderBy('tasks.name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search users by name, email
     */
    public function searchUsers()
    {
        try {
            $q = $this->request->getVar('q');

            $builder = $this->db->table('users');

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('name', $q);
                $builder->orLike('email', $q);
                $builder->groupEnd();
            }

            $data = $builder->select('id, name, email')
                ->limit(50)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search businesses by name, email
     */
    public function searchBusinesses()
    {
        try {
            $q = $this->request->getVar('q');

            $builder = $this->db->table('businesses');

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('name', $q);
                $builder->orLike('company_email', $q);
                $builder->groupEnd();
            }

            $data = $builder->select('id, uuid, name, company_email, company_telephone')
                ->limit(50)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search categories by name
     */
    public function searchCategories()
    {
        try {
            $q = $this->request->getVar('q');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('categories');
            $builder->where('uuid_business_id', $businessUuid);

            if (!empty($q)) {
                $builder->like('name', $q);
            }

            $data = $builder->select('id, name, description')
                ->limit(50)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search sprints by name, dates
     */
    public function searchSprints()
    {
        try {
            $q = $this->request->getVar('q');
            $projectId = $this->request->getVar('project_id');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('sprints');
            $builder->where('uuid_business_id', $businessUuid);

            if (!empty($projectId)) {
                $builder->where('projects_id', $projectId);
            }

            if (!empty($q)) {
                $builder->like('sprint_name', $q);
            }

            $data = $builder->select('id, sprint_name, start_date, end_date, status')
                ->limit(50)
                ->orderBy('start_date', 'DESC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search templates by name, type
     */
    public function searchTemplates()
    {
        try {
            $q = $this->request->getVar('q');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('templates');
            $builder->where('uuid_business_id', $businessUuid);

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('name', $q);
                $builder->orLike('type', $q);
                $builder->groupEnd();
            }

            $data = $builder->select('id, name, type, description')
                ->limit(50)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search roles by name
     */
    public function searchRoles()
    {
        try {
            $q = $this->request->getVar('q');

            $builder = $this->db->table('roles');

            if (!empty($q)) {
                $builder->like('name', $q);
            }

            $data = $builder->select('id, uuid, name, description')
                ->limit(50)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search tags by name
     */
    public function searchTags()
    {
        try {
            $q = $this->request->getVar('q');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('tags');
            $builder->where('uuid_business_id', $businessUuid);

            if (!empty($q)) {
                $builder->like('name', $q);
            }

            $data = $builder->select('id, name, color, description')
                ->limit(50)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search services by name, description
     */
    public function searchServices()
    {
        try {
            $q = $this->request->getVar('q');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('services');
            $builder->where('uuid_business_id', $businessUuid);

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('name', $q);
                $builder->orLike('description', $q);
                $builder->groupEnd();
            }

            $data = $builder->select('id, name, description, price')
                ->limit(50)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search purchase invoices by number, supplier
     */
    public function searchPurchaseInvoices()
    {
        try {
            $q = $this->request->getVar('q');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('purchase_invoices');
            $builder->where('uuid_business_id', $businessUuid);

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('invoice_number', $q);
                $builder->orLike('supplier_name', $q);
                $builder->groupEnd();
            }

            $data = $builder->select('id, uuid, invoice_number, supplier_name, total, date, status')
                ->limit(50)
                ->orderBy('date', 'DESC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search sales invoices by number, customer
     */
    public function searchSalesInvoices()
    {
        try {
            $q = $this->request->getVar('q');
            $customerId = $this->request->getVar('customer_id');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('sales_invoices');
            $builder->select('sales_invoices.id, sales_invoices.uuid, sales_invoices.invoice_number, sales_invoices.custom_invoice_number, customers.company_name, sales_invoices.total, sales_invoices.date, sales_invoices.status');
            $builder->join('customers', 'customers.id = sales_invoices.customers_id', 'left');
            $builder->where('sales_invoices.uuid_business_id', $businessUuid);

            if (!empty($customerId)) {
                $builder->where('sales_invoices.customers_id', $customerId);
            }

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('sales_invoices.invoice_number', $q);
                $builder->orLike('sales_invoices.custom_invoice_number', $q);
                $builder->orLike('customers.company_name', $q);
                $builder->groupEnd();
            }

            $data = $builder->limit(50)
                ->orderBy('sales_invoices.date', 'DESC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search domains by name
     */
    public function searchDomains()
    {
        try {
            $q = $this->request->getVar('q');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('domains');
            $builder->where('uuid_business_id', $businessUuid);

            if (!empty($q)) {
                $builder->like('name', $q);
            }

            $data = $builder->select('id, name, status, expiry_date')
                ->limit(50)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search work orders by number, customer
     */
    public function searchWorkOrders()
    {
        try {
            $q = $this->request->getVar('q');
            $customerId = $this->request->getVar('customer_id');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('work_orders');
            $builder->select('work_orders.id, work_orders.order_number, work_orders.title, customers.company_name, work_orders.status');
            $builder->join('customers', 'customers.id = work_orders.customer_id', 'left');
            $builder->where('work_orders.uuid_business_id', $businessUuid);

            if (!empty($customerId)) {
                $builder->where('work_orders.customer_id', $customerId);
            }

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('work_orders.order_number', $q);
                $builder->orLike('work_orders.title', $q);
                $builder->orLike('customers.company_name', $q);
                $builder->groupEnd();
            }

            $data = $builder->limit(50)
                ->orderBy('work_orders.order_number', 'DESC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search project jobs by name, number, project
     */
    public function searchProjectJobs()
    {
        try {
            $q = $this->request->getVar('q');
            $projectUuid = $this->request->getVar('project_uuid');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('project_jobs');
            $builder->select('project_jobs.id, project_jobs.uuid, project_jobs.job_number, project_jobs.job_name, project_jobs.status, projects.name as project_name');
            $builder->join('projects', 'projects.uuid = project_jobs.uuid_project_id', 'left');
            $builder->where('project_jobs.uuid_business_id', $businessUuid);

            if (!empty($projectUuid)) {
                $builder->where('project_jobs.uuid_project_id', $projectUuid);
            }

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('project_jobs.job_name', $q);
                $builder->orLike('project_jobs.job_number', $q);
                $builder->orLike('projects.name', $q);
                $builder->groupEnd();
            }

            $data = $builder->limit(50)
                ->orderBy('project_jobs.job_number', 'DESC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Search project job phases by name, job
     */
    public function searchProjectJobPhases()
    {
        try {
            $q = $this->request->getVar('q');
            $jobUuid = $this->request->getVar('job_uuid');
            $businessUuid = session('uuid_business');

            $builder = $this->db->table('project_job_phases');
            $builder->select('project_job_phases.id, project_job_phases.uuid, project_job_phases.phase_number, project_job_phases.phase_name, project_job_phases.status, project_jobs.job_name');
            $builder->join('project_jobs', 'project_jobs.uuid = project_job_phases.uuid_project_job_id', 'left');
            $builder->where('project_job_phases.uuid_business_id', $businessUuid);

            if (!empty($jobUuid)) {
                $builder->where('project_job_phases.uuid_project_job_id', $jobUuid);
            }

            if (!empty($q)) {
                $builder->groupStart();
                $builder->like('project_job_phases.phase_name', $q);
                $builder->orLike('project_job_phases.phase_number', $q);
                $builder->orLike('project_jobs.job_name', $q);
                $builder->groupEnd();
            }

            $data = $builder->limit(50)
                ->orderBy('project_job_phases.phase_order', 'ASC')
                ->get()
                ->getResultArray();

            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->respond([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
