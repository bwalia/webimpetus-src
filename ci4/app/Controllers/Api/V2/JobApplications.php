<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/job-applications",
 *     tags={"Job Applications"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="job_uuid",
 *          in="query",
 *          required=false,
 *          description="Filter by job UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *          name="uuid_business_id",
 *          in="query",
 *          required=false,
 *          description="Business UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get all job applications"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/job-applications/{id}",
 *     tags={"Job Applications"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Application UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single job application"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/job-applications",
 *     tags={"Job Applications"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="job_uuid", type="string"),
 *             @OA\Property(property="applicant_name", type="string"),
 *             @OA\Property(property="applicant_email", type="string"),
 *             @OA\Property(property="applicant_phone", type="string"),
 *             @OA\Property(property="resume_path", type="string"),
 *             @OA\Property(property="cover_letter", type="string"),
 *             @OA\Property(property="status", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Application created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/job-applications/{id}",
 *     tags={"Job Applications"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Application UUID",
 *          @OA\Schema(type="string")
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string"),
 *             @OA\Property(property="notes", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Application updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/job-applications/{id}",
 *     tags={"Job Applications"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Application UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Application deleted"
 *     )
 * )
 */
class JobApplications extends ResourceController
{
    protected $format = 'json';
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $jobUuid = $this->request->getVar('job_uuid');
        $uuidBusinessId = $this->request->getVar('uuid_business_id');

        $builder = $this->db->table('job_applications');

        if ($jobUuid) {
            $builder->where('job_uuid', $jobUuid);
        }

        if ($uuidBusinessId) {
            $builder->where('uuid_business_id', $uuidBusinessId);
        }

        $data = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->db->table('job_applications')
            ->where('uuid', $id)
            ->get()
            ->getRowArray();

        if (!$data) {
            return $this->failNotFound('Application not found');
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

        // Default status
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        if ($this->db->table('job_applications')->insert($data)) {
            return $this->respondCreated(['message' => 'Application created successfully', 'uuid' => $data['uuid']]);
        }

        return $this->fail('Failed to create application');
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        $application = $this->db->table('job_applications')->where('uuid', $id)->get()->getRowArray();

        if (!$application) {
            return $this->failNotFound('Application not found');
        }

        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->db->table('job_applications')->where('uuid', $id)->update($data)) {
            return $this->respond(['message' => 'Application updated successfully']);
        }

        return $this->fail('Failed to update application');
    }

    public function delete($id = null)
    {
        $application = $this->db->table('job_applications')->where('uuid', $id)->get()->getRowArray();

        if (!$application) {
            return $this->failNotFound('Application not found');
        }

        if ($this->db->table('job_applications')->where('uuid', $id)->delete()) {
            return $this->respondDeleted(['message' => 'Application deleted successfully']);
        }

        return $this->fail('Failed to delete application');
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
