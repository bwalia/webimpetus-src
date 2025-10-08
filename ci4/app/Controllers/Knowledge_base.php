<?php

namespace App\Controllers;
use App\Controllers\Core\CommonController;
use App\Models\Knowledge_base_model;
use App\Models\Users_model;
use App\Libraries\UUID;
use CodeIgniter\API\ResponseTrait;

class Knowledge_base extends CommonController
{
    use ResponseTrait;
    protected $kb_model;
    protected $table;
    protected $rawTblName;
    protected $users_model;

    function __construct()
    {
        parent::__construct();
        $this->kb_model = new Knowledge_base_model();
        $this->users_model = new Users_model();
        $this->table = "knowledge_base";
        $this->rawTblName = "knowledge_base";
        $this->model = $this->kb_model;
    }

    public function index()
    {
        $keyword = $this->request->getVar('query');
        $pager = \Config\Services::pager();
        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
            'is_add_permission' => 1,
            $this->table => $this->kb_model->where(["uuid_business_id" => $this->businessUuid])->paginate(20),
            'pager' => $this->kb_model->pager,
        ];

        echo view($this->table . "/list", $data);
    }

    public function knowledgeBaseList()
    {
        $limit = (int)$this->request->getVar('limit');
        $offset = (int)$this->request->getVar('offset');
        $query = $this->request->getVar('query');
        $order = $this->request->getVar('order') ?? "created_at";
        $dir = $this->request->getVar('dir') ?? "desc";

        $sqlQuery = $this->kb_model
            ->select("uuid, id, article_number, title, category, status, view_count, helpful_count, created_at")
            ->where(['uuid_business_id' => session('uuid_business')]);

        if ($query) {
            $sqlQuery = $sqlQuery->groupStart()
                ->like("title", $query)
                ->orLike("article_number", $query)
                ->orLike("category", $query)
                ->orLike("keywords", $query)
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
        $kbData = $uuid ? $this->model->getRowsByUUID($uuid)->getRow() : null;

        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data["users"] = $this->users_model->where('uuid_business_id', $this->businessUuid)->findAll();
        $data["article"] = $kbData;

        echo view($this->table . "/edit", $data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $data = $this->request->getPost();

        if (empty($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'knowledge_base_saving');
            $data['uuid_business_id'] = $this->session->get('uuid_business');
            $data['author_id'] = $this->session->get('uuid');

            // Generate article number
            $count = $this->kb_model->where('uuid_business_id', $this->businessUuid)->countAllResults();
            $data['article_number'] = 'KB-' . str_pad($count + 1, 6, '0', STR_PAD_LEFT);
        }

        // Convert date fields to proper format
        if (!empty($data['published_date'])) {
            $data['published_date'] = date('Y-m-d H:i:s', strtotime($data['published_date']));
        }

        // If status is published and no published date, set it now
        if ($data['status'] == 'published' && empty($data['published_date'])) {
            $data['published_date'] = date('Y-m-d H:i:s');
        }

        $response = $this->model->insertOrUpdateByUUID($uuid, $data);

        if ($response) {
            session()->setFlashdata('message', 'Knowledge base article saved successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        } else {
            session()->setFlashdata('message', 'Error saving article!');
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to('/' . $this->table);
    }

    public function deleterow($uuid)
    {
        $this->model->deleteDataByUUID($uuid);
        session()->setFlashdata('message', 'Knowledge base article deleted successfully!');
        session()->setFlashdata('alert-class', 'alert-success');
        return redirect()->to('/' . $this->table);
    }

    public function search()
    {
        $query = $this->request->getVar('q');
        $results = $this->kb_model->searchKnowledgeBase($query);
        return $this->respond($results);
    }
}
