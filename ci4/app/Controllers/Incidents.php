<?php

namespace App\Controllers;
use App\Controllers\Core\CommonController;
use App\Models\Incidents_model;
use App\Models\Users_model;
use App\Models\Knowledge_base_model;
use App\Libraries\UUID;
use CodeIgniter\API\ResponseTrait;

class Incidents extends CommonController
{
    use ResponseTrait;
    protected $incidents_model;
    protected $table;
    protected $rawTblName;
    protected $users_model;
    protected $kb_model;

    function __construct()
    {
        parent::__construct();
        $this->incidents_model = new Incidents_model();
        $this->users_model = new Users_model();
        $this->kb_model = new Knowledge_base_model();
        $this->table = "incidents";
        $this->rawTblName = "incidents";
        $this->model = $this->incidents_model;
    }

    public function index()
    {
        $keyword = $this->request->getVar('query');
        $pager = \Config\Services::pager();
        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
            'is_add_permission' => 1,
            $this->table => $this->incidents_model->where(["uuid_business_id" => $this->businessUuid])->paginate(20),
            'pager' => $this->incidents_model->pager,
        ];

        echo view($this->table . "/list", $data);
    }

    public function incidentsList()
    {
        $limit = (int)$this->request->getVar('limit');
        $offset = (int)$this->request->getVar('offset');
        $query = $this->request->getVar('query');
        $order = $this->request->getVar('order') ?? "created_at";
        $dir = $this->request->getVar('dir') ?? "desc";

        $sqlQuery = $this->incidents_model
            ->select("uuid, id, incident_number, title, priority, status, category, created_at")
            ->where(['uuid_business_id' => session('uuid_business')]);

        if ($query) {
            $sqlQuery = $sqlQuery->groupStart()
                ->like("title", $query)
                ->orLike("incident_number", $query)
                ->orLike("category", $query)
                ->groupEnd();
        }

        $countQuery = $sqlQuery->countAllResults(false);
        $sqlQuery = $sqlQuery->limit($limit, $offset)->orderBy($order, $dir);

        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
            'data' => $sqlQuery->get()->getResultArray(),
            'recordsTotal' => $countQuery,
        ];

        return $this->response->setJSON($data);
    }

    public function edit($uuid = 0)
    {
        $incidentData = $uuid ? $this->model->getRowsByUUID($uuid)->getRow() : null;

        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data["users"] = $this->users_model->where('uuid_business_id', $this->businessUuid)->findAll();
        $data["knowledge_base"] = $this->kb_model->where(['uuid_business_id' => $this->businessUuid, 'status' => 'published'])->findAll();
        $data["customers"] = $this->db->table('customers')->where('uuid_business_id', $this->businessUuid)->get()->getResultArray();
        $data["incident"] = $incidentData;

        echo view($this->table . "/edit", $data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $data = $this->request->getPost();

        if (empty($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'incidents_saving');
            $data['uuid_business_id'] = $this->session->get('uuid_business');

            // Generate incident number
            $count = $this->incidents_model->where('uuid_business_id', $this->businessUuid)->countAllResults();
            $data['incident_number'] = 'INC-' . str_pad($count + 1, 6, '0', STR_PAD_LEFT);
        }

        // Convert date fields to proper format
        if (!empty($data['reported_date'])) {
            $data['reported_date'] = date('Y-m-d H:i:s', strtotime($data['reported_date']));
        }
        if (!empty($data['due_date'])) {
            $data['due_date'] = date('Y-m-d H:i:s', strtotime($data['due_date']));
        }
        if (!empty($data['resolved_date'])) {
            $data['resolved_date'] = date('Y-m-d H:i:s', strtotime($data['resolved_date']));
        }

        $response = $this->model->insertOrUpdateByUUID($uuid, $data);

        if ($response) {
            session()->setFlashdata('message', 'Incident saved successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        } else {
            session()->setFlashdata('message', 'Error saving incident!');
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to('/' . $this->table);
    }

    public function deleterow($uuid)
    {
        $this->model->deleteDataByUUID($uuid);
        session()->setFlashdata('message', 'Incident deleted successfully!');
        session()->setFlashdata('alert-class', 'alert-success');
        return redirect()->to('/' . $this->table);
    }
}
