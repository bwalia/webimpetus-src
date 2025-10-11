<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Incidents_model;
use App\Libraries\IncidentNotification;
use App\Libraries\UUID;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/incidents",
 *     tags={"Incidents"},
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
 *         description="Get all incidents"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/incidents/{uuid}",
 *     tags={"Incidents"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Incident UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single incident"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/incidents",
 *     tags={"Incidents"},
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
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="priority", type="string"),
 *             @OA\Property(property="status", type="string"),
 *             @OA\Property(property="reported_by", type="string"),
 *             @OA\Property(property="assigned_to", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Incident created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/incidents/{uuid}",
 *     tags={"Incidents"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Incident UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Incident updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/incidents/{uuid}",
 *     tags={"Incidents"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Incident UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Incident deleted"
 *     )
 * )
 */
class Incidents extends ResourceController
{
    protected $modelName = 'App\Models\Incidents_model';
    protected $format = 'json';

    public function index()
    {
        $model = new Incidents_model();
        $uuid_business_id = $this->request->getVar('uuid_business_id');

        if (!$uuid_business_id) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        $data = $model->where('uuid_business_id', $uuid_business_id)->findAll();
        return $this->respond($data);
    }

    public function show($uuid = null)
    {
        $model = new Incidents_model();
        $data = $model->where('uuid', $uuid)->first();

        if (!$data) {
            return $this->failNotFound('Incident not found');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $model = new Incidents_model();
        $data = $this->request->getJSON(true);

        if (!isset($data['uuid_business_id'])) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        // Generate UUID if not provided
        if (!isset($data['uuid'])) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'incidents_api');
        }

        // Generate incident number if not provided
        if (!isset($data['incident_number'])) {
            $count = $model->where('uuid_business_id', $data['uuid_business_id'])->countAllResults();
            $data['incident_number'] = 'INC-' . str_pad($count + 1, 6, '0', STR_PAD_LEFT);
        }

        if ($model->insert($data)) {
            // Send notifications for new incident
            try {
                $notification = new IncidentNotification();
                $notificationResults = $notification->sendIncidentNotifications($data);
                log_message('info', 'API: Incident notifications sent: ' . json_encode($notificationResults));
            } catch (\Exception $e) {
                log_message('error', 'API: Failed to send incident notifications: ' . $e->getMessage());
            }

            return $this->respondCreated(['message' => 'Incident created successfully', 'uuid' => $data['uuid']]);
        }

        return $this->fail($model->errors());
    }

    public function update($uuid = null)
    {
        $model = new Incidents_model();
        $data = $this->request->getJSON(true);

        $incident = $model->where('uuid', $uuid)->first();
        if (!$incident) {
            return $this->failNotFound('Incident not found');
        }

        if ($model->where('uuid', $uuid)->set($data)->update()) {
            return $this->respond(['message' => 'Incident updated successfully']);
        }

        return $this->fail($model->errors());
    }

    public function delete($uuid = null)
    {
        $model = new Incidents_model();

        $incident = $model->where('uuid', $uuid)->first();
        if (!$incident) {
            return $this->failNotFound('Incident not found');
        }

        if ($model->where('uuid', $uuid)->delete()) {
            return $this->respondDeleted(['message' => 'Incident deleted successfully']);
        }

        return $this->fail('Failed to delete incident');
    }
}
