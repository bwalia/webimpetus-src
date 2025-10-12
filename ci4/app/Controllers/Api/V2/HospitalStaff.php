<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\HospitalStaff_model;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * @OA\Tag(
 *     name="Hospital Staff",
 *     description="Hospital staff management endpoints"
 * )
 */
class HospitalStaff extends CommonController
{
    private $hospitalStaff_model;

    public function __construct()
    {
        parent::__construct();
        $this->hospitalStaff_model = new HospitalStaff_model();
    }

    /**
     * @OA\Get(
     *     path="/api/v2/hospital_staff",
     *     tags={"Hospital Staff"},
     *     summary="Get list of hospital staff",
     *     description="Returns paginated list of hospital staff with details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid_business_id",
     *         in="query",
     *         required=true,
     *         description="Business UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status (Active, On Leave, Inactive, Suspended)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="department",
     *         in="query",
     *         description="Filter by department",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="job_title",
     *         in="query",
     *         description="Filter by job title",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of records per page",
     *         @OA\Schema(type="integer", default=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Hospital staff retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="uuid", type="string"),
     *                     @OA\Property(property="staff_number", type="string"),
     *                     @OA\Property(property="user_name", type="string"),
     *                     @OA\Property(property="department", type="string"),
     *                     @OA\Property(property="job_title", type="string"),
     *                     @OA\Property(property="status", type="string")
     *                 )
     *             ),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="page", type="integer"),
     *                 @OA\Property(property="limit", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $businessUuid = $this->request->getGet('uuid_business_id');

        if (empty($businessUuid)) {
            return $this->respond([
                'status' => false,
                'message' => 'Business UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $filters = [
            'status' => $this->request->getGet('status'),
            'department' => $this->request->getGet('department'),
            'job_title' => $this->request->getGet('job_title')
        ];

        $staff = $this->hospitalStaff_model->getStaffWithDetails($businessUuid, $filters);

        return $this->respond([
            'status' => true,
            'message' => 'Hospital staff retrieved successfully',
            'data' => $staff,
            'pagination' => [
                'total' => count($staff),
                'page' => 1,
                'limit' => count($staff)
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v2/hospital_staff/{uuid}",
     *     tags={"Hospital Staff"},
     *     summary="Get hospital staff by UUID",
     *     description="Returns a single hospital staff record with details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Staff UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Staff not found"
     *     )
     * )
     */
    public function show($uuid = null)
    {
        if (empty($uuid)) {
            return $this->respond([
                'status' => false,
                'message' => 'Staff UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $staff = $this->hospitalStaff_model->getStaffByUuid($uuid);

        if (empty($staff)) {
            return $this->respond([
                'status' => false,
                'message' => 'Hospital staff not found'
            ], ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->respond([
            'status' => true,
            'message' => 'Hospital staff retrieved successfully',
            'data' => $staff
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/hospital_staff",
     *     tags={"Hospital Staff"},
     *     summary="Create new hospital staff",
     *     description="Creates a new hospital staff record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"uuid_business_id", "department", "job_title", "employment_type", "status"},
     *             @OA\Property(property="uuid_business_id", type="string"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="contact_id", type="integer"),
     *             @OA\Property(property="employee_id", type="integer"),
     *             @OA\Property(property="department", type="string"),
     *             @OA\Property(property="job_title", type="string"),
     *             @OA\Property(property="specialization", type="string"),
     *             @OA\Property(property="gmc_number", type="string"),
     *             @OA\Property(property="nmc_number", type="string"),
     *             @OA\Property(property="employment_type", type="string", enum={"Full-time", "Part-time", "Contract", "Locum"}),
     *             @OA\Property(property="status", type="string", enum={"Active", "On Leave", "Inactive", "Suspended"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Hospital staff created successfully"
     *     )
     * )
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['uuid_business_id'])) {
            return $this->respond([
                'status' => false,
                'message' => 'Business UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Generate UUID and staff number
        $data['uuid'] = UUID::v5(UUID::v4(), 'hospital_staff');
        $data['staff_number'] = $this->hospitalStaff_model->getNextStaffNumber($data['uuid_business_id']);
        $data['created_by'] = session('uuid') ?? $this->request->getHeaderLine('X-User-UUID');

        try {
            $this->hospitalStaff_model->insert($data);

            return $this->respond([
                'status' => true,
                'message' => 'Hospital staff created successfully',
                'data' => [
                    'uuid' => $data['uuid'],
                    'staff_number' => $data['staff_number']
                ]
            ], ResponseInterface::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->respond([
                'status' => false,
                'message' => 'Failed to create hospital staff: ' . $e->getMessage()
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v2/hospital_staff/{uuid}",
     *     tags={"Hospital Staff"},
     *     summary="Update hospital staff",
     *     description="Updates an existing hospital staff record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Staff UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="department", type="string"),
     *             @OA\Property(property="job_title", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hospital staff updated successfully"
     *     )
     * )
     */
    public function update($uuid = null)
    {
        if (empty($uuid)) {
            return $this->respond([
                'status' => false,
                'message' => 'Staff UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $staff = $this->hospitalStaff_model->where('uuid', $uuid)->first();

        if (empty($staff)) {
            return $this->respond([
                'status' => false,
                'message' => 'Hospital staff not found'
            ], ResponseInterface::HTTP_NOT_FOUND);
        }

        $data = $this->request->getJSON(true);

        try {
            $this->hospitalStaff_model->where('uuid', $uuid)->set($data)->update();

            return $this->respond([
                'status' => true,
                'message' => 'Hospital staff updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->respond([
                'status' => false,
                'message' => 'Failed to update hospital staff: ' . $e->getMessage()
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/hospital_staff/{uuid}",
     *     tags={"Hospital Staff"},
     *     summary="Delete hospital staff",
     *     description="Deletes a hospital staff record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Staff UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hospital staff deleted successfully"
     *     )
     * )
     */
    public function delete($uuid = null)
    {
        if (empty($uuid)) {
            return $this->respond([
                'status' => false,
                'message' => 'Staff UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $staff = $this->hospitalStaff_model->where('uuid', $uuid)->first();

        if (empty($staff)) {
            return $this->respond([
                'status' => false,
                'message' => 'Hospital staff not found'
            ], ResponseInterface::HTTP_NOT_FOUND);
        }

        try {
            $this->hospitalStaff_model->where('uuid', $uuid)->delete();

            return $this->respond([
                'status' => true,
                'message' => 'Hospital staff deleted successfully'
            ]);

        } catch (\Exception $e) {
            return $this->respond([
                'status' => false,
                'message' => 'Failed to delete hospital staff: ' . $e->getMessage()
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
