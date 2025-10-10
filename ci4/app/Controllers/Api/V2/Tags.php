<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Tags_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/tags",
 *     tags={"Tags"},
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
 *         description="Get all tags"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/tags/{id}",
 *     tags={"Tags"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Tag ID",
 *          @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single tag"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/tags",
 *     tags={"Tags"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="color", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Tag created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/tags/{id}",
 *     tags={"Tags"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Tag ID",
 *          @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Tag updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/tags/{id}",
 *     tags={"Tags"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Tag ID",
 *          @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Tag deleted"
 *     )
 * )
 */
class Tags extends ResourceController
{
    protected $modelName = 'App\Models\Tags_model';
    protected $format = 'json';

    public function index()
    {
        $model = new Tags_model();
        $uuid_business_id = $this->request->getVar('uuid_business_id');

        if (!$uuid_business_id) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        $data = $model->where('uuid_business_id', $uuid_business_id)->findAll();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $model = new Tags_model();
        $data = $model->find($id);

        if (!$data) {
            return $this->failNotFound('Tag not found');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $model = new Tags_model();
        $data = $this->request->getJSON(true);

        if (!isset($data['uuid_business_id'])) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        if ($model->insert($data)) {
            return $this->respondCreated(['message' => 'Tag created successfully']);
        }

        return $this->fail($model->errors());
    }

    public function update($id = null)
    {
        $model = new Tags_model();
        $data = $this->request->getJSON(true);

        $tag = $model->find($id);
        if (!$tag) {
            return $this->failNotFound('Tag not found');
        }

        if ($model->update($id, $data)) {
            return $this->respond(['message' => 'Tag updated successfully']);
        }

        return $this->fail($model->errors());
    }

    public function delete($id = null)
    {
        $model = new Tags_model();

        $tag = $model->find($id);
        if (!$tag) {
            return $this->failNotFound('Tag not found');
        }

        if ($model->delete($id)) {
            return $this->respondDeleted(['message' => 'Tag deleted successfully']);
        }

        return $this->fail('Failed to delete tag');
    }
}
