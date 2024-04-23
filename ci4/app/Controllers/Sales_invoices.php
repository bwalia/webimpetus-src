<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Core\Common_model;
use App\Models\Sales_invoice_model;
use stdClass;

class Sales_invoices extends CommonController
{
    private $si_model;

    function __construct()
    {
        parent::__construct();

        $this->si_model = new Sales_invoice_model();
        $this->model = new Common_model();
        @$this->sales_invoice_items = "sales_invoice_items";
        @$this->sales_invoice_notes = "sales_invoice_notes";
        @$this->sales_invoices = "sales_invoices";
    }

    public function index()
    {
        $data[$this->table] = $this->si_model->getInvoice();
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;

        echo view($this->table . "/list", $data);
    }

    public function edit($id = '')
    {
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data["users"] = $this->model->getUser();
        $data[$this->rawTblName] = $this->model->getRowsByUUID($id)->getRow();
        if (empty($id)) {
            if (empty($data[$this->rawTblName])) {
                $data[$this->rawTblName] = new stdClass();
            }
            $data[$this->rawTblName]->date = time();
            $data[$this->rawTblName]->status = 'Invoiced';
        }
        // if there any special cause we can overried this function and pass data to add or edit view
        $data['additional_data'] = $this->getAdditionalData($id);

        echo view($this->table . "/edit", $data);
    }

    public function clone($uuid = null)
    {
        $data = $this->model->getRowsByUUID($uuid)->getRowArray();
        $uuidVal = UUID::v5(UUID::v4(), 'sales_invoices');
        unset($data['id'], $data['created_at'], $data['modified_at']);
        $data['uuid'] = $uuidVal;

        $data['invoice_number'] = findMaxFieldValue($this->sales_invoices, "invoice_number");
        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = 1001;
        } else {
            $data['invoice_number'] += 1;
        }
        $data['custom_invoice_number'] = $data['custom_invoice_number'];
        $inid = $this->model->insertTableData($data, $this->sales_invoices);

        $invoice_items = $this->db->table($this->sales_invoice_items)->where('sales_invoices_uuid', $uuid)->get()->getResultArray();
        $invoice_notes = $this->db->table($this->sales_invoice_notes)->where('sales_invoices_uuid', $uuid)->get()->getResultArray();

        foreach ($invoice_items as $val) {
            unset($val['id']);
            $val['sales_invoices_uuid'] = $uuidVal;
            $val['uuid'] = UUID::v5(UUID::v4(), 'sales_invoice_items');
            $this->db->table($this->sales_invoice_items)->insert($val);
        }

        foreach ($invoice_notes as $val) {
            unset($val['id']);
            $val['sales_invoices_uuid'] = $uuidVal;
            $val['uuid'] = UUID::v5(UUID::v4(), 'sales_invoice_notes');
            $this->db->table($this->sales_invoice_notes)->insert($val);
        }

        session()->setFlashdata('message', 'Data cloned Successfully!');
        session()->setFlashdata('alert-class', 'alert-success');
        return redirect()->to($this->table . "/edit/" . $uuidVal);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $data = $this->request->getPost();
        $itemIds = @$data['item_id'];
        unset($data['item_id']);

        $data['due_date'] = strtotime($data['due_date']);
        $data['date'] = strtotime($data['date']);
        $data['paid_date'] = strtotime($data['paid_date']);

        if (empty($uuid)) {
            $data['invoice_number'] = findMaxFieldValue($this->sales_invoices, "invoice_number");
            $data['uuid'] = UUID::v5(UUID::v4(), 'work_orders');
            if (empty($data['invoice_number'])) {
                $data['invoice_number'] = 1001;
            } else {
                $data['invoice_number'] += 1;
            }
            $data['custom_invoice_number'] = $data['custom_invoice_number'] . $data['invoice_number'];
        }

        $data['is_locked'] = isset($data['is_locked']) ? 1 : 0;
        $response = $this->model->insertOrUpdateByUUID($uuid, $data);
        if (!$response) {
            session()->setFlashdata('message', 'Something wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        } else {
            if ($itemIds) {
                foreach ($itemIds as $itemId) {
                    $this->db->table($this->sales_invoice_items)->where('id', $itemId)->update(array(
                        'sales_invoices_uuid' => $data['uuid'],
                    ));
                }
            }
        }
        return redirect()->to('/' . $this->table);
    }

