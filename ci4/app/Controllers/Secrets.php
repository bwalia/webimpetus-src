<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use CodeIgniter\Controller;
use App\Models\Secret_model;
use App\Models\Service_model;
use App\Controllers\Core\CommonController;
use App\Models\Core\Common_model;
use App\Libraries\UUID;

class Secrets extends CommonController
{
	protected $secretModel;
	protected $service_model;
	public function __construct()
	{
		parent::__construct();

		$this->secretModel = new Secret_model();
		$this->service_model = new Service_model();
		$this->model = new Common_model();
	}

	public function index()
	{
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['content'] = $this->secretModel->getAllSecrets();
		return view('secrets/list', $data);
	}



	public function edit($uuid = 0)
	{
		$secretsData = $uuid ? $this->secretModel->getRowsByUUID($uuid)->getRow() : "";
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['secret'] = $secretsData;
		$data['services'] = $this->service_model->getRows();
		$data['sservices'] = array_column($this->secretModel->getServices($secretsData ? $secretsData->id : $uuid), 'service_id');

		return view('secrets/edit', $data);
	}

	public function update()
	{
		$uuid = $this->request->getPost('uuid');


		$data = array(
			'key_name' => $this->request->getPost('key_name'),
			'status' => $this->request->getPost('status') ? $this->request->getPost('status') : 0,
		);

		if (strpos($this->request->getPost('key_value'), '********') === false) {
			$data['key_value'] = $this->request->getPost('key_value');
		}
		$businessUuid = $this->businessUuid;
		$data['uuid_business_id'] = $businessUuid;
		if (!$uuid || empty($uuid) || !isset($uuid)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'secrets');
        }
		$id = $this->model->insertOrUpdateByUUID($uuid, $data);

		if ($id) {

			$secret_id = $id;
			$this->secretModel->deleteService($id);
			if (!empty($this->request->getPost('sid'))) {
				foreach ($this->request->getPost('sid') as $val) {
					$this->secretModel->serviceData(array(
						'service_id' => $val,
						'secret_id' => $secret_id,
						'uuid_business_id' => $businessUuid,
					));
				}
			}
		} else {
			session()->setFlashdata('message', 'Data updated Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		}


		return redirect()->to('/secrets');
	}
}
