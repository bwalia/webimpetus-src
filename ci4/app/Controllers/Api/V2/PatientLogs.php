<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\PatientLogs_model;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * @OA\Tag(
 *     name="Patient Logs",
 *     description="Patient activity logging endpoints"
 * )
 */
class PatientLogs extends CommonController
{
    private $patientLogs_model;

    public function __construct()
    {
        parent::__construct();
        $this->patientLogs_model = new PatientLogs_model();
    }

    /**
     * @OA\Get(
     *     path="/api/v2/patient_logs",
     *     tags={"Patient Logs"},
     *     summary="Get list of patient logs",
     *     description="Returns paginated list of patient logs with filters",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid_business_id",
     *         in="query",
     *         required=true,
     *         description="Business UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="patient_contact_id",
     *         in="query",
     *         description="Filter by patient contact ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="log_category",
     *         in="query",
     *         description="Filter by category",
     *         @OA\Schema(type="string", enum={"General", "Medication", "Vital Signs", "Treatment/Procedure", "Lab Result", "Admission", "Discharge"})
     *     ),
     *     @OA\Parameter(
     *         name="staff_uuid",
     *         in="query",
     *         description="Filter by staff UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_flagged",
     *         in="query",
     *         description="Filter flagged logs only",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="Filter from date (Y-m-d)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="Filter to date (Y-m-d)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Patient logs retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="uuid", type="string"),
     *                     @OA\Property(property="log_number", type="string"),
     *                     @OA\Property(property="patient_name", type="string"),
     *                     @OA\Property(property="log_category", type="string"),
     *                     @OA\Property(property="performed_datetime", type="string", format="date-time"),
     *                     @OA\Property(property="status", type="string")
     *                 )
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
            'patient_contact_id' => $this->request->getGet('patient_contact_id'),
            'log_category' => $this->request->getGet('log_category'),
            'staff_uuid' => $this->request->getGet('staff_uuid'),
            'is_flagged' => $this->request->getGet('is_flagged'),
            'from_date' => $this->request->getGet('from_date'),
            'to_date' => $this->request->getGet('to_date')
        ];

        // Remove null filters
        $filters = array_filter($filters, function($value) {
            return !is_null($value) && $value !== '';
        });

        $logs = $this->patientLogs_model->getLogsWithDetails($businessUuid, $filters);

        return $this->respond([
            'status' => true,
            'message' => 'Patient logs retrieved successfully',
            'data' => $logs,
            'pagination' => [
                'total' => count($logs),
                'page' => 1,
                'limit' => count($logs)
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v2/patient_logs/{uuid}",
     *     tags={"Patient Logs"},
     *     summary="Get patient log by UUID",
     *     description="Returns a single patient log record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Log UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Log not found"
     *     )
     * )
     */
    public function show($uuid = null)
    {
        if (empty($uuid)) {
            return $this->respond([
                'status' => false,
                'message' => 'Log UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $log = $this->patientLogs_model->where('uuid', $uuid)->first();

        if (empty($log)) {
            return $this->respond([
                'status' => false,
                'message' => 'Patient log not found'
            ], ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->respond([
            'status' => true,
            'message' => 'Patient log retrieved successfully',
            'data' => $log
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/patient_logs",
     *     tags={"Patient Logs"},
     *     summary="Create new patient log",
     *     description="Creates a new patient log entry",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"uuid_business_id", "patient_contact_id", "staff_uuid", "log_category", "performed_datetime"},
     *             @OA\Property(property="uuid_business_id", type="string"),
     *             @OA\Property(property="patient_contact_id", type="integer"),
     *             @OA\Property(property="staff_uuid", type="string"),
     *             @OA\Property(property="log_category", type="string"),
     *             @OA\Property(property="log_type", type="string"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="performed_datetime", type="string", format="date-time"),
     *             @OA\Property(property="priority", type="string", enum={"Normal", "High", "Urgent"}),
     *             @OA\Property(property="status", type="string", enum={"Draft", "Scheduled", "In Progress", "Completed", "Cancelled"}),
     *             @OA\Property(property="is_flagged", type="boolean"),
     *             @OA\Property(property="flag_reason", type="string"),
     *             @OA\Property(property="medication_name", type="string", description="For Medication category"),
     *             @OA\Property(property="dosage", type="string", description="For Medication category"),
     *             @OA\Property(property="route", type="string", description="For Medication category"),
     *             @OA\Property(property="frequency", type="string", description="For Medication category"),
     *             @OA\Property(property="blood_pressure_systolic", type="integer", description="For Vital Signs category"),
     *             @OA\Property(property="blood_pressure_diastolic", type="integer", description="For Vital Signs category"),
     *             @OA\Property(property="heart_rate", type="integer", description="For Vital Signs category"),
     *             @OA\Property(property="temperature", type="number", description="For Vital Signs category"),
     *             @OA\Property(property="oxygen_saturation", type="integer", description="For Vital Signs category"),
     *             @OA\Property(property="test_name", type="string", description="For Lab Result category"),
     *             @OA\Property(property="test_result", type="string", description="For Lab Result category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Patient log created successfully"
     *     )
     * )
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Validate required fields
        $required = ['uuid_business_id', 'patient_contact_id', 'staff_uuid', 'log_category', 'performed_datetime'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->respond([
                    'status' => false,
                    'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
                ], ResponseInterface::HTTP_BAD_REQUEST);
            }
        }

        // Generate UUID and log number
        $data['uuid'] = UUID::v5(UUID::v4(), 'patient_logs');
        $data['log_number'] = $this->patientLogs_model->getNextLogNumber($data['uuid_business_id']);
        $data['created_by'] = session('uuid') ?? $this->request->getHeaderLine('X-User-UUID');

        try {
            $this->patientLogs_model->insert($data);

            return $this->respond([
                'status' => true,
                'message' => 'Patient log created successfully',
                'data' => [
                    'uuid' => $data['uuid'],
                    'log_number' => $data['log_number']
                ]
            ], ResponseInterface::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->respond([
                'status' => false,
                'message' => 'Failed to create patient log: ' . $e->getMessage()
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v2/patient_logs/{uuid}",
     *     tags={"Patient Logs"},
     *     summary="Update patient log",
     *     description="Updates an existing patient log",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Log UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="is_flagged", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient log updated successfully"
     *     )
     * )
     */
    public function update($uuid = null)
    {
        if (empty($uuid)) {
            return $this->respond([
                'status' => false,
                'message' => 'Log UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $log = $this->patientLogs_model->where('uuid', $uuid)->first();

        if (empty($log)) {
            return $this->respond([
                'status' => false,
                'message' => 'Patient log not found'
            ], ResponseInterface::HTTP_NOT_FOUND);
        }

        $data = $this->request->getJSON(true);

        try {
            $this->patientLogs_model->where('uuid', $uuid)->set($data)->update();

            return $this->respond([
                'status' => true,
                'message' => 'Patient log updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->respond([
                'status' => false,
                'message' => 'Failed to update patient log: ' . $e->getMessage()
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/patient_logs/{uuid}",
     *     tags={"Patient Logs"},
     *     summary="Delete patient log",
     *     description="Deletes a patient log record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Log UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient log deleted successfully"
     *     )
     * )
     */
    public function delete($uuid = null)
    {
        if (empty($uuid)) {
            return $this->respond([
                'status' => false,
                'message' => 'Log UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $log = $this->patientLogs_model->where('uuid', $uuid)->first();

        if (empty($log)) {
            return $this->respond([
                'status' => false,
                'message' => 'Patient log not found'
            ], ResponseInterface::HTTP_NOT_FOUND);
        }

        try {
            $this->patientLogs_model->where('uuid', $uuid)->delete();

            return $this->respond([
                'status' => true,
                'message' => 'Patient log deleted successfully'
            ]);

        } catch (\Exception $e) {
            return $this->respond([
                'status' => false,
                'message' => 'Failed to delete patient log: ' . $e->getMessage()
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v2/patient_logs/timeline/{patient_contact_id}",
     *     tags={"Patient Logs"},
     *     summary="Get patient timeline",
     *     description="Returns complete patient activity timeline",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="patient_contact_id",
     *         in="path",
     *         required=true,
     *         description="Patient contact ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="uuid_business_id",
     *         in="query",
     *         required=true,
     *         description="Business UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function timeline($patientContactId = null)
    {
        if (empty($patientContactId)) {
            return $this->respond([
                'status' => false,
                'message' => 'Patient contact ID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $businessUuid = $this->request->getGet('uuid_business_id');

        if (empty($businessUuid)) {
            return $this->respond([
                'status' => false,
                'message' => 'Business UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $timeline = $this->patientLogs_model->getPatientTimeline($patientContactId, $businessUuid);

        return $this->respond([
            'status' => true,
            'message' => 'Patient timeline retrieved successfully',
            'data' => $timeline
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v2/patient_logs/flagged",
     *     tags={"Patient Logs"},
     *     summary="Get flagged logs",
     *     description="Returns all flagged patient logs",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid_business_id",
     *         in="query",
     *         required=true,
     *         description="Business UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function flagged()
    {
        $businessUuid = $this->request->getGet('uuid_business_id');

        if (empty($businessUuid)) {
            return $this->respond([
                'status' => false,
                'message' => 'Business UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $flaggedLogs = $this->patientLogs_model->getFlaggedLogs($businessUuid);

        return $this->respond([
            'status' => true,
            'message' => 'Flagged logs retrieved successfully',
            'data' => $flaggedLogs
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v2/patient_logs/medications/{patient_contact_id}",
     *     tags={"Patient Logs"},
     *     summary="Get medication history",
     *     description="Returns patient medication history",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="patient_contact_id",
     *         in="path",
     *         required=true,
     *         description="Patient contact ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="uuid_business_id",
     *         in="query",
     *         required=true,
     *         description="Business UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function medications($patientContactId = null)
    {
        if (empty($patientContactId)) {
            return $this->respond([
                'status' => false,
                'message' => 'Patient contact ID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $businessUuid = $this->request->getGet('uuid_business_id');

        if (empty($businessUuid)) {
            return $this->respond([
                'status' => false,
                'message' => 'Business UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $medications = $this->patientLogs_model->getMedicationHistory($patientContactId, $businessUuid);

        return $this->respond([
            'status' => true,
            'message' => 'Medication history retrieved successfully',
            'data' => $medications
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v2/patient_logs/vital-signs/{patient_contact_id}",
     *     tags={"Patient Logs"},
     *     summary="Get vital signs history",
     *     description="Returns patient vital signs history",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="patient_contact_id",
     *         in="path",
     *         required=true,
     *         description="Patient contact ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="uuid_business_id",
     *         in="query",
     *         required=true,
     *         description="Business UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="Number of days to retrieve (default 7)",
     *         @OA\Schema(type="integer", default=7)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function vitalSigns($patientContactId = null)
    {
        if (empty($patientContactId)) {
            return $this->respond([
                'status' => false,
                'message' => 'Patient contact ID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $businessUuid = $this->request->getGet('uuid_business_id');

        if (empty($businessUuid)) {
            return $this->respond([
                'status' => false,
                'message' => 'Business UUID is required'
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $days = $this->request->getGet('days') ?? 7;
        $vitalSigns = $this->patientLogs_model->getVitalSigns($patientContactId, $businessUuid, $days);

        return $this->respond([
            'status' => true,
            'message' => 'Vital signs retrieved successfully',
            'data' => $vitalSigns
        ]);
    }
}
