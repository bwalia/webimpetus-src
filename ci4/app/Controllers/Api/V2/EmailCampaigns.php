<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Email_campaigns_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/email-campaigns",
 *     tags={"Email Campaigns"},
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
 *         description="Get all email campaigns"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/email-campaigns/{uuid}",
 *     tags={"Email Campaigns"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Campaign UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single email campaign"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/email-campaigns",
 *     tags={"Email Campaigns"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="subject", type="string"),
 *             @OA\Property(property="content", type="string"),
 *             @OA\Property(property="status", type="string"),
 *             @OA\Property(property="scheduled_at", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Campaign created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/email-campaigns/{uuid}",
 *     tags={"Email Campaigns"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Campaign UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Campaign updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/email-campaigns/{uuid}",
 *     tags={"Email Campaigns"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Campaign UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Campaign deleted"
 *     )
 * )
 */
class EmailCampaigns extends ResourceController
{
    protected $modelName = 'App\Models\Email_campaigns_model';
    protected $format = 'json';

    public function index()
    {
        $model = new Email_campaigns_model();
        $uuid_business_id = $this->request->getVar('uuid_business_id');

        if (!$uuid_business_id) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        $data = $model->where('uuid_business_id', $uuid_business_id)->findAll();
        return $this->respond($data);
    }

    public function show($uuid = null)
    {
        $model = new Email_campaigns_model();
        $data = $model->where('uuid', $uuid)->first();

        if (!$data) {
            return $this->failNotFound('Campaign not found');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $model = new Email_campaigns_model();
        $data = $this->request->getJSON(true);

        if (!isset($data['uuid_business_id'])) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        if ($model->insert($data)) {
            return $this->respondCreated(['message' => 'Campaign created successfully']);
        }

        return $this->fail($model->errors());
    }

    public function update($uuid = null)
    {
        $model = new Email_campaigns_model();
        $data = $this->request->getJSON(true);

        $campaign = $model->where('uuid', $uuid)->first();
        if (!$campaign) {
            return $this->failNotFound('Campaign not found');
        }

        if ($model->where('uuid', $uuid)->set($data)->update()) {
            return $this->respond(['message' => 'Campaign updated successfully']);
        }

        return $this->fail($model->errors());
    }

    public function delete($uuid = null)
    {
        $model = new Email_campaigns_model();

        $campaign = $model->where('uuid', $uuid)->first();
        if (!$campaign) {
            return $this->failNotFound('Campaign not found');
        }

        if ($model->where('uuid', $uuid)->delete()) {
            return $this->respondDeleted(['message' => 'Campaign deleted successfully']);
        }

        return $this->fail('Failed to delete campaign');
    }
}
