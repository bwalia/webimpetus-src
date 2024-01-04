<?php

namespace App\Controllers;

use App\Models\ServiceDomainsModel;
use CodeIgniter\Controller;
use App\Models\Domain_model;
use App\Models\Users_model;
use App\Models\Service_model;
use App\Controllers\Core\CommonController;
use App\Models\Core\Common_model;

use App\Libraries\UUID;

class Domains extends CommonController
{
	public $domainModel;
	public $user_model;
	public $service_model;
	public $serviceDomainModel;

	function __construct()
	{
		parent::__construct();
		$this->domainModel = new Domain_model();
		$this->user_model = new Users_model();
		$this->service_model = new Service_model();
		$this->serviceDomainModel = new ServiceDomainsModel();
	}

	public function index()
	{
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['domains'] = $this->domainModel->getRows();
		$data['serviceDomains'] = $this->serviceDomainModel->getRowsWithServiceName();
		
		$data['query'] = $this->db->getlastQuery();
		echo view('domains/list', $data);
	}


	public function edit($id = '')
	{
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['domain'] = !empty($id) ? $this->domainModel->getRows($id)->getRow() : [];
		$data['serviceDomains'] = $this->serviceDomainModel->getRows($id);
		$data['customers'] = $this->model->getCommonData('customers', array('uuid_business_id' => session('uuid_business'), 'email!=' => ''));
		//echo '<pre>';print_r($data['customers']); die;
		$data['services'] = $this->service_model->getRowsWithService();

		echo view($this->table . "/edit", $data);
	}


	public function update()
	{
		$id = $this->request->getPost('id');
		$domainUUID = $id;
		$data = array(
			'name'  => $this->request->getPost('name'),
			'notes' => $this->request->getPost('notes'),
			'customer_uuid' => $this->request->getPost('uuid'),
			'sid' => json_encode($this->request->getPost('sid')),
			'uuid_business_id' => session('uuid_business'),
		);
		if (empty($id)) {
			$data['uuid'] = UUID::v5(UUID::v4(), 'domains');
			$domainUUID = $data['uuid'];
		}
		$file = $this->request->getFile('file');
		if ($file && !$file->hasMoved()) {
			$data['image_logo'] = $file->getName();
		}
		
		$response = $this->model->insertOrUpdate($id, $data);
		
		$sids = $this->request->getPost('sid');
		$this->serviceDomainModel->deleteDataByDomain($domainUUID);
		if ($sids && !empty($sids)) {
			foreach ($sids as $key => $sid) {
				$isDomainExists = $this->serviceDomainModel->checkRecordExists($domainUUID, $sid);
				if (empty($isDomainExists)) {
					$serviceDomainData = [
						'uuid' =>  UUID::v5(UUID::v4(), 'service__domains'),
						'service_uuid' => $sid,
						'domain_uuid' => $domainUUID
					];
					$updateServiceRl = $this->serviceDomainModel->saveData($serviceDomainData);
					if (!$updateServiceRl) {
						session()->setFlashdata('message', 'Something wrong!');
						session()->setFlashdata('alert-class', 'alert-danger');
					}
				}
			}
		}
		if (!$response) {
			session()->setFlashdata('message', 'Something wrong!');
			session()->setFlashdata('alert-class', 'alert-danger');
		}

		return redirect()->to('/' . $this->table);
	}


	public function delete($id)
	{
		//echo $id; die;
		if (!empty($id)) {
			$response = $this->domainModel->deleteData($id);
			if ($response) {
				session()->setFlashdata('message', 'Data deleted Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
			} else {
				session()->setFlashdata('message', 'Something wrong delete failed!');
				session()->setFlashdata('alert-class', 'alert-danger');
			}
		}

		return redirect()->to('/domains');
	}
}
