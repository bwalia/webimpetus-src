<?php

namespace App\Models;

use CodeIgniter\Model;

class Deployments_model extends Model
{
    protected $table = 'deployments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'uuid',
        'uuid_business_id',
        'deployment_name',
        'uuid_service_id',
        'environment',
        'version',
        'deployment_type',
        'deployment_status',
        'deployment_date',
        'completed_date',
        'deployed_by',
        'uuid_task_id',
        'uuid_incident_id',
        'description',
        'deployment_notes',
        'rollback_plan',
        'affected_components',
        'downtime_required',
        'downtime_start',
        'downtime_end',
        'git_commit_hash',
        'git_branch',
        'repository_url',
        'deployment_url',
        'health_check_url',
        'health_check_status',
        'approval_required',
        'approved_by',
        'approval_date',
        'priority',
        'status',
        'created',
        'modified'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created';
    protected $updatedField = 'modified';

    // Validation
    protected $validationRules = [
        'deployment_name' => 'required|min_length[3]|max_length[255]',
        'uuid_business_id' => 'required',
        'environment' => 'required|in_list[Development,Testing,Acceptance,Production]',
        'deployment_status' => 'in_list[Planned,In Progress,Completed,Failed,Rolled Back]',
    ];

    protected $validationMessages = [
        'deployment_name' => [
            'required' => 'Deployment name is required',
            'min_length' => 'Deployment name must be at least 3 characters',
        ],
        'environment' => [
            'required' => 'Environment is required',
            'in_list' => 'Invalid environment selected',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateUuid'];
    protected $beforeUpdate = [];
    protected $afterInsert = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Generate UUID before insert
     */
    protected function generateUuid(array $data)
    {
        if (!isset($data['data']['uuid'])) {
            $data['data']['uuid'] = $this->generateUUID();
        }
        return $data;
    }

    /**
     * Generate UUID v4
     */
    private function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Get deployments with related service, task, and incident information
     */
    public function getDeploymentsWithRelations($uuid_business_id, $filters = [])
    {
        $builder = $this->db->table($this->table . ' d');
        $builder->select('d.*, s.name as service_name, s.code as service_code,
                         t.title as task_title, i.title as incident_title,
                         u1.name as deployed_by_name, u2.name as approved_by_name');
        $builder->join('services s', 's.uuid = d.uuid_service_id', 'left');
        $builder->join('tasks t', 't.uuid = d.uuid_task_id', 'left');
        $builder->join('incidents i', 'i.uuid = d.uuid_incident_id', 'left');
        $builder->join('users u1', 'u1.uuid = d.deployed_by', 'left');
        $builder->join('users u2', 'u2.uuid = d.approved_by', 'left');
        $builder->where('d.uuid_business_id', $uuid_business_id);

        // Apply filters
        if (!empty($filters['environment'])) {
            $builder->where('d.environment', $filters['environment']);
        }
        if (!empty($filters['deployment_status'])) {
            $builder->where('d.deployment_status', $filters['deployment_status']);
        }
        if (!empty($filters['uuid_service_id'])) {
            $builder->where('d.uuid_service_id', $filters['uuid_service_id']);
        }

        $builder->orderBy('d.deployment_date', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get deployment statistics by environment
     */
    public function getDeploymentStats($uuid_business_id)
    {
        $builder = $this->db->table($this->table);
        $builder->select('environment, deployment_status, COUNT(*) as count');
        $builder->where('uuid_business_id', $uuid_business_id);
        $builder->groupBy(['environment', 'deployment_status']);

        return $builder->get()->getResultArray();
    }

    /**
     * Get recent deployments
     */
    public function getRecentDeployments($uuid_business_id, $limit = 10)
    {
        return $this->where('uuid_business_id', $uuid_business_id)
                    ->orderBy('deployment_date', 'DESC')
                    ->limit($limit)
                    ->find();
    }
}
