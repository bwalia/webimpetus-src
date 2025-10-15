<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Libraries\UUID;
use App\Models\ProjectJobPhases_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/project_job_phases",
 *     tags={"Project Job Phases"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid_business_id",
 *          in="query",
 *          required=true,
 *          description="Business UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *          name="uuid_project_job_id",
 *          in="query",
 *          required=false,
 *          description="Job UUID to filter phases",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get all project job phases"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/project_job_phases/{uuid}",
 *     tags={"Project Job Phases"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Phase UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single project job phase"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/project_job_phases",
 *     tags={"Project Job Phases"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid_business_id",
 *          in="query",
 *          required=true,
 *          description="Business UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *          name="uuid_project_job_id",
 *          in="query",
 *          required=true,
 *          description="Job UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *          name="phase_name",
 *          in="query",
 *          required=true,
 *          description="Phase Name",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Create a project job phase"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/project_job_phases/{uuid}",
 *     tags={"Project Job Phases"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Phase UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Update a project job phase"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/project_job_phases/{uuid}",
 *     tags={"Project Job Phases"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Phase UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Delete a project job phase"
 *     )
 * )
 */
class ProjectJobPhases extends ResourceController
{
    protected $modelName = 'App\Models\ProjectJobPhases_model';
    protected $format    = 'json';

    /**
     * Get all project job phases
     *
     * @return mixed
     */
    public function index()
    {
        $api = new Api_v2();
        $params = !empty($_GET['params']) ? json_decode($_GET['params'], true) : [];

        // Pagination Params
        $_GET['page'] = !empty($params['pagination']) && !empty($params['pagination']['page']) ? $params['pagination']['page'] : 1;
        $_GET['perPage'] = !empty($params['pagination']) && !empty($params['pagination']['perPage']) ? $params['pagination']['perPage'] : 10;

        // Sorting params
        $_GET['field'] = !empty($params['sort']) && !empty($params['sort']['field']) ? $params['sort']['field'] : 'phase_order';
        $_GET['order'] = !empty($params['sort']) && !empty($params['sort']['order']) ? $params['sort']['order'] : 'ASC';

        // Filter by business uuid
        $_GET['q'] = !empty($params['filter']) && !empty($params['filter']['q']) ? $params['filter']['q'] : '';
        $_GET['uuid_business_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_business_id']) ? $params['filter']['uuid_business_id'] : $_GET['uuid_business_id'] ?? false;

        // Filter by job uuid
        $_GET['uuid_project_job_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_project_job_id']) ? $params['filter']['uuid_project_job_id'] : $_GET['uuid_project_job_id'] ?? false;

        // Filter by status
        $_GET['status'] = !empty($params['filter']) && !empty($params['filter']['status']) ? $params['filter']['status'] : '';

        $arr = [];
        if (!empty($_GET['uuid_business_id'])) {
            $arr['uuid_business_id'] = $_GET['uuid_business_id'];
        } else {
            $data['data'] = 'You must specify the Business UUID';
            return $this->respond($data, 403);
        }

        if (!empty($_GET['uuid_project_job_id'])) {
            $arr['uuid_project_job_id'] = $_GET['uuid_project_job_id'];
        }

        if (!empty($_GET['status'])) {
            $arr['status'] = $_GET['status'];
        }

        $data['data'] = $api->common_model->getApiData('project_job_phases', $arr);
        $data['total'] = $api->common_model->getCount('project_job_phases', $arr);
        $data['message'] = 200;
        return $this->respond($data);
    }

    /**
     * Get a single project job phase
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $api = new Api_v2();
        $data['data'] = $api->common_model->getRow('project_job_phases', $id, 'uuid');
        $data['message'] = 200;
        return $this->respond($data);
    }

    /**
     * Create a new project job phase
     *
     * @return mixed
     */
    public function create()
    {
        $api = new Api_v2();
        $model = new ProjectJobPhases_model();

        $requestData = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($requestData['uuid_business_id'])) {
            return $this->respond(['error' => 'Business UUID is required'], 400);
        }

        if (empty($requestData['uuid_project_job_id'])) {
            return $this->respond(['error' => 'Job UUID is required'], 400);
        }

        if (empty($requestData['phase_name'])) {
            return $this->respond(['error' => 'Phase name is required'], 400);
        }

        // Generate UUID and phase number
        $uuid = UUID::v5(UUID::v4(), 'project_job_phases');
        $phaseNumber = $model->getNextPhaseNumber($requestData['uuid_business_id'], $requestData['uuid_project_job_id']);

        $data = [
            'uuid' => $uuid,
            'uuid_business_id' => $requestData['uuid_business_id'],
            'uuid_project_job_id' => $requestData['uuid_project_job_id'],
            'phase_number' => $phaseNumber,
            'phase_name' => $requestData['phase_name'],
            'phase_description' => $requestData['phase_description'] ?? null,
            'phase_order' => $requestData['phase_order'] ?? 1,
            'status' => $requestData['status'] ?? 'Not Started',
            'assigned_to_user_id' => $requestData['assigned_to_user_id'] ?? null,
            'assigned_to_employee_id' => $requestData['assigned_to_employee_id'] ?? null,
            'planned_start_date' => $requestData['planned_start_date'] ?? null,
            'planned_end_date' => $requestData['planned_end_date'] ?? null,
            'estimated_hours' => $requestData['estimated_hours'] ?? null,
            'depends_on_phase_uuid' => $requestData['depends_on_phase_uuid'] ?? null,
            'completion_percentage' => $requestData['completion_percentage'] ?? 0,
            'deliverables' => $requestData['deliverables'] ?? null,
            'acceptance_criteria' => $requestData['acceptance_criteria'] ?? null,
            'notes' => $requestData['notes'] ?? null,
            'created_by' => $requestData['created_by'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s'),
        ];

        $result = $model->insert($data);

        if ($result) {
            $response['data'] = $api->common_model->getRow('project_job_phases', $uuid, 'uuid');
            $response['message'] = 'Phase created successfully';
            return $this->respond($response, 201);
        } else {
            return $this->respond(['error' => 'Failed to create phase'], 500);
        }
    }

    /**
     * Update a project job phase
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $api = new Api_v2();
        $model = new ProjectJobPhases_model();

        $requestData = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if (empty($id)) {
            return $this->respond(['error' => 'Phase UUID is required'], 400);
        }

        $existing = $api->common_model->getRow('project_job_phases', $id, 'uuid');
        if (!$existing) {
            return $this->respond(['error' => 'Phase not found'], 404);
        }

        $data = [
            'phase_name' => $requestData['phase_name'] ?? $existing->phase_name,
            'phase_description' => $requestData['phase_description'] ?? $existing->phase_description,
            'phase_order' => $requestData['phase_order'] ?? $existing->phase_order,
            'status' => $requestData['status'] ?? $existing->status,
            'assigned_to_user_id' => $requestData['assigned_to_user_id'] ?? $existing->assigned_to_user_id,
            'assigned_to_employee_id' => $requestData['assigned_to_employee_id'] ?? $existing->assigned_to_employee_id,
            'planned_start_date' => $requestData['planned_start_date'] ?? $existing->planned_start_date,
            'planned_end_date' => $requestData['planned_end_date'] ?? $existing->planned_end_date,
            'actual_start_date' => $requestData['actual_start_date'] ?? $existing->actual_start_date,
            'actual_end_date' => $requestData['actual_end_date'] ?? $existing->actual_end_date,
            'estimated_hours' => $requestData['estimated_hours'] ?? $existing->estimated_hours,
            'depends_on_phase_uuid' => $requestData['depends_on_phase_uuid'] ?? $existing->depends_on_phase_uuid,
            'completion_percentage' => $requestData['completion_percentage'] ?? $existing->completion_percentage,
            'deliverables' => $requestData['deliverables'] ?? $existing->deliverables,
            'acceptance_criteria' => $requestData['acceptance_criteria'] ?? $existing->acceptance_criteria,
            'notes' => $requestData['notes'] ?? $existing->notes,
            'modified_at' => date('Y-m-d H:i:s'),
        ];

        $result = $model->where('uuid', $id)->set($data)->update();

        if ($result) {
            $response['data'] = $api->common_model->getRow('project_job_phases', $id, 'uuid');
            $response['message'] = 'Phase updated successfully';
            return $this->respond($response);
        } else {
            return $this->respond(['error' => 'Failed to update phase'], 500);
        }
    }

    /**
     * Delete a project job phase
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $api = new Api_v2();
        $model = new ProjectJobPhases_model();

        if (empty($id)) {
            return $this->respond(['error' => 'Phase UUID is required'], 400);
        }

        $existing = $api->common_model->getRow('project_job_phases', $id, 'uuid');
        if (!$existing) {
            return $this->respond(['error' => 'Phase not found'], 404);
        }

        $result = $model->where('uuid', $id)->delete();

        if ($result) {
            $response['message'] = 'Phase deleted successfully';
            return $this->respond($response);
        } else {
            return $this->respond(['error' => 'Failed to delete phase'], 500);
        }
    }
}
