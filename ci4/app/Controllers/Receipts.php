<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Core\Common_model;
use App\Models\Receipts_model;
use App\Traits\PermissionTrait;
use stdClass;

class Receipts extends CommonController
{
    use PermissionTrait;

    private $receipt_model;

    function __construct()
    {
        parent::__construct();

        $this->receipt_model = new Receipts_model();
        $this->model = new Common_model();
        $this->table = "receipts";
        $this->rawTblName = "receipt";
    }

    public function index()
    {
        $this->requireReadPermission();

        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;
        $this->data['is_add_permission'] = 1;

        $this->addPermissionsToView($this->data);

        echo view($this->table . "/list", $this->data);
    }

    public function edit($id = '')
    {
        $this->requireEditPermission($id);

        $this->data['tableName'] = $this->table;
        $this->data['rawTblName'] = $this->rawTblName;

        if (!empty($id)) {
            $this->data[$this->rawTblName] = $this->receipt_model->getReceiptByUuid($id);
            if (empty($this->data[$this->rawTblName])) {
                session()->setFlashdata('message', 'Receipt not found!');
                session()->setFlashdata('alert-class', 'alert-danger');
                return redirect()->to('/' . $this->table);
            }
            $this->data[$this->rawTblName] = (object) $this->data[$this->rawTblName];
        } else {
            $this->data[$this->rawTblName] = new stdClass();
            $this->data[$this->rawTblName]->receipt_date = date('Y-m-d');
            $this->data[$this->rawTblName]->status = 'Draft';
            $this->data[$this->rawTblName]->receipt_type = 'Customer Payment';
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

        // Get customers
        $this->data['customers'] = $this->model->getAllDataFromTable('customers');

        echo view($this->table . "/edit", $this->data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $this->requireEditPermission($uuid, true);

        $data = $this->request->getPost();

        // Generate UUID for new receipt
        if (empty($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'receipts');
            $data['receipt_number'] = $this->receipt_model->getNextReceiptNumber(session('uuid_business'));
            $data['uuid_business_id'] = session('uuid_business');
            $data['created_by'] = session('uuid');
        }

        // Convert date format if needed
        if (!empty($data['receipt_date']) && strpos($data['receipt_date'], '/') !== false) {
            $data['receipt_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['receipt_date'])));
        }

        $response = $this->model->insertOrUpdateByUUID($uuid, $data, $this->table);

        if (!$response) {
            session()->setFlashdata('message', 'Something went wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        } else {
            session()->setFlashdata('message', 'Receipt saved successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        }

        return redirect()->to('/' . $this->table);
    }

    public function delete($uuid)
    {
        $this->requireDeletePermission(true);

        $receipt = $this->receipt_model->where('uuid', $uuid)->first();

        if (!$receipt) {
            session()->setFlashdata('message', 'Receipt not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        // Don't allow deletion of posted receipts
        if ($receipt['is_posted']) {
            session()->setFlashdata('message', 'Cannot delete posted receipt!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $this->model->deleteTableData($this->table, $uuid, 'uuid');

        session()->setFlashdata('message', 'Receipt deleted successfully!');
        session()->setFlashdata('alert-class', 'alert-success');

        return redirect()->to('/' . $this->table);
    }

    public function receiptsList()
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

        $receipts = $this->receipt_model->getReceiptsWithDetails(session('uuid_business'), $filters);

        echo json_encode(['data' => $receipts]);
    }

    public function post($uuid)
    {
        $receipt = $this->receipt_model->where('uuid', $uuid)->first();

        if (!$receipt) {
            echo json_encode(['status' => false, 'message' => 'Receipt not found!']);
            return;
        }

        if ($receipt['is_posted']) {
            echo json_encode(['status' => false, 'message' => 'Receipt already posted!']);
            return;
        }

        // Post to journal
        $result = $this->receipt_model->postToJournal($uuid);

        if ($result) {
            // Update status to Cleared
            $this->model->updateTableDataByUUID($uuid, ['status' => 'Cleared'], $this->table);

            echo json_encode(['status' => true, 'message' => 'Receipt posted successfully!']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to post receipt!']);
        }
    }

    public function printReceipt($uuid)
    {
        $receipt = $this->receipt_model->getReceiptByUuid($uuid);

        if (!$receipt) {
            session()->setFlashdata('message', 'Receipt not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $business = getRowArray('businesses', ['uuid_business_id' => session('uuid_business')], false);

        $this->data['receipt'] = (object) $receipt;
        $this->data['business'] = (object) $business;

        echo view($this->table . "/receipt_pdf", $this->data);
    }

    public function downloadPDF($uuid)
    {
        $receipt = $this->receipt_model->getReceiptByUuid($uuid);

        if (!$receipt) {
            session()->setFlashdata('message', 'Receipt not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $business = getRowArray('businesses', ['uuid_business_id' => session('uuid_business')], false);

        $this->data['receipt'] = (object) $receipt;
        $this->data['business'] = (object) $business;

        // Generate PDF using mPDF or similar library
        $html = view($this->table . "/receipt_pdf", $this->data);

        // For now, just output HTML (PDF generation library needed)
        echo $html;
    }
}
