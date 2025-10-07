<?php

namespace App\Controllers;

use App\Models\Tenant_model;
use App\Models\Users_model;
use App\Models\Service_model;
use App\Models\Tenant_service_model;
use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Core\Common_model;

class Tenants extends CommonController
{
	protected $tenantModel;
	protected $user_model;
	protected $service_model;
	protected $tservice_model;
	function __construct()
	{
		parent::__construct();
		$this->tenantModel = new Tenant_model();
		$this->user_model = new Users_model();
		$this->service_model = new Service_model();
		$this->tservice_model = new Tenant_service_model();
		//$this->CI = &get_instance();
	}
	public function index()
	{
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;

		$data[$this->table] = $this->tenantModel->getJoins();
		echo view('tenants/list', $data);
	}

	public function tenantsList()
    {
        $limit = (int)$this->request->getVar('limit');
        $offset = (int)$this->request->getVar('offset');
        $query = $this->request->getVar('query');
        $order = $this->request->getVar('order') ?? "name";
        $dir = $this->request->getVar('dir') ?? "asc";

        $sqlQuery = $this->tenantModel
        	->join('tenants_services', 'tenants_services.tid = tenants.id', 'LEFT')
        	->join('services', 'tenants_services.sid = services.id', 'LEFT')
        	->groupBy('tenants.id')
        	->select('GROUP_CONCAT(services.name) as service_name')
        	->select('tenants.*')
			->where(['tenants.uuid_business_id' => session('uuid_business')]);
        if ($query) {
            $sqlQuery = $sqlQuery
                ->like("tenants.name", $query);
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


	public function save()
	{
		//echo '<pre>';print_r($this->request->getPost('name')); die;        
		if (!empty($this->request->getPost('contact_email'))) {

			$count = $this->tenantModel->getWhere(['contact_email' => $this->request->getPost('contact_email')])->getNumRows();
			if (!empty($count)) {
				session()->setFlashdata('message', 'Email already exist!');
				session()->setFlashdata('alert-class', 'alert-danger');
				return redirect()->to('/tenants/edit');
			} else {
				$data = array(
					'name'  => $this->request->getPost('name'),
					'contact_email' => $this->request->getPost('contact_email'),
					'contact_name' => $this->request->getPost('contact_name'),
					'address' => $this->request->getPost('address'),
					'notes' => $this->request->getPost('notes'),
					'user_uuid' => $this->request->getPost('user_uuid'),
					'uuid_business_id' => session('uuid_business'),
					//'status' => 0,
				);
				if (!isset($data['uuid']) || empty($data['uuid'])) {
					$data['uuid'] = UUID::v5(UUID::v4(), 'tenants');
				}
				$this->tenantModel->saveData($data);
				$tid = $this->tenantModel->getLastInserted(); //die;
				if (!empty($this->request->getPost('sid'))) {
					foreach ($this->request->getPost('sid') as $val) {
						$this->tservice_model->saveData(array(
							'sid' => $val,
							'tid' => $tid,
							'uuid_business_id' => session('uuid_business'),
						));
					}
				}
				session()->setFlashdata('message', 'Data entered Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
			}
		}
		return redirect()->to('/tenants');
	}

	public function edit($uuid = 0)
	{
		$tenantData = $uuid ? $this->tenantModel->getRowsByUUId($uuid)->getRow() : "";
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['tenant'] = $tenantData;
		$data['users'] = $this->user_model->getUser();
		$data['services'] = $this->service_model->getRows();
		$data['tservices'] = array_column($this->tservice_model->getRows($tenantData ? $tenantData->id : $uuid, 1), 'sid');
		$data['actionUrl'] = (empty($uuid)) ? '/tenants/save' : '/tenants/update';
		echo view($this->table . "/edit", $data);
	}

	public function update()
	{
		$uuid = $this->request->getPost('uuid');
		$id = $this->request->getPost('id');

		$count = $this->tenantModel->getWhere(['contact_email' => $this->request->getPost('contact_email'), 'id!=' => $id])->getNumRows();
		if (!empty($count)) {
			session()->setFlashdata('message', 'Email already exist!');
			session()->setFlashdata('alert-class', 'alert-danger');
			return redirect()->to('/tenants/edit/' . $uuid);
		} else {
			$data = array(
				'name'  => $this->request->getPost('name'),
				'contact_email' => $this->request->getPost('contact_email'),
				'contact_name' => $this->request->getPost('contact_name'),
				'address' => $this->request->getPost('address'),
				'notes' => $this->request->getPost('notes'),
				'user_uuid' => $this->request->getPost('user_uuid'),
				//'status' => 0,
			);
			
			$this->tenantModel->updateDataByUUID($data, $uuid);
			$tid = $id;
			$this->tservice_model->deleteData($id);
			if (!empty($this->request->getPost('sid'))) {
				foreach ($this->request->getPost('sid') as $val) {
					$this->tservice_model->saveData(array(
						'sid' => $val,
						'tid' => $tid,
						'uuid_business_id' => session('uuid_business'),
					));
				}
			}
			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}
		return redirect()->to('/tenants');
	}

	public function status()
	{
		if (!empty($id = $this->request->getPost('id'))) {
			$data = array(
				'status' => $this->request->getPost('status')
			);
			$this->tenantModel->updateUser($data, $id);
		}
		echo '1';
	}
}
