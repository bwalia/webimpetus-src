<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Domain_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/domains",
 *     tags={"Domains"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="with_relations",
 *          in="query",
 *          required=false,
 *          description="Include service relations",
 *          @OA\Schema(type="boolean")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get all domains"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/domains/{id}",
 *     tags={"Domains"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Domain UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single domain"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/domains",
 *     tags={"Domains"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="customer_uuid", type="string"),
 *             @OA\Property(property="domain_path", type="string"),
 *             @OA\Property(property="domain_path_type", type="string"),
 *             @OA\Property(property="domain_service_name", type="string"),
 *             @OA\Property(property="domain_service_port", type="string"),
 *             @OA\Property(property="notes", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Domain created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/domains/{id}",
 *     tags={"Domains"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Domain UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="customer_uuid", type="string"),
 *             @OA\Property(property="domain_path", type="string"),
 *             @OA\Property(property="notes", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Domain updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/domains/{id}",
 *     tags={"Domains"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Domain UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Domain deleted"
 *     )
 * )
 */
class Domains extends ResourceController
{
    protected $modelName = 'App\Models\Domain_model';
    protected $format = 'json';

    public function index()
    {
        $model = new Domain_model();
        $withRelations = $this->request->getVar('with_relations') === 'true';
        $data = $model->getRows(false, $withRelations);
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $model = new Domain_model();
        $data = $model->getRows($id, true);

        if (!$data || $data->getNumRows() === 0) {
            return $this->failNotFound('Domain not found');
        }

        return $this->respond($data->getRowArray());
    }

    public function create()
    {
        $model = new Domain_model();
        $data = $this->request->getJSON(true);

        // Generate UUID if not provided
        if (!isset($data['uuid'])) {
            $data['uuid'] = $this->generateUUID();
        }

        if ($model->saveData($data)) {
            return $this->respondCreated(['message' => 'Domain created successfully', 'uuid' => $data['uuid']]);
        }

        return $this->fail('Failed to create domain');
    }

    public function update($id = null)
    {
        $model = new Domain_model();
        $data = $this->request->getJSON(true);

        $domain = $model->getRows($id);
        if (!$domain || $domain->getNumRows() === 0) {
            return $this->failNotFound('Domain not found');
        }

        if ($model->updateData($id, $data)) {
            return $this->respond(['message' => 'Domain updated successfully']);
        }

        return $this->fail('Failed to update domain');
    }

    public function delete($id = null)
    {
        $model = new Domain_model();

        $domain = $model->getRows($id);
        if (!$domain || $domain->getNumRows() === 0) {
            return $this->failNotFound('Domain not found');
        }

        if ($model->deleteData($id)) {
            return $this->respondDeleted(['message' => 'Domain deleted successfully']);
        }

        return $this->fail('Failed to delete domain');
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