    public function removeInvoiceItem()
    {
        $id = $this->request->getPost('id');
        if ($id > 0) {
            $this->model->deleteTableData($this->sales_invoice_items, $id);
            $response['status'] = true;
        }
        echo json_encode($response);
    }

    public function updateInvoice()
    {
        $mainTableId = $this->request->getPost('mainTableId');
        $data['balance_due'] = $this->request->getPost('totalAmountWithTax');
        $data['total'] = $this->request->getPost('totalAmountWithTax');
        $data['total_due_with_tax'] = $this->request->getPost('totalAmountWithTax');
        $data['total_hours'] = $this->request->getPost('totalHour');
        $data['total_due'] = $this->request->getPost('totalAmount');
        $data['total_tax'] = $this->request->getPost('total_tax');

        $res = $this->model->updateTableDataByUUID($mainTableId, $data, $this->sales_invoices);

        $response['status'] = true;
        $response['msg'] = "Data updated successfully";
        $response['status'] = true;
        $response['data'] = $res;

        echo json_encode($response);
    }

    public function saveNotes()
    {
        $id = $this->request->getPost('id');
        $data['notes'] = $this->request->getPost('notes');
        $data['sales_invoices_uuid'] = $this->request->getPost('mainTableId');
        $data['uuid_business_id'] = session('uuid_business');

        if ($id > 0) {
            $res = $this->model->updateTableData($id, $data, $this->sales_invoice_notes);
        } else {
            $data['created_by'] = $_SESSION['uuid'];
            $id = $this->model->insertTableData($data, $this->sales_invoice_notes);
        }

        $response['name'] = getUserInfo()->name;
        $response['status'] = true;
        $response['msg'] = "Data updated successfully";
        $response['data'] = getRowArray($this->sales_invoice_notes, ["id" => $id]);

        echo json_encode($response);
    }

    public function addInvoiceItem()
    {
        $id = $this->request->getPost('id');
        $mainTableId = $this->request->getPost('mainTableId');
        $data['description'] = $this->request->getPost('description');
        $data['rate'] = $this->request->getPost('rate');
        $data['hours'] = $this->request->getPost('hours');
        $data['amount'] = $data['rate'] * $data['hours'];
        $data['uuid_business_id'] = session('uuid_business');

        if ($id > 0) {
            $this->model->updateTableData($id, $data, $this->sales_invoice_items);
            $response['status'] = true;
        } else {
            $data['uuid_business_id'] = session('uuid_business');
            $data['sales_invoices_uuid'] = $mainTableId;
            $data['uuid'] = UUID::v5(UUID::v4(), 'sales_invoice_items');
            $id = $this->model->insertTableData($data, $this->sales_invoice_items);
            if ($id > 0) {
                $response['msg'] = "Data added successfully";
                $response['status'] = true;
            } else {
                $response['msg'] = "Data insertion failed";
                $response['status'] = false;
            }
        }

        $response['data'] = getRowArray($this->sales_invoice_items, ["id" => $id]);

        echo json_encode($response);
    }

    public function deleteNote()
    {
        $id = $this->request->getPost('id');
        $res = $this->model->deleteTableData($this->sales_invoice_notes, $id);
        $response['id'] = $id;
        if ($res) {
            $response['status'] = true;
            $response['msg'] = "Data deleted successfully";
        } else {
            $response['status'] = false;
            $response['msg'] = "Failed";
        }
        echo json_encode($response);
    }

    public function loadBillToData()
    {
        $clientId = $this->request->getPost('clientId');
        $commonModel = new Common_model();
        $response = $commonModel->loadBillToData($clientId);
        echo json_encode($response);
    }

    public function calculateDueDate()
    {
        $term = $this->request->getPost('term');
        $currentDate = $this->request->getPost('currentDate');
        $commonModel = new Common_model();
        $response = $commonModel->calculateDueDate($term, $currentDate);
        echo json_encode($response);
    }
}
