<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Core\Common_model;
use App\Models\Purchase_invoice_model;
use stdClass;

class Purchase_invoices extends CommonController
{
    private $purchase_invoice_model;
    private $purchase_invoice_items;
    private $purchase_invoice_notes;
    private $purchase_invoices;

    function __construct()
    {
        parent::__construct();

        $this->purchase_invoice_model = new Purchase_invoice_model();

        $this->purchase_invoice_items = "purchase_invoice_items";
        $this->purchase_invoice_notes = "purchase_invoice_notes";
        $this->purchase_invoices = "purchase_invoices";
    }

    public function index()
    {

        $data[$this->table] = $this->purchase_invoice_model->getInvoice();
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;

        echo view($this->table . "/list", $data);
    }

    public function purchaseInvoicesList()
	{
		$limit = (int)$this->request->getVar('limit');
		$offset = (int)$this->request->getVar('offset');
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "invoice_number";
		$dir = $this->request->getVar('dir') ?? "asc";

        $invoices = $this->purchase_invoice_model->getInvoiceRows($limit, $offset, $order, $dir, $query);

		$data = [
			'rawTblName' => $this->rawTblName,
			'tableName' => $this->table,
			'data' => $invoices['data'],
			'recordsTotal' => $invoices['total'],
		];
		return $this->response->setJSON($data);
	}

    public function clone($uuid = null)
    {
        $data = $this->model->getRowsByUUID($uuid)->getRowArray();
        $uuidVal = UUID::v5(UUID::v4(), 'purchase_invoices');
        unset($data['id'], $data['created_at'], $data['modified_at']);
        $data['uuid'] = $uuidVal;

        $data['invoice_number'] = findMaxFieldValue($this->purchase_invoices, "invoice_number");
        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = 1001;
        } else {
            $data['invoice_number'] += 1;
        }
        $data['custom_invoice_number'] = $data['custom_invoice_number'];
        $this->model->insertTableData($data, $this->purchase_invoices);

        $invoice_items = $this->db->table($this->purchase_invoice_items)->where('purchase_invoices_uuid', $uuid)->get()->getResultArray();
        $invoice_notes = $this->db->table($this->purchase_invoice_notes)->where('purchase_invoices_uuid', $uuid)->get()->getResultArray();

        foreach ($invoice_items as $val) {
            unset($val['id']);
            $val['purchase_invoices_uuid'] = $uuidVal;
            $val['uuid'] = UUID::v5(UUID::v4(), 'purchase_invoice_items');
            $this->db->table($this->purchase_invoice_items)->insert($val);
        }

        foreach ($invoice_notes as $val) {
            unset($val['id']);
            $val['purchase_invoices_uuid'] = $uuidVal;
            $val['uuid'] = UUID::v5(UUID::v4(), 'purchase_invoice_notes');
            $this->db->table($this->purchase_invoice_notes)->insert($val);
        }

        session()->setFlashdata('message', 'Data cloned Successfully!');
        session()->setFlashdata('alert-class', 'alert-success');

        return redirect()->to($this->table . "/edit/" . $uuidVal);
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
        $data['additional_data'] = $this->getAdditionalData($id);

        echo view($this->table . "/edit", $data);
    }

    public function getAdditionalData($uuid)
    {
        $data = [];
        if (!empty($uuid)) {
            $data['invoice_items'] = $this->db->table($this->purchase_invoice_items)
                ->where('purchase_invoices_uuid', $uuid)
                ->get()
                ->getResultArray();

            $data['invoice_notes'] = $this->db->table($this->purchase_invoice_notes)
                ->where('purchase_invoices_uuid', $uuid)
                ->get()
                ->getResultArray();
        } else {
            $data['invoice_items'] = [];
            $data['invoice_notes'] = [];
        }
        return $data;
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
            $data['invoice_number'] = findMaxFieldValue($this->purchase_invoices, "invoice_number");
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
                    $this->db->table($this->purchase_invoice_items)->where('id', $itemId)->update(array(
                        'purchase_invoices_uuid' => $data['uuid'],
                    ));
                }
            }
        }

        return redirect()->to('/' . $this->table);
    }

    public function removeInvoiceItem()
    {

        $id = $this->request->getPost('id');
        $mainTableId = $this->request->getPost('mainTableId');
        if ($id > 0) {
            $this->model->deleteTableData($this->purchase_invoice_items, $id);
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

        $res = $this->model->updateTableDataByUUID($mainTableId, $data, $this->purchase_invoices);

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
        $data['purchase_invoices_uuid'] = $this->request->getPost('mainTableId');

        if ($id > 0) {
            $res = $this->model->updateTableData($id, $data, $this->purchase_invoice_notes);
        } else {
            $data['created_by'] = $_SESSION['uuid'];
            $data['uuid_business_id'] = session('uuid_business');
            $id = $this->model->insertTableData($data, $this->purchase_invoice_notes);
        }

        $response['name'] = getUserInfo()->name;
        $response['status'] = true;
        $response['msg'] = "Data updated successfully";
        $response['data'] = getRowArray($this->purchase_invoice_notes, ["id" => $id]);

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
            $this->model->updateTableData($id, $data, $this->purchase_invoice_items);
            $response['status'] = true;
        } else {
            $data['uuid_business_id'] = session('uuid_business');
            $data['purchase_invoices_uuid'] = $mainTableId;
            $data['uuid'] = UUID::v5(UUID::v4(), 'purchase_invoice_items');
            $id = $this->model->insertTableData($data, $this->purchase_invoice_items);
            if ($id > 0) {
                $response['msg'] = "Data added successfully";
                $response['status'] = true;
            } else {
                $response['msg'] = "Data insertion failed";
                $response['status'] = false;
            }
        }

        $response['data'] = getRowArray($this->purchase_invoice_items, ["id" => $id]);

        echo json_encode($response);
    }

    public function deleteNote()
    {
        $id = $this->request->getPost('id');
        $res = $this->model->deleteTableData($this->purchase_invoice_notes, $id);
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
