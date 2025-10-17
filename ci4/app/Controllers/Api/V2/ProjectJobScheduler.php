<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Libraries\UUID;
use App\Models\ProjectJobScheduler_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/project_job_scheduler",
 *     tags={"Project Job Scheduler"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *          name="uuid_business_id",
 *          in="query",
 *          required=true,
 *          description="Business UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *          name="start_date",
 *          in="query",
 *          required=false,
 *          description="Filter by start date (YYYY-MM-DD)",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *          name="end_date",
 *          in="query",
 *          required=false,
 *          description="Filter by end date (YYYY-MM-DD)",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get all scheduled events"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/project_job_scheduler/{uuid}",
 *     tags={"Project Job Scheduler"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Event UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single scheduled event"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/project_job_scheduler",
 *     tags={"Project Job Scheduler"},
 *     security={{"bearerAuth": {}}},
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
 *          name="title",
 *          in="query",
 *          required=true,
 *          description="Event Title",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *          name="schedule_date",
 *          in="query",
 *          required=true,
 *          description="Schedule Date (YYYY-MM-DD)",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Create a scheduled event"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/project_job_scheduler/{uuid}",
 *     tags={"Project Job Scheduler"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Event UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Update a scheduled event"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/project_job_scheduler/{uuid}",
 *     tags={"Project Job Scheduler"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Event UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Delete a scheduled event"
 *     )
 * )
 */
class ProjectJobScheduler extends ResourceController
{
    protected $modelName = 'App\Models\ProjectJobScheduler_model';
    protected $format    = 'json';

    /**
     * Get all scheduled events
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
        $_GET['field'] = !empty($params['sort']) && !empty($params['sort']['field']) ? $params['sort']['field'] : 'schedule_date';
        $_GET['order'] = !empty($params['sort']) && !empty($params['sort']['order']) ? $params['sort']['order'] : 'ASC';

        // Filter by business uuid
        $_GET['q'] = !empty($params['filter']) && !empty($params['filter']['q']) ? $params['filter']['q'] : '';
        $_GET['uuid_business_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_business_id']) ? $params['filter']['uuid_business_id'] : $_GET['uuid_business_id'] ?? false;

        // Filter by job uuid
        $_GET['uuid_project_job_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_project_job_id']) ? $params['filter']['uuid_project_job_id'] : $_GET['uuid_project_job_id'] ?? false;

        // Filter by date range
        $_GET['start_date'] = !empty($params['filter']) && !empty($params['filter']['start_date']) ? $params['filter']['start_date'] : $_GET['start_date'] ?? false;
        $_GET['end_date'] = !empty($params['filter']) && !empty($params['filter']['end_date']) ? $params['filter']['end_date'] : $_GET['end_date'] ?? false;

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

        // Date range filtering handled separately in the query
        $model = new ProjectJobScheduler_model();
        $builder = $model->builder();

        foreach ($arr as $key => $value) {
            $builder->where($key, $value);
        }

        if (!empty($_GET['start_date'])) {
            $builder->where('schedule_date >=', $_GET['start_date']);
        }

        if (!empty($_GET['end_date'])) {
            $builder->where('schedule_date <=', $_GET['end_date']);
        }

        $total = $builder->countAllResults(false);

        $page = intval($_GET['page']);
        $perPage = intval($_GET['perPage']);
        $offset = ($page - 1) * $perPage;

        if (!empty($_GET['field'])) {
            $builder->orderBy($_GET['field'], $_GET['order']);
        }

        $results = $builder->limit($perPage, $offset)->get()->getResult();

        $data['data'] = $results;
        $data['total'] = $total;
        $data['message'] = 200;
        return $this->respond($data);
    }

    /**
     * Get a single scheduled event
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $api = new Api_v2();
        $data['data'] = $api->common_model->getRow('project_job_scheduler', $id, 'uuid');
        $data['message'] = 200;
        return $this->respond($data);
    }

    /**
     * Create a new scheduled event
     *
     * @return mixed
     */
    public function create()
    {
        $api = new Api_v2();
        $model = new ProjectJobScheduler_model();

        $requestData = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($requestData['uuid_business_id'])) {
            return $this->respond(['error' => 'Business UUID is required'], 400);
        }

        if (empty($requestData['uuid_project_job_id'])) {
            return $this->respond(['error' => 'Job UUID is required'], 400);
        }

        if (empty($requestData['title'])) {
            return $this->respond(['error' => 'Event title is required'], 400);
        }

        if (empty($requestData['schedule_date'])) {
            return $this->respond(['error' => 'Schedule date is required'], 400);
        }

        // Generate UUID
        $uuid = UUID::v5(UUID::v4(), 'project_job_scheduler');

        $data = [
            'uuid' => $uuid,
            'uuid_business_id' => $requestData['uuid_business_id'],
            'uuid_project_job_id' => $requestData['uuid_project_job_id'],
            'uuid_phase_id' => $requestData['uuid_phase_id'] ?? null,
            'assigned_to_user_id' => $requestData['assigned_to_user_id'] ?? null,
            'assigned_to_employee_id' => $requestData['assigned_to_employee_id'] ?? null,
            'schedule_date' => $requestData['schedule_date'],
            'start_time' => $requestData['start_time'] ?? null,
            'end_time' => $requestData['end_time'] ?? null,
            'all_day' => $requestData['all_day'] ?? 0,
            'duration_hours' => $requestData['duration_hours'] ?? null,
            'title' => $requestData['title'],
            'color' => $requestData['color'] ?? '#667eea',
            'notes' => $requestData['notes'] ?? null,
            'status' => $requestData['status'] ?? 'Scheduled',
            'created_by' => $requestData['created_by'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $result = $model->insert($data);

        if ($result) {
            $response['data'] = $api->common_model->getRow('project_job_scheduler', $uuid, 'uuid');
            $response['message'] = 'Event created successfully';
            return $this->respond($response, 201);
        } else {
            return $this->respond(['error' => 'Failed to create event'], 500);
        }
    }

    /**
     * Update a scheduled event
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $api = new Api_v2();
        $model = new ProjectJobScheduler_model();

        $requestData = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if (empty($id)) {
            return $this->respond(['error' => 'Event UUID is required'], 400);
        }

        $existing = $api->common_model->getRow('project_job_scheduler', $id, 'uuid');
        if (!$existing) {
            return $this->respond(['error' => 'Event not found'], 404);
        }

        $data = [
            'uuid_phase_id' => $requestData['uuid_phase_id'] ?? $existing->uuid_phase_id,
            'assigned_to_user_id' => $requestData['assigned_to_user_id'] ?? $existing->assigned_to_user_id,
            'assigned_to_employee_id' => $requestData['assigned_to_employee_id'] ?? $existing->assigned_to_employee_id,
            'schedule_date' => $requestData['schedule_date'] ?? $existing->schedule_date,
            'start_time' => $requestData['start_time'] ?? $existing->start_time,
            'end_time' => $requestData['end_time'] ?? $existing->end_time,
            'all_day' => $requestData['all_day'] ?? $existing->all_day,
            'duration_hours' => $requestData['duration_hours'] ?? $existing->duration_hours,
            'title' => $requestData['title'] ?? $existing->title,
            'color' => $requestData['color'] ?? $existing->color,
            'notes' => $requestData['notes'] ?? $existing->notes,
            'status' => $requestData['status'] ?? $existing->status,
        ];

        $result = $model->where('uuid', $id)->set($data)->update();

        if ($result) {
            $response['data'] = $api->common_model->getRow('project_job_scheduler', $id, 'uuid');
            $response['message'] = 'Event updated successfully';
            return $this->respond($response);
        } else {
            return $this->respond(['error' => 'Failed to update event'], 500);
        }
    }

    /**
     * Delete a scheduled event
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $api = new Api_v2();
        $model = new ProjectJobScheduler_model();

        if (empty($id)) {
            return $this->respond(['error' => 'Event UUID is required'], 400);
        }

        $existing = $api->common_model->getRow('project_job_scheduler', $id, 'uuid');
        if (!$existing) {
            return $this->respond(['error' => 'Event not found'], 404);
        }

        $result = $model->where('uuid', $id)->delete();

        if ($result) {
            $response['message'] = 'Event deleted successfully';
            return $this->respond($response);
        } else {
            return $this->respond(['error' => 'Failed to delete event'], 500);
        }
    }
}
