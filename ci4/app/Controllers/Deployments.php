<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Deployments_model;

class Deployments extends CommonController
{
    protected $deployments_model;

    public function __construct()
    {
        parent::__construct();
        $this->deployments_model = new Deployments_model();
    }

    /**
     * List all deployments
     */
    public function index()
    {
        $this->data['page_title'] = "Deployments";
        $this->data['tableName'] = "deployments";

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('deployments/list', $this->data);
    }

    /**
     * Edit/Add deployment
     */
    public function edit($uuid = null)
    {
        $this->data['page_title'] = $uuid ? "Edit Deployment" : "Add Deployment";
        $this->data['tableName'] = "deployments";

        // Get deployment data if editing
        if ($uuid) {
            $deployment = $this->deployments_model
                ->where('uuid', $uuid)
                ->where('uuid_business_id', $this->businessUuid)
                ->first();

            if (!$deployment) {
                return redirect()->to('/deployments')->with('error', 'Deployment not found');
            }

            $this->data['deployment'] = (object) $deployment;
        } else {
            $this->data['deployment'] = (object) [];
        }

        // Get services for dropdown
        $this->data['services'] = $this->db->table('services')
            ->where('uuid_business_id', $this->businessUuid)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Get tasks for dropdown
        $this->data['tasks'] = $this->db->table('tasks')
            ->where('uuid_business_id', $this->businessUuid)
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        // Get incidents for dropdown
        $this->data['incidents'] = $this->db->table('incidents')
            ->where('uuid_business_id', $this->businessUuid)
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        // Get users for dropdown
        $this->data['users'] = $this->db->table('users')
            ->where('uuid_business_id', $this->businessUuid)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('deployments/edit', $this->data);
    }

    /**
     * Update/Insert deployment
     */
    public function update()
    {
        $input = $this->request->getPost();

        // Add business UUID
        $input['uuid_business_id'] = $this->businessUuid;

        // Handle checkboxes
        $input['downtime_required'] = $this->request->getPost('downtime_required') ? 1 : 0;
        $input['approval_required'] = $this->request->getPost('approval_required') ? 1 : 0;
        $input['status'] = $this->request->getPost('status') ? 1 : 0;

        // Set timestamps
        if (empty($input['uuid'])) {
            $input['created'] = date('Y-m-d H:i:s');
        }
        $input['modified'] = date('Y-m-d H:i:s');

        try {
            if (!empty($input['uuid'])) {
                // Update existing deployment
                $this->deployments_model->where('uuid', $input['uuid'])->set($input)->update();
                $message = 'Deployment updated successfully';
            } else {
                // Insert new deployment
                $this->deployments_model->insert($input);
                $message = 'Deployment created successfully';
            }

            return redirect()->to('/deployments')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error saving deployment: ' . $e->getMessage());
        }
    }

    /**
     * Delete deployment
     */
    public function delete($uuid)
    {
        try {
            $this->deployments_model
                ->where('uuid', $uuid)
                ->where('uuid_business_id', $this->businessUuid)
                ->delete();

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Deployment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error deleting deployment: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get deployment list for DataTables
     */
    public function deploymentsList()
    {
        $deployments = $this->deployments_model->getDeploymentsWithRelations($this->businessUuid);

        return $this->response->setJSON([
            'data' => $deployments
        ]);
    }
}
