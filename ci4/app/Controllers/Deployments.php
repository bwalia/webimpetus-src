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
            ->orderBy('name', 'ASC')
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

    /**
     * Check if user has deployment access for a specific environment
     */
    public function checkDeploymentAccess($uuid = null)
    {
        $deploymentUuid = $uuid ?? $this->request->getPost('deployment_uuid');
        $passcode = $this->request->getPost('passcode');

        if (!$deploymentUuid) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Deployment UUID is required'
            ]);
        }

        // Get deployment details
        $deployment = $this->deployments_model
            ->where('uuid', $deploymentUuid)
            ->where('uuid_business_id', $this->businessUuid)
            ->first();

        if (!$deployment) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Deployment not found'
            ]);
        }

        $environment = $deployment['environment'];
        $userUuid = session('uuid');

        // Check if passcode is provided
        if (!empty($passcode)) {
            $passcodeValid = $this->validatePasscode($deploymentUuid, $environment, $passcode);

            if ($passcodeValid) {
                return $this->response->setJSON([
                    'status' => true,
                    'method' => 'passcode',
                    'message' => 'Passcode validated successfully',
                    'can_deploy' => true
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Invalid or expired passcode'
                ]);
            }
        }

        // Check role-based permissions
        $hasPermission = $this->db->table('deployment_permissions')
            ->where('uuid_user_id', $userUuid)
            ->where('environment', $environment)
            ->where('uuid_business_id', $this->businessUuid)
            ->where('can_deploy', 1)
            ->where('status', 1)
            ->countAllResults() > 0;

        if ($hasPermission) {
            return $this->response->setJSON([
                'status' => true,
                'method' => 'permission',
                'message' => 'User has deployment permission',
                'can_deploy' => true
            ]);
        }

        // Check if deployment has passcodes available
        $hasPasscode = $this->db->table('deployment_passcodes')
            ->where('uuid_deployment_id', $deploymentUuid)
            ->where('environment', $environment)
            ->where('uuid_business_id', $this->businessUuid)
            ->where('status', 1)
            ->groupStart()
                ->where('expires_at >', date('Y-m-d H:i:s'))
                ->orWhere('expires_at IS NULL', null, false)
            ->groupEnd()
            ->groupStart()
                ->where('current_uses < max_uses', null, false)
            ->groupEnd()
            ->countAllResults() > 0;

        return $this->response->setJSON([
            'status' => false,
            'message' => 'Insufficient permissions to deploy to ' . $environment . ' environment',
            'can_deploy' => false,
            'requires_passcode' => $hasPasscode,
            'environment' => $environment
        ]);
    }

    /**
     * Validate deployment passcode
     */
    private function validatePasscode($deploymentUuid, $environment, $passcode)
    {
        $passcodeRecord = $this->db->table('deployment_passcodes')
            ->where('uuid_deployment_id', $deploymentUuid)
            ->where('environment', $environment)
            ->where('uuid_business_id', $this->businessUuid)
            ->where('status', 1)
            ->groupStart()
                ->where('expires_at >', date('Y-m-d H:i:s'))
                ->orWhere('expires_at IS NULL', null, false)
            ->groupEnd()
            ->groupStart()
                ->where('current_uses < max_uses', null, false)
            ->groupEnd()
            ->get()
            ->getRowArray();

        if (!$passcodeRecord) {
            return false;
        }

        // Verify passcode using password_verify
        if (password_verify($passcode, $passcodeRecord['passcode_hash'])) {
            // Increment usage counter
            $this->db->table('deployment_passcodes')
                ->where('id', $passcodeRecord['id'])
                ->set('current_uses', 'current_uses + 1', false)
                ->update();

            return true;
        }

        return false;
    }

    /**
     * Execute deployment
     */
    public function executeDeployment()
    {
        $deploymentUuid = $this->request->getPost('deployment_uuid');
        $passcode = $this->request->getPost('passcode');

        if (!$deploymentUuid) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Deployment UUID is required'
            ]);
        }

        // Get deployment details
        $deployment = $this->deployments_model
            ->where('uuid', $deploymentUuid)
            ->where('uuid_business_id', $this->businessUuid)
            ->first();

        if (!$deployment) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Deployment not found'
            ]);
        }

        $environment = $deployment['environment'];
        $userUuid = session('uuid');
        $canDeploy = false;
        $deployMethod = '';

        // Validate access via passcode
        if (!empty($passcode)) {
            if ($this->validatePasscode($deploymentUuid, $environment, $passcode)) {
                $canDeploy = true;
                $deployMethod = 'passcode';
            }
        } else {
            // Validate access via permissions
            $hasPermission = $this->db->table('deployment_permissions')
                ->where('uuid_user_id', $userUuid)
                ->where('environment', $environment)
                ->where('uuid_business_id', $this->businessUuid)
                ->where('can_deploy', 1)
                ->where('status', 1)
                ->countAllResults() > 0;

            if ($hasPermission) {
                $canDeploy = true;
                $deployMethod = 'permission';
            }
        }

        if (!$canDeploy) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Access denied: You do not have permission to deploy to ' . $environment . ' environment'
            ]);
        }

        // Update deployment status to "In Progress"
        $this->deployments_model->where('uuid', $deploymentUuid)->set([
            'deployment_status' => 'In Progress',
            'deployed_by' => $userUuid,
            'deployment_date' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s')
        ])->update();

        // Log the deployment execution
        $this->logDeploymentExecution($deploymentUuid, $userUuid, $deployMethod, $environment);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Deployment initiated successfully',
            'deployment_uuid' => $deploymentUuid,
            'environment' => $environment,
            'method' => $deployMethod
        ]);
    }

    /**
     * Log deployment execution
     */
    private function logDeploymentExecution($deploymentUuid, $userUuid, $method, $environment)
    {
        // Create deployment execution log
        $logData = [
            'uuid' => $this->generateUUID(),
            'uuid_business_id' => $this->businessUuid,
            'uuid_deployment_id' => $deploymentUuid,
            'uuid_user_id' => $userUuid,
            'environment' => $environment,
            'execution_method' => $method,
            'executed_at' => date('Y-m-d H:i:s'),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString()
        ];

        // Check if deployment_logs table exists, if not, create it
        if (!$this->db->tableExists('deployment_logs')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'uuid' => ['type' => 'VARCHAR', 'constraint' => 36],
                'uuid_business_id' => ['type' => 'VARCHAR', 'constraint' => 36],
                'uuid_deployment_id' => ['type' => 'VARCHAR', 'constraint' => 36],
                'uuid_user_id' => ['type' => 'VARCHAR', 'constraint' => 36],
                'environment' => ['type' => 'VARCHAR', 'constraint' => 50],
                'execution_method' => ['type' => 'VARCHAR', 'constraint' => 50],
                'executed_at' => ['type' => 'DATETIME'],
                'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
                'user_agent' => ['type' => 'TEXT', 'null' => true],
                'created' => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('deployment_logs', true);
        }

        $this->db->table('deployment_logs')->insert($logData);
    }

    /**
     * Manage deployment permissions (admin only)
     */
    public function managePermissions($deploymentUuid = null)
    {
        // This method would show a UI for managing who can deploy to which environments
        $this->data['page_title'] = "Deployment Permissions";
        $this->data['deployment_uuid'] = $deploymentUuid;

        // Get all users
        $this->data['users'] = $this->db->table('users')
            ->where('uuid_business_id', $this->businessUuid)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Get existing permissions
        $this->data['permissions'] = $this->db->table('deployment_permissions')
            ->where('uuid_business_id', $this->businessUuid)
            ->get()
            ->getResultArray();

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('deployments/permissions', $this->data);
    }

    /**
     * Save deployment permission
     */
    public function savePermission()
    {
        $input = $this->request->getPost();

        $data = [
            'uuid' => $input['uuid'] ?? $this->generateUUID(),
            'uuid_business_id' => $this->businessUuid,
            'uuid_user_id' => $input['uuid_user_id'],
            'environment' => $input['environment'],
            'can_deploy' => $input['can_deploy'] ?? 1,
            'granted_by' => session('uuid'),
            'granted_date' => date('Y-m-d H:i:s'),
            'notes' => $input['notes'] ?? null,
            'status' => $input['status'] ?? 1
        ];

        try {
            if (!empty($input['id'])) {
                $this->db->table('deployment_permissions')->where('id', $input['id'])->update($data);
                $message = 'Permission updated successfully';
            } else {
                $this->db->table('deployment_permissions')->insert($data);
                $message = 'Permission granted successfully';
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error saving permission: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate passcode for deployment
     */
    public function generatePasscode()
    {
        $deploymentUuid = $this->request->getPost('deployment_uuid');
        $environment = $this->request->getPost('environment');
        $expiresIn = $this->request->getPost('expires_in') ?? 24; // hours
        $maxUses = $this->request->getPost('max_uses') ?? 1;

        // Generate random 6-digit PIN
        $passcode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $data = [
            'uuid' => $this->generateUUID(),
            'uuid_business_id' => $this->businessUuid,
            'uuid_deployment_id' => $deploymentUuid,
            'environment' => $environment,
            'passcode' => $passcode, // Store plain for display (will be shown once)
            'passcode_hash' => password_hash($passcode, PASSWORD_DEFAULT),
            'expires_at' => date('Y-m-d H:i:s', strtotime("+{$expiresIn} hours")),
            'max_uses' => $maxUses,
            'current_uses' => 0,
            'created_by' => session('uuid'),
            'status' => 1
        ];

        try {
            $this->db->table('deployment_passcodes')->insert($data);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Passcode generated successfully',
                'passcode' => $passcode,
                'expires_at' => $data['expires_at'],
                'max_uses' => $maxUses
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error generating passcode: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate UUID v4
     */
    private function generateUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
