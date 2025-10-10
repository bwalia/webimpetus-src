<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Roles_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/roles",
 *     tags={"Roles"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Get all roles"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/roles/{uuid}",
 *     tags={"Roles"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Role UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single role"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/roles",
 *     tags={"Roles"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="role_name", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Role created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/roles/{uuid}",
 *     tags={"Roles"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Role UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Role updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/roles/{uuid}",
 *     tags={"Roles"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Role UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Role deleted"
 *     )
 * )
 */
class Roles extends ResourceController
{
    protected $modelName = 'App\Models\Roles_model';
    protected $format = 'json';

    public function index()
    {
        $model = new Roles_model();
        $data = $model->findAll();
        return $this->respond($data);
    }

    public function show($uuid = null)
    {
        $model = new Roles_model();
        $data = $model->where('uuid', $uuid)->first();

        if (!$data) {
            return $this->failNotFound('Role not found');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $model = new Roles_model();
        $data = $this->request->getJSON(true);

        if ($model->insert($data)) {
            return $this->respondCreated(['message' => 'Role created successfully']);
        }

        return $this->fail($model->errors());
    }

    public function update($uuid = null)
    {
        $model = new Roles_model();
        $data = $this->request->getJSON(true);

        $role = $model->where('uuid', $uuid)->first();
        if (!$role) {
            return $this->failNotFound('Role not found');
        }

        if ($model->where('uuid', $uuid)->set($data)->update()) {
            return $this->respond(['message' => 'Role updated successfully']);
        }

        return $this->fail($model->errors());
    }

    public function delete($uuid = null)
    {
        $model = new Roles_model();

        $role = $model->where('uuid', $uuid)->first();
        if (!$role) {
            return $this->failNotFound('Role not found');
        }

        if ($model->where('uuid', $uuid)->delete()) {
            return $this->respondDeleted(['message' => 'Role deleted successfully']);
        }

        return $this->fail('Failed to delete role');
    }
}
