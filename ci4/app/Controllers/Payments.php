<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Core\Common_model;
use App\Models\Payments_model;
use App\Traits\PermissionTrait;
use stdClass;

class Payments extends CommonController
{
    use PermissionTrait;

    private $payment_model;

    function __construct()
    {
        parent::__construct();

        $this->payment_model = new Payments_model();
        $this->model = new Common_model();
        $this->table = "payments";
        $this->rawTblName = "payment";
    }

    public function index()
    {
        $this->requireReadPermission();

        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;
        $this->data['is_add_permission'] = 1;

        // Pass granular permissions to view
        $this->addPermissionsToView($this->data);

        echo view($this->table . "/list", $this->data);
    }

    public function edit($id = '')
    {
        $this->requireEditPermission($id);

        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;

        if (!empty($id)) {
            $this->data[$this->rawTblName] = $this->payment_model->getPaymentByUuid($id);
            if (empty($this->data[$this->rawTblName])) {
                session()->setFlashdata('message', 'Payment not found!');
                session()->setFlashdata('alert-class', 'alert-danger');
                return redirect()->to('/' . $this->table);
            }
            $this->data[$this->rawTblName] = (object) $this->data[$this->rawTblName];
        } else {
            $this->data[$this->rawTblName] = new stdClass();
            $this->data[$this->rawTblName]->payment_date = date('Y-m-d');
            $this->data[$this->rawTblName]->status = 'Draft';
            $this->data[$this->rawTblName]->payment_type = 'Supplier Payment';
            $this->data[$this->rawTblName]->payment_method = 'Bank Transfer';
            $this->data[$this->rawTblName]->currency = 'GBP';
        }

        // Get bank accounts (only cash and bank accounts, not all assets)
        $accountsModel = new \App\Models\Accounts_model();
        $this->data['bank_accounts'] = $accountsModel
            ->where('uuid_business_id', session('uuid_business'))
            ->where('account_type', 'Asset')
            ->where('is_active', 1)
            ->groupStart()
                ->like('account_name', 'Bank', 'both')
                ->orLike('account_name', 'Cash', 'both')
                ->orLike('account_name', 'PayPal', 'both')
                ->orLike('account_name', 'Stripe', 'both')
                ->orWhere('account_code >=', '1010')
                ->where('account_code <=', '1099')
            ->groupEnd()
            ->orderBy('account_code', 'ASC')
            ->findAll();

        // Get suppliers (using customers table as suppliers)
        $this->data['suppliers'] = $this->model->getAllDataFromTable('customers');

        echo view($this->table . "/edit", $this->data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $this->requireEditPermission($uuid, true); // true = redirect with message

        $data = $this->request->getPost();

        // Generate UUID for new payment
        if (empty($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'payments');
            $data['payment_number'] = $this->payment_model->getNextPaymentNumber(session('uuid_business'));
            $data['uuid_business_id'] = session('uuid_business');
            $data['created_by'] = session('uuid');
        }

        // Convert date format if needed
        if (!empty($data['payment_date']) && strpos($data['payment_date'], '/') !== false) {
            $data['payment_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['payment_date'])));
        }

        $response = $this->model->insertOrUpdateByUUID($uuid, $data, $this->table);

        if (!$response) {
            session()->setFlashdata('message', 'Something went wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        } else {
            session()->setFlashdata('message', 'Payment saved successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        }

        return redirect()->to('/' . $this->table);
    }

    public function delete($uuid)
    {
        $this->requireDeletePermission(true); // true = redirect with message

        $payment = $this->payment_model->where('uuid', $uuid)->first();

        if (!$payment) {
            session()->setFlashdata('message', 'Payment not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        // Don't allow deletion of posted payments
        if ($payment['is_posted']) {
            session()->setFlashdata('message', 'Cannot delete posted payment!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $this->model->deleteTableData($this->table, $uuid, 'uuid');

        session()->setFlashdata('message', 'Payment deleted successfully!');
        session()->setFlashdata('alert-class', 'alert-success');

        return redirect()->to('/' . $this->table);
    }

    public function paymentsList()
    {
        $filters = [];

        if ($this->request->getGet('status')) {
            $filters['status'] = $this->request->getGet('status');
        }

        if ($this->request->getGet('from_date')) {
            $filters['from_date'] = $this->request->getGet('from_date');
        }

        if ($this->request->getGet('to_date')) {
            $filters['to_date'] = $this->request->getGet('to_date');
        }

        $payments = $this->payment_model->getPaymentsWithDetails(session('uuid_business'), $filters);

        echo json_encode(['data' => $payments]);
    }

    public function post($uuid)
    {
        $payment = $this->payment_model->where('uuid', $uuid)->first();

        if (!$payment) {
            echo json_encode(['status' => false, 'message' => 'Payment not found!']);
            return;
        }

        if ($payment['is_posted']) {
            echo json_encode(['status' => false, 'message' => 'Payment already posted!']);
            return;
        }

        // Post to journal
        $result = $this->payment_model->postToJournal($uuid);

        if ($result) {
            // Update status to Completed
            $this->model->updateTableDataByUUID($uuid, ['status' => 'Completed'], $this->table);

            echo json_encode(['status' => true, 'message' => 'Payment posted successfully!']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to post payment!']);
        }
    }

    public function printRemittance($uuid)
    {
        $payment = $this->payment_model->getPaymentByUuid($uuid);

        if (!$payment) {
            session()->setFlashdata('message', 'Payment not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $business = getRowArray('businesses', ['uuid_business_id' => session('uuid_business')], false);

        $this->data['payment'] = (object) $payment;
        $this->data['business'] = (object) $business;

        echo view($this->table . "/remittance_pdf", $this->data);
    }

    public function downloadPDF($uuid)
    {
        $payment = $this->payment_model->getPaymentByUuid($uuid);

        if (!$payment) {
            session()->setFlashdata('message', 'Payment not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $business = getRowArray('businesses', ['uuid_business_id' => session('uuid_business')], false);

        $this->data['payment'] = (object) $payment;
        $this->data['business'] = (object) $business;

        // Generate PDF using mPDF or similar library
        $html = view($this->table . "/remittance_pdf", $this->data);

        // For now, just output HTML (PDF generation library needed)
        echo $html;
    }
}
