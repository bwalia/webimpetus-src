<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/interviews",
 *     tags={"Interviews"},
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
 *     @OA\Parameter(
 *          name="job_id",
 *          in="query",
 *          required=false,
 *          description="Filter by job ID",
 *          @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get all interviews"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/interviews/{id}",
 *     tags={"Interviews"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Interview UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single interview"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/interviews",
 *     tags={"Interviews"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="job_id", type="integer"),
 *             @OA\Property(property="job_title", type="string"),
 *             @OA\Property(property="candidate_name", type="string"),
 *             @OA\Property(property="candidate_email", type="string"),
 *             @OA\Property(property="interview_date", type="string", format="date-time"),
 *             @OA\Property(property="interview_type", type="string"),
 *             @OA\Property(property="interviewer", type="string"),
 *             @OA\Property(property="status", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Interview created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/interviews/{id}",
 *     tags={"Interviews"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Interview UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="interview_date", type="string", format="date-time"),
 *             @OA\Property(property="status", type="string"),
 *             @OA\Property(property="feedback", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Interview updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/interviews/{id}",
 *     tags={"Interviews"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Interview UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Interview deleted"
 *     )
 * )
 */
class Interviews extends ResourceController
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
        $jobId = $this->request->getVar('job_id');

        $builder = $this->db->table('interviews');

        if ($uuidBusinessId) {
            $builder->where('uuid_business_id', $uuidBusinessId);
        }

        if ($jobId) {
            $builder->where('job_id', $jobId);
        }

        $data = $builder->orderBy('interview_date', 'DESC')->get()->getResultArray();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->db->table('interviews')
            ->where('uuid', $id)
            ->get()
            ->getRowArray();

        if (!$data) {
            return $this->failNotFound('Interview not found');
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

        if ($this->db->table('interviews')->insert($data)) {
            return $this->respondCreated(['message' => 'Interview created successfully', 'uuid' => $data['uuid']]);
        }

        return $this->fail('Failed to create interview');
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        $interview = $this->db->table('interviews')->where('uuid', $id)->get()->getRowArray();

        if (!$interview) {
            return $this->failNotFound('Interview not found');
        }

        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->db->table('interviews')->where('uuid', $id)->update($data)) {
            return $this->respond(['message' => 'Interview updated successfully']);
        }

        return $this->fail('Failed to update interview');
    }

    public function delete($id = null)
    {
        $interview = $this->db->table('interviews')->where('uuid', $id)->get()->getRowArray();

        if (!$interview) {
            return $this->failNotFound('Interview not found');
        }

        if ($this->db->table('interviews')->where('uuid', $id)->delete()) {
            return $this->respondDeleted(['message' => 'Interview deleted successfully']);
        }

        return $this->fail('Failed to delete interview');
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
