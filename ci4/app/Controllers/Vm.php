<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Core\CommonController;
use App\Models\Categories;
use App\Models\VmCategoryModel;
use App\Models\VmModel;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\UUID;

class Vm extends CommonController
{
    use ResponseTrait;
    protected $table;
    protected $rawTblName;
    protected $vmModel;
    protected $vmCategoryModel;
    protected $categories;
    function __construct()
	{
		parent::__construct();
		$this->table = "vm";
		$this->rawTblName = "virtual_machines";
		$this->vmModel = new VmModel();
		$this->vmCategoryModel = new VmCategoryModel();
		$this->categories = new Categories();
	}
    public function index()
    {
        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
            'title' => 'Virtual Machines',
            'virtual_machines' => []
        ];
        echo view($this->table . "/list", $data);
    }

    public function edit($uuid = null)
    {
        $categories = $this->vmCategoryModel->categoriesByVmId($uuid)->get()->getResultArray();
        $data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['vm'] = isset($uuid) ? $this->vmModel->where('uuid', $uuid )->get()->getRow() : [];
        $data['categories'] = $categories ?? [];
		return view($this->table . '/edit', $data);
    }
    public function update()
    {
        $data = $this->request->getPost();
        $isSaved = false;
        unset($data['id']);
        unset($data['vm_categories']);
        $data['vm_tags'] = implode(',', $data['vm_tags']);

        if (!$data['uuid'] || !isset($data['uuid']) || empty($data['uuid'])) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'virtual_machines');
            $isSaved = $this->vmModel->insert($data);
        } else {
            $isSaved = $this->vmModel->set($data)->where('uuid', $data['uuid'])->update();
        }
        if ($isSaved) {
            $vmCategories = $this->request->getPost('vm_categories');
            if (!empty($vmCategories)) {
                $this->vmCategoryModel->where('vm_id', $data['uuid'])->delete();
                foreach ($vmCategories as $category) {
                    $this->vmCategoryModel->insert([
                        'vm_id' => $data['uuid'],
                        'category_id' => $category,
                        'uuid' => UUID::v5(UUID::v4(), 'vm__categories'),
                        'uuid_business_id' => session('uuid_business')
                    ]);
                }
            }
        }
        return redirect()->to('/vm');
    }

    public function vmCategories()
    {
        $q = $this->request->getVar('q');
        $data = $this->categories->where('uuid_business_id', session('uuid_business'));
        if(!empty($q)) {
            $data = $data->like('name', $q);
        }
        $data = $data->limit(500)->get()->getResult();
        return $this->respond($data);
    }
}
