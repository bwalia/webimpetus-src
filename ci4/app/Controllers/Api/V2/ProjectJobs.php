<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Libraries\UUID;
use App\Models\ProjectJobs_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/project_jobs",
 *     tags={"Project Jobs"},
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
 *     @OA\Response(
 *         response="200",
 *         description="Get all project jobs"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/project_jobs/{uuid}",
 *     tags={"Project Jobs"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Job UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single project job"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/project_jobs",
 *     tags={"Project Jobs"},
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
 *          name="uuid_project_id",
 *          in="query",
 *          required=true,
 *          description="Project UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *          name="job_name",
 *          in="query",
 *          required=true,
 *          description="Job Name",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Create a project job"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/project_jobs/{uuid}",
 *     tags={"Project Jobs"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Job UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Update a project job"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/project_jobs/{uuid}",
 *     tags={"Project Jobs"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Job UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Delete a project job"
 *     )
 * )
 */
class ProjectJobs extends ResourceController
{
    protected $modelName = 'App\Models\ProjectJobs_model';
    protected $format    = 'json';

    /**
     * Get all project jobs
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
        $_GET['field'] = !empty($params['sort']) && !empty($params['sort']['field']) ? $params['sort']['field'] : 'created_at';
        $_GET['order'] = !empty($params['sort']) && !empty($params['sort']['order']) ? $params['sort']['order'] : 'DESC';

        // Filter by business uuid
        $_GET['q'] = !empty($params['filter']) && !empty($params['filter']['q']) ? $params['filter']['q'] : '';
        $_GET['uuid_business_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_business_id']) ? $params['filter']['uuid_business_id'] : $_GET['uuid_business_id'] ?? false;

        // Filter by project uuid
        $_GET['uuid_project_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_project_id']) ? $params['filter']['uuid_project_id'] : $_GET['uuid_project_id'] ?? false;

        // Filter by status
        $_GET['status'] = !empty($params['filter']) && !empty($params['filter']['status']) ? $params['filter']['status'] : '';

        $arr = [];
        if (!empty($_GET['uuid_business_id'])) {
            $arr['uuid_business_id'] = $_GET['uuid_business_id'];
        } else {
            $data['data'] = 'You must specify the Business UUID';
            return $this->respond($data, 403);
        }

        if (!empty($_GET['uuid_project_id'])) {
            $arr['uuid_project_id'] = $_GET['uuid_project_id'];
        }

        if (!empty($_GET['status'])) {
            $arr['status'] = $_GET['status'];
        }

        $data['data'] = $api->common_model->getApiData('project_jobs', $arr);
        $data['total'] = $api->common_model->getCount('project_jobs', $arr);
        $data['message'] = 200;
        return $this->respond($data);
    }

    /**
     * Get a single project job
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $api = new Api_v2();
        $data['data'] = $api->common_model->getRow('project_jobs', $id, 'uuid');
        $data['message'] = 200;
        return $this->respond($data);
    }

    /**
     * Create a new project job
     *
     * @return mixed
     */
    public function create()
    {
        $api = new Api_v2();
        $model = new ProjectJobs_model();

        $requestData = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($requestData['uuid_business_id'])) {
            return $this->respond(['error' => 'Business UUID is required'], 400);
        }

        if (empty($requestData['uuid_project_id'])) {
            return $this->respond(['error' => 'Project UUID is required'], 400);
        }

        if (empty($requestData['job_name'])) {
            return $this->respond(['error' => 'Job name is required'], 400);
        }

        // Generate UUID and job number
        $uuid = UUID::v5(UUID::v4(), 'project_jobs');
        $jobNumber = $model->getNextJobNumber($requestData['uuid_business_id'], $requestData['uuid_project_id']);

        $data = [
            'uuid' => $uuid,
            'uuid_business_id' => $requestData['uuid_business_id'],
            'uuid_project_id' => $requestData['uuid_project_id'],
            'job_number' => $jobNumber,
            'job_name' => $requestData['job_name'],
            'job_description' => $requestData['job_description'] ?? null,
            'job_type' => $requestData['job_type'] ?? 'Development',
            'priority' => $requestData['priority'] ?? 'Normal',
            'status' => $requestData['status'] ?? 'Planning',
            'assigned_to_user_id' => $requestData['assigned_to_user_id'] ?? null,
            'assigned_to_employee_id' => $requestData['assigned_to_employee_id'] ?? null,
            'planned_start_date' => $requestData['planned_start_date'] ?? null,
            'planned_end_date' => $requestData['planned_end_date'] ?? null,
            'estimated_hours' => $requestData['estimated_hours'] ?? null,
            'estimated_cost' => $requestData['estimated_cost'] ?? null,
            'billable' => $requestData['billable'] ?? 1,
            'hourly_rate' => $requestData['hourly_rate'] ?? null,
            'completion_percentage' => $requestData['completion_percentage'] ?? 0,
            'notes' => $requestData['notes'] ?? null,
            'created_by' => $requestData['created_by'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s'),
        ];

        $result = $model->insert($data);

        if ($result) {
            $response['data'] = $api->common_model->getRow('project_jobs', $uuid, 'uuid');
            $response['message'] = 'Job created successfully';
            return $this->respond($response, 201);
        } else {
            return $this->respond(['error' => 'Failed to create job'], 500);
        }
    }

    /**
     * Update a project job
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $api = new Api_v2();
        $model = new ProjectJobs_model();

        $requestData = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if (empty($id)) {
            return $this->respond(['error' => 'Job UUID is required'], 400);
        }

        $existing = $api->common_model->getRow('project_jobs', $id, 'uuid');
        if (!$existing) {
            return $this->respond(['error' => 'Job not found'], 404);
        }

        $data = [
            'job_name' => $requestData['job_name'] ?? $existing->job_name,
            'job_description' => $requestData['job_description'] ?? $existing->job_description,
            'job_type' => $requestData['job_type'] ?? $existing->job_type,
            'priority' => $requestData['priority'] ?? $existing->priority,
            'status' => $requestData['status'] ?? $existing->status,
            'assigned_to_user_id' => $requestData['assigned_to_user_id'] ?? $existing->assigned_to_user_id,
            'assigned_to_employee_id' => $requestData['assigned_to_employee_id'] ?? $existing->assigned_to_employee_id,
            'planned_start_date' => $requestData['planned_start_date'] ?? $existing->planned_start_date,
            'planned_end_date' => $requestData['planned_end_date'] ?? $existing->planned_end_date,
            'actual_start_date' => $requestData['actual_start_date'] ?? $existing->actual_start_date,
            'actual_end_date' => $requestData['actual_end_date'] ?? $existing->actual_end_date,
            'estimated_hours' => $requestData['estimated_hours'] ?? $existing->estimated_hours,
            'estimated_cost' => $requestData['estimated_cost'] ?? $existing->estimated_cost,
            'billable' => $requestData['billable'] ?? $existing->billable,
            'hourly_rate' => $requestData['hourly_rate'] ?? $existing->hourly_rate,
            'completion_percentage' => $requestData['completion_percentage'] ?? $existing->completion_percentage,
            'notes' => $requestData['notes'] ?? $existing->notes,
            'modified_at' => date('Y-m-d H:i:s'),
        ];

        $result = $model->where('uuid', $id)->set($data)->update();

        if ($result) {
            $response['data'] = $api->common_model->getRow('project_jobs', $id, 'uuid');
            $response['message'] = 'Job updated successfully';
            return $this->respond($response);
        } else {
            return $this->respond(['error' => 'Failed to update job'], 500);
        }
    }

    /**
     * Delete a project job
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $api = new Api_v2();
        $model = new ProjectJobs_model();

        if (empty($id)) {
            return $this->respond(['error' => 'Job UUID is required'], 400);
        }

        $existing = $api->common_model->getRow('project_jobs', $id, 'uuid');
        if (!$existing) {
            return $this->respond(['error' => 'Job not found'], 404);
        }

        $result = $model->where('uuid', $id)->delete();

        if ($result) {
            $response['message'] = 'Job deleted successfully';
            return $this->respond($response);
        } else {
            return $this->respond(['error' => 'Failed to delete job'], 500);
        }
    }
}
