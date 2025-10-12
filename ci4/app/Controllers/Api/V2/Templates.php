<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Template_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/templates",
 *     tags={"Templates"},
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
 *         description="Get all templates"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/templates/{id}",
 *     tags={"Templates"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Template code",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single template"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/templates",
 *     tags={"Templates"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="code", type="string"),
 *             @OA\Property(property="subject", type="string"),
 *             @OA\Property(property="template_content", type="string"),
 *             @OA\Property(property="comment", type="string"),
 *             @OA\Property(property="template_type", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Template created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/templates/{id}",
 *     tags={"Templates"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Template code",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="subject", type="string"),
 *             @OA\Property(property="template_content", type="string"),
 *             @OA\Property(property="comment", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Template updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/templates/{id}",
 *     tags={"Templates"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Template code",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Template deleted"
 *     )
 * )
 */
class Templates extends ResourceController
{
    protected $modelName = 'App\Models\Template_model';
    protected $format = 'json';

    public function index()
    {
        $model = new Template_model();
        $uuidBusinessId = $this->request->getVar('uuid_business_id');

        $builder = $model->builder();

        if ($uuidBusinessId) {
            $builder->where('uuid_business_id', $uuidBusinessId);
        }

        $data = $builder->get()->getResultArray();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $model = new Template_model();
        $data = $model->where('code', $id)->first();

        if (!$data) {
            return $this->failNotFound('Template not found');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $model = new Template_model();
        $data = $this->request->getJSON(true);

        if (!isset($data['code']) || empty($data['code'])) {
            return $this->respond(['error' => 'Template code is required'], 400);
        }

        if ($model->insert($data)) {
            return $this->respondCreated(['message' => 'Template created successfully', 'code' => $data['code']]);
        }

        return $this->fail($model->errors() ?: 'Failed to create template');
    }

    public function update($id = null)
    {
        $model = new Template_model();
        $data = $this->request->getJSON(true);

        $template = $model->where('code', $id)->first();
        if (!$template) {
            return $this->failNotFound('Template not found');
        }

        if ($model->where('code', $id)->set($data)->update()) {
            return $this->respond(['message' => 'Template updated successfully']);
        }

        return $this->fail($model->errors() ?: 'Failed to update template');
    }

    public function delete($id = null)
    {
        $model = new Template_model();

        $template = $model->where('code', $id)->first();
        if (!$template) {
            return $this->failNotFound('Template not found');
        }

        if ($model->where('code', $id)->delete()) {
            return $this->respondDeleted(['message' => 'Template deleted successfully']);
        }

        return $this->fail('Failed to delete template');
    }
}
