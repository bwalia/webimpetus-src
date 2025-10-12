<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Tenant_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/tenants",
 *     tags={"Tenants"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid_business_id",
 *          in="query",
 *          required=false,
 *          description="Business UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get all tenants"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/tenants/{id}",
 *     tags={"Tenants"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Tenant UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single tenant"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/tenants",
 *     tags={"Tenants"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="address", type="string"),
 *             @OA\Property(property="contact_name", type="string"),
 *             @OA\Property(property="contact_email", type="string"),
 *             @OA\Property(property="contact_phone", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Tenant created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/tenants/{id}",
 *     tags={"Tenants"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Tenant UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="address", type="string"),
 *             @OA\Property(property="contact_name", type="string"),
 *             @OA\Property(property="contact_email", type="string"),
 *             @OA\Property(property="contact_phone", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Tenant updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/tenants/{id}",
 *     tags={"Tenants"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Tenant UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Tenant deleted"
 *     )
 * )
 */
class Tenants extends ResourceController
{
    protected $modelName = 'App\Models\Tenant_model';
    protected $format = 'json';

    public function index()
    {
        $model = new Tenant_model();
        $data = $model->getJoins();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $model = new Tenant_model();
        $data = $model->getRowsByUUId($id);

        if (!$data) {
            return $this->failNotFound('Tenant not found');
        }

        return $this->respond($data->getRowArray());
    }

    public function create()
    {
        $model = new Tenant_model();
        $data = $this->request->getJSON(true);

        // Generate UUID if not provided
        if (!isset($data['uuid'])) {
            $data['uuid'] = $this->generateUUID();
        }

        if ($model->saveData($data)) {
            $insertId = $model->getLastInserted();
            return $this->respondCreated(['message' => 'Tenant created successfully', 'id' => $insertId]);
        }

        return $this->fail('Failed to create tenant');
    }

    public function update($id = null)
    {
        $model = new Tenant_model();
        $data = $this->request->getJSON(true);

        $tenant = $model->getRowsByUUId($id);
        if (!$tenant || $tenant->getNumRows() === 0) {
            return $this->failNotFound('Tenant not found');
        }

        if ($model->updateDataByUUID($data, $id)) {
            return $this->respond(['message' => 'Tenant updated successfully']);
        }

        return $this->fail('Failed to update tenant');
    }

    public function delete($id = null)
    {
        $model = new Tenant_model();

        $tenant = $model->getRowsByUUId($id);
        if (!$tenant || $tenant->getNumRows() === 0) {
            return $this->failNotFound('Tenant not found');
        }

        $tenantData = $tenant->getRowArray();
        if ($model->deleteData($tenantData['id'])) {
            return $this->respondDeleted(['message' => 'Tenant deleted successfully']);
        }

        return $this->fail('Failed to delete tenant');
    }

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
}
