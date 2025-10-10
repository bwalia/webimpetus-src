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
use stdClass;

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

	/**
	 * List all domains
	 */
	public function index()
	{
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;
		$data['domains'] = $this->domainModel->getRows();
		$data['serviceDomains'] = $this->serviceDomainModel->getRowsWithServiceName();
		$data['is_add_permission'] = 1;

		echo view('domains/list', $data);
	}

	/**
	 * Get filtered domain list for DataTable
	 */
	public function domainList()
	{
		$limit = (int)$this->request->getVar('limit') ?: 10;
		$offset = (int)$this->request->getVar('offset') ?: 0;
		$query = $this->request->getVar('query');
		$order = $this->request->getVar('order') ?? "name";
		$dir = $this->request->getVar('dir') ?? "asc";

		$sqlQuery = $this->domainModel->getFilteredRows($query, $limit, $offset, $order, $dir);

		$data = [
			'rawTblName' => $this->rawTblName,
			'tableName' => $this->table,
			'data' => $sqlQuery['data'],
			'recordsTotal' => $sqlQuery['count'],
		];
		return $this->response->setJSON($data);
	}

	/**
	 * Edit/Create domain form
	 */
	public function edit($id = '')
	{
		$data['tableName'] = $this->table;
		$data['rawTblName'] = $this->rawTblName;

		if (!empty($id)) {
			$domainData = $this->domainModel->getRows($id)->getRow();
			$data['domain'] = $domainData;
		} else {
			$data['domain'] = new stdClass();
			$data['domain']->uuid = '';
			$data['domain']->name = '';
			$data['domain']->notes = '';
			$data['domain']->customer_uuid = '';
			$data['domain']->domain_path = '';
			$data['domain']->domain_path_type = '';
			$data['domain']->domain_service_name = '';
			$data['domain']->domain_service_port = '';
			$data['domain']->image_logo = '';
		}

		$data['serviceDomains'] = !empty($id) ? $this->serviceDomainModel->getRows($id) : [];
		$data['customers'] = $this->model->getCommonData('customers', array('uuid_business_id' => session('uuid_business'), 'email!=' => ''));
		$data['services'] = $this->service_model->getRowsWithService();

		echo view($this->table . "/edit", $data);
	}


	/**
	 * Update/Create domain
	 */
	public function update()
	{
		$post = $this->request->getPost();
		$id = $post['id'] ?? '';
		$domainUUID = $id;

		// Validation
		if (empty($post['name'])) {
			session()->setFlashdata('message', 'Domain name is required!');
			session()->setFlashdata('alert-class', 'alert-danger');
			return redirect()->back()->withInput();
		}

		if (empty($post['uuid'])) {
			session()->setFlashdata('message', 'Customer is required!');
			session()->setFlashdata('alert-class', 'alert-danger');
			return redirect()->back()->withInput();
		}

		// Validate domain name format
		if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})+$/', $post['name'])) {
			session()->setFlashdata('message', 'Invalid domain name format!');
			session()->setFlashdata('alert-class', 'alert-danger');
			return redirect()->back()->withInput();
		}

		$data = array(
			'name'  => trim($post['name']),
			'notes' => $post['notes'] ?? '',
			'customer_uuid' => $post['uuid'],
			'domain_path' => $post['domain_path'] ?? '',
			'domain_path_type' => $post['domain_path_type'] ?? '',
			'domain_service_name' => $post['domain_service_name'] ?? '',
			'domain_service_port' => $post['domain_service_port'] ?? '',
			'sid' => isset($post['sid']) ? json_encode($post['sid']) : null,
			'uuid_business_id' => session('uuid_business'),
		);

		// Generate UUID for new domain
		if (empty($id)) {
			$data['uuid'] = UUID::v5(UUID::v4(), 'domains');
			$domainUUID = $data['uuid'];
		}

		// Handle file upload
		$file = $this->request->getFile('file');
		if ($file && $file->isValid() && !$file->hasMoved()) {
			$newName = $file->getRandomName();
			$file->move(WRITEPATH . 'uploads/domains', $newName);
			$data['image_logo'] = 'writable/uploads/domains/' . $newName;
			$data['image_type'] = $file->getClientMimeType();
		}

		// Save domain data
		try {
			$response = $this->model->insertOrUpdateByUUID($id, $data);

			// Handle service associations
			$sids = isset($post['sid']) ? $post['sid'] : [];

			// Delete existing associations
			$this->serviceDomainModel->deleteDataByDomain($domainUUID);

			// Add new associations
			if (!empty($sids)) {
				foreach ($sids as $sid) {
					$serviceDomainData = [
						'uuid' =>  UUID::v5(UUID::v4(), 'service__domains'),
						'service_uuid' => $sid,
						'domain_uuid' => $domainUUID
					];
					$this->serviceDomainModel->saveData($serviceDomainData);
				}
			}

			session()->setFlashdata('message', 'Domain saved successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		} catch (\Exception $e) {
			log_message('error', 'Domain save error: ' . $e->getMessage());
			session()->setFlashdata('message', 'Error saving domain: ' . $e->getMessage());
			session()->setFlashdata('alert-class', 'alert-danger');
		}

		return redirect()->to('/' . $this->table);
	}


	/**
	 * Delete domain
	 */
	public function delete($id)
	{
		if (!empty($id)) {
			try {
				// Delete service associations first
				$this->serviceDomainModel->deleteDataByDomain($id);

				// Delete domain
				$response = $this->domainModel->deleteData($id);

				if ($response) {
					session()->setFlashdata('message', 'Domain deleted successfully!');
					session()->setFlashdata('alert-class', 'alert-success');
				} else {
					session()->setFlashdata('message', 'Domain deletion failed!');
					session()->setFlashdata('alert-class', 'alert-danger');
				}
			} catch (\Exception $e) {
				log_message('error', 'Domain delete error: ' . $e->getMessage());
				session()->setFlashdata('message', 'Error deleting domain: ' . $e->getMessage());
				session()->setFlashdata('alert-class', 'alert-danger');
			}
		}

		return redirect()->to('/domains');
	}

	/**
	 * Delete domain image
	 */
	public function deleteImage($id)
	{
		if (!empty($id)) {
			$domain = $this->domainModel->getRows($id)->getRow();

			if ($domain && !empty($domain->image_logo)) {
				// Delete file from filesystem
				$filePath = ROOTPATH . 'public/' . $domain->image_logo;
				if (file_exists($filePath)) {
					unlink($filePath);
				}

				// Update database
				$this->domainModel->updateData($id, ['image_logo' => null, 'image_type' => null]);

				session()->setFlashdata('message', 'Image deleted successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
			}
		}

		return redirect()->to('/domains/edit/' . $id);
	}

	/**
	 * Upload media files via AJAX
	 */
	public function uploadMediaFiles()
	{
		$file = $this->request->getFile('file');
		$id = $this->request->getPost('id');

		if ($file && $file->isValid() && !$file->hasMoved()) {
			$newName = $file->getRandomName();
			$file->move(WRITEPATH . 'uploads/domains', $newName);

			$filePath = 'writable/uploads/domains/' . $newName;

			// Update domain with new image
			if (!empty($id)) {
				$this->domainModel->updateData($id, [
					'image_logo' => $filePath,
					'image_type' => $file->getClientMimeType()
				]);
			}

			$response = [
				'status' => '1',
				'msg' => 'File uploaded successfully',
				'file_path' => '<img src="/' . $filePath . '" width="100px"><a href="/domains/deleteImage/' . $id . '" onclick="return confirm(\'Are you sure?\')" class="btn btn-danger btn-sm ml-2"><i class="fa fa-trash"></i></a>'
			];
		} else {
			$response = [
				'status' => '0',
				'msg' => 'File upload failed: ' . $file->getErrorString()
			];
		}

		return $this->response->setJSON($response);
	}
}
