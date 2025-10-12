<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/jobs",
 *     tags={"Jobs"},
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
 *         description="Get all job vacancies"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/jobs/{id}",
 *     tags={"Jobs"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Job UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single job vacancy"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/jobs",
 *     tags={"Jobs"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="reference_number", type="string"),
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="location", type="string"),
 *             @OA\Property(property="salary_range", type="string"),
 *             @OA\Property(property="employment_type", type="string"),
 *             @OA\Property(property="status", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Job created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/jobs/{id}",
 *     tags={"Jobs"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Job UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="status", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Job updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/jobs/{id}",
 *     tags={"Jobs"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Job UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Job deleted"
 *     )
 * )
 */
class Jobs extends ResourceController
{
    protected $format = 'json';
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $uuidBusinessId = $this->request->getVar('uuid_business_id');

        $builder = $this->db->table('jobs');

        if ($uuidBusinessId) {
            $builder->where('uuid_business_id', $uuidBusinessId);
        }

        $data = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->db->table('jobs')
            ->where('uuid', $id)
            ->get()
            ->getRowArray();

        if (!$data) {
            return $this->failNotFound('Job not found');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        // Generate UUID if not provided
        if (!isset($data['uuid'])) {
            $data['uuid'] = $this->generateUUID();
        }

        // Set timestamps
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        if ($this->db->table('jobs')->insert($data)) {
            return $this->respondCreated(['message' => 'Job created successfully', 'uuid' => $data['uuid']]);
        }

        return $this->fail('Failed to create job');
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        $job = $this->db->table('jobs')->where('uuid', $id)->get()->getRowArray();

        if (!$job) {
            return $this->failNotFound('Job not found');
        }

        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->db->table('jobs')->where('uuid', $id)->update($data)) {
            return $this->respond(['message' => 'Job updated successfully']);
        }

        return $this->fail('Failed to update job');
    }

    public function delete($id = null)
    {
        $job = $this->db->table('jobs')->where('uuid', $id)->get()->getRowArray();

        if (!$job) {
            return $this->failNotFound('Job not found');
        }

        if ($this->db->table('jobs')->where('uuid', $id)->delete()) {
            return $this->respondDeleted(['message' => 'Job deleted successfully']);
        }

        return $this->fail('Failed to delete job');
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
