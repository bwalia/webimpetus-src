<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Knowledge_base_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/knowledge-base",
 *     tags={"Knowledge Base"},
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
 *         description="Get all knowledge base articles"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/knowledge-base/{uuid}",
 *     tags={"Knowledge Base"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Article UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single knowledge base article"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/knowledge-base",
 *     tags={"Knowledge Base"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="content", type="string"),
 *             @OA\Property(property="category", type="string"),
 *             @OA\Property(property="status", type="string"),
 *             @OA\Property(property="article_number", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Article created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/knowledge-base/{uuid}",
 *     tags={"Knowledge Base"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Article UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Article updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/knowledge-base/{uuid}",
 *     tags={"Knowledge Base"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Article UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Article deleted"
 *     )
 * )
 */
class KnowledgeBase extends ResourceController
{
    protected $modelName = 'App\Models\Knowledge_base_model';
    protected $format = 'json';

    public function index()
    {
        $model = new Knowledge_base_model();
        $uuid_business_id = $this->request->getVar('uuid_business_id');

        if (!$uuid_business_id) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        $data = $model->where('uuid_business_id', $uuid_business_id)->findAll();
        return $this->respond($data);
    }

    public function show($uuid = null)
    {
        $model = new Knowledge_base_model();
        $data = $model->where('uuid', $uuid)->first();

        if (!$data) {
            return $this->failNotFound('Article not found');
        }

        // Increment view count
        $model->where('uuid', $uuid)->set(['view_count' => ($data['view_count'] ?? 0) + 1])->update();

        return $this->respond($data);
    }

    public function create()
    {
        $model = new Knowledge_base_model();
        $data = $this->request->getJSON(true);

        if (!isset($data['uuid_business_id'])) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        if ($model->insert($data)) {
            return $this->respondCreated(['message' => 'Article created successfully']);
        }

        return $this->fail($model->errors());
    }

    public function update($uuid = null)
    {
        $model = new Knowledge_base_model();
        $data = $this->request->getJSON(true);

        $article = $model->where('uuid', $uuid)->first();
        if (!$article) {
            return $this->failNotFound('Article not found');
        }

        if ($model->where('uuid', $uuid)->set($data)->update()) {
            return $this->respond(['message' => 'Article updated successfully']);
        }

        return $this->fail($model->errors());
    }

    public function delete($uuid = null)
    {
        $model = new Knowledge_base_model();

        $article = $model->where('uuid', $uuid)->first();
        if (!$article) {
            return $this->failNotFound('Article not found');
        }

        if ($model->where('uuid', $uuid)->delete()) {
            return $this->respondDeleted(['message' => 'Article deleted successfully']);
        }

        return $this->fail('Failed to delete article');
    }
}
