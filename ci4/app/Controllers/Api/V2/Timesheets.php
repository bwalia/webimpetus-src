<?php

namespace App\Controllers\Api\V2;

use App\Libraries\UUID;
use App\Models\Timesheets_model;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Timesheets",
 *     description="Track billable and non-billable time entries."
 * )
 */
class Timesheets extends ResourceController
{
    /** @var string */
    protected $format = 'json';

    /** @var string */
    protected $modelName = Timesheets_model::class;

    /**
     * List timesheets with optional pagination & filters.
     *
     * @OA\Get(
     *     path="/api/v2/timesheets",
     *     tags={"Timesheets"},
     *     summary="List timesheets",
     *     description="Returns paginated timesheets for a business with optional filters (status, employee, project, dates, invoicing).",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="format",
     *         in="query",
     *         required=false,
     *         description="Optional response format (default: json).",
     *         @OA\Schema(type="string", enum={"json","csv"})
     *     ),
     *     @OA\Parameter(
     *         name="params",
     *         in="query",
     *         required=false,
     *         description="JSON encoded pagination, sorting, and filtering options.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Timesheets matching the supplied filters.",
     *         @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")
     *     ),
     *     @OA\Response(response=403, description="Business identifier missing"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index()
    {
        $paramsPayload = $this->request->getGet('params');
        $decodedParams = [];
        if (!empty($paramsPayload)) {
            $decodedParams = json_decode($paramsPayload, true) ?? [];
        }

        $page = (int)($decodedParams['pagination']['page'] ?? $this->request->getGet('page') ?? 1);
        $perPage = (int)($decodedParams['pagination']['perPage'] ?? $this->request->getGet('perPage') ?? 20);
        $perPage = max(1, min($perPage, 200));

        $sortField = $decodedParams['sort']['field'] ?? $this->request->getGet('order') ?? 'start_time';
        $sortDirection = strtoupper($decodedParams['sort']['order'] ?? $this->request->getGet('dir') ?? 'DESC');
        $sortDirection = $sortDirection === 'ASC' ? 'ASC' : 'DESC';

        $filters = $decodedParams['filter'] ?? [];
        $filters['uuid_business_id'] = $filters['uuid_business_id']
            ?? $this->request->getGet('uuid_business_id');

        if (empty($filters['uuid_business_id'])) {
            return $this->failForbidden('uuid_business_id is required');
        }

        $offset = ($page - 1) * $perPage;

        $builder = $this->baseQuery()
            ->where('t.uuid_business_id', $filters['uuid_business_id'])
            ->where('t.deleted_at', null);

        $this->applyFilters($builder, $filters);

        $sortable = [
            'start_time' => 't.start_time',
            'end_time' => 't.end_time',
            'status' => 't.status',
            'employee' => 'employee_full_name',
            'project' => 'p.name',
            'billable_hours' => 't.billable_hours',
            'total_amount' => 't.total_amount',
        ];
        $sortColumn = $sortable[$sortField] ?? 't.start_time';

        $totalBuilder = clone $builder;
        $total = (int)$totalBuilder->select('COUNT(*) AS total_rows')->get()->getRow('total_rows');

        $records = $builder
            ->select($this->selectColumns())
            ->orderBy($sortColumn, $sortDirection)
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $response = [
            'data' => $records,
            'meta' => [
                'pagination' => [
                    'page' => $page,
                    'perPage' => $perPage,
                    'total' => $total,
                    'lastPage' => (int)ceil($total / $perPage),
                ],
                'sort' => [
                    'field' => $sortField,
                    'order' => $sortDirection,
                ],
                'filter' => $filters,
            ],
        ];

        return $this->respond($response);
    }

    /**
     * Retrieve a single timesheet by UUID.
     *
     * @OA\Get(
     *     path="/api/v2/timesheets/{uuid}",
     *     tags={"Timesheets"},
     *     summary="Retrieve a timesheet",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Timesheet UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Timesheet detail", @OA\JsonContent(ref="#/components/schemas/TimesheetResponse")),
     *     @OA\Response(response=404, description="Timesheet not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show($uuid = null)
    {
        if (empty($uuid)) {
            return $this->failValidationErrors('uuid is required');
        }

        $record = $this->fetchTimesheet($uuid);
        if (!$record) {
            return $this->failNotFound('Timesheet not found');
        }

        return $this->respond(['data' => $record]);
    }

    /**
     * Create a new timesheet entry.
     *
     * @OA\Post(
     *     path="/api/v2/timesheets",
     *     tags={"Timesheets"},
     *     summary="Create a timesheet",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/TimesheetCreateRequest"), @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(ref="#/components/schemas/TimesheetCreateRequest"))),
     *     @OA\Response(response=201, description="Timesheet created", @OA\JsonContent(ref="#/components/schemas/TimesheetResponse")),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function create()
    {
        $payload = $this->getRequestData();
        $required = ['uuid_business_id', 'employee_id', 'start_time'];
        $missing = array_filter($required, fn ($field) => empty($payload[$field]));
        if (!empty($missing)) {
            return $this->failValidationErrors('Missing required fields: ' . implode(', ', $missing));
        }

        $payload['uuid'] = $payload['uuid'] ?? UUID::v5(UUID::v4(), 'timesheets');
        $payload['start_time'] = $this->normalizeDateTime($payload['start_time']);
        if (!empty($payload['end_time'])) {
            $payload['end_time'] = $this->normalizeDateTime($payload['end_time']);
        }
        $payload = $this->normalizeFlags($payload);

        if (!$this->model->insert($payload)) {
            return $this->failValidationErrors($this->model->errors());
        }

        $record = $this->fetchTimesheet($payload['uuid']);

        return $this->respondCreated(['data' => $record]);
    }

    /**
     * Update an existing timesheet.
     *
     * @OA\Put(
     *     path="/api/v2/timesheets/{uuid}",
     *     tags={"Timesheets"},
     *     summary="Update a timesheet",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Timesheet UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/TimesheetUpdateRequest")),
     *     @OA\Response(response=200, description="Timesheet updated", @OA\JsonContent(ref="#/components/schemas/TimesheetResponse")),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=404, description="Timesheet not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update($uuid = null)
    {
        if (empty($uuid)) {
            return $this->failValidationErrors('uuid is required');
        }

        $existing = $this->model->where('uuid', $uuid)->first();
        if (!$existing) {
            return $this->failNotFound('Timesheet not found');
        }

        $payload = $this->getRequestData();
        if (isset($payload['start_time'])) {
            $payload['start_time'] = $this->normalizeDateTime($payload['start_time']);
        }
        if (isset($payload['end_time'])) {
            $payload['end_time'] = $this->normalizeDateTime($payload['end_time']);
        }
        $payload = $this->normalizeFlags($payload);

        if (empty($payload)) {
            return $this->failValidationErrors('No fields supplied for update');
        }

        if (!$this->model->update((int)$existing['id'], $payload)) {
            return $this->failValidationErrors($this->model->errors());
        }

        $record = $this->fetchTimesheet($uuid);

        return $this->respond(['data' => $record]);
    }

    /**
     * Delete a timesheet (soft delete).
     *
     * @OA\Delete(
     *     path="/api/v2/timesheets/{uuid}",
     *     tags={"Timesheets"},
     *     summary="Delete a timesheet",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Timesheet UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Deletion status", @OA\JsonContent(ref="#/components/schemas/DeleteConfirmation")),
     *     @OA\Response(response=404, description="Timesheet not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function delete($uuid = null)
    {
        if (empty($uuid)) {
            return $this->failValidationErrors('uuid is required');
        }

        $existing = $this->model->where('uuid', $uuid)->first();
        if (!$existing) {
            return $this->failNotFound('Timesheet not found');
        }

        if (!$this->model->delete((int)$existing['id'])) {
            return $this->failServerError('Failed to delete timesheet');
        }

        return $this->respond(['data' => [true]]);
    }

    /**
     * Start a running timer.
     *
     * @OA\Post(
     *     path="/api/v2/timesheets/start",
     *     tags={"Timesheets"},
     *     summary="Start a timesheet timer",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/TimesheetStartRequest")),
     *     @OA\Response(response=201, description="Timer started", @OA\JsonContent(ref="#/components/schemas/TimesheetResponse")),
     *     @OA\Response(response=400, description="Validation error")
     * )
     */
    public function startTimer()
    {
        $payload = $this->getRequestData();
        $required = ['uuid_business_id', 'employee_id'];
        $missing = array_filter($required, fn ($field) => empty($payload[$field]));
        if (!empty($missing)) {
            return $this->failValidationErrors('Missing required fields: ' . implode(', ', $missing));
        }

        $data = [
            'uuid' => UUID::v5(UUID::v4(), 'timesheets'),
            'uuid_business_id' => $payload['uuid_business_id'],
            'employee_id' => $payload['employee_id'],
            'project_id' => $payload['project_id'] ?? null,
            'task_id' => $payload['task_id'] ?? null,
            'customer_id' => $payload['customer_id'] ?? null,
            'description' => $payload['description'] ?? null,
            'hourly_rate' => $payload['hourly_rate'] ?? null,
            'start_time' => date('Y-m-d H:i:s'),
            'is_running' => 1,
            'status' => $payload['status'] ?? 'running',
            'is_billable' => isset($payload['is_billable']) ? (int)$this->toBool($payload['is_billable']) : 1,
            'created_by' => $payload['created_by'] ?? null,
        ];

        if (!$this->model->insert($data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        $record = $this->fetchTimesheet($data['uuid']);

        return $this->respondCreated(['data' => $record]);
    }

    /**
     * Stop a running timer.
     *
     * @OA\Post(
     *     path="/api/v2/timesheets/{uuid}/stop",
     *     tags={"Timesheets"},
     *     summary="Stop a timesheet timer",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="Timesheet UUID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Timer stopped", @OA\JsonContent(ref="#/components/schemas/TimesheetResponse")),
     *     @OA\Response(response=404, description="Timesheet not found or not running"),
     *     @OA\Response(response=400, description="Unable to stop timer")
     * )
     */
    public function stopTimer($uuid)
    {
        if (empty($uuid)) {
            return $this->failValidationErrors('uuid is required');
        }

        $record = $this->model->where('uuid', $uuid)->first();
        if (!$record) {
            return $this->failNotFound('Timesheet not found');
        }

        if (!$this->model->stopTimer($uuid)) {
            return $this->fail('Unable to stop timer or timer already stopped', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $updated = $this->fetchTimesheet($uuid);

        return $this->respond(['data' => $updated]);
    }

    /**
     * Build the base query joining related tables.
     */
    private function baseQuery(): BaseBuilder
    {
        return $this->model->db->table('timesheets t')
            ->join('employees e', 'e.id = t.employee_id', 'left')
            ->join('projects p', 'p.id = t.project_id', 'left')
            ->join('tasks task', 'task.id = t.task_id', 'left')
            ->join('customers c', 'c.id = t.customer_id', 'left');
    }

    /**
     * Fetch a single timesheet with joined data.
     */
    private function fetchTimesheet(string $uuid): ?array
    {
        return $this->baseQuery()
            ->select($this->selectColumns())
            ->where('t.uuid', $uuid)
            ->where('t.deleted_at', null)
            ->get()
            ->getRowArray();
    }

    private function selectColumns(): string
    {
        return implode(', ', [
            't.uuid',
            't.uuid_business_id',
            't.employee_id',
            't.project_id',
            't.task_id',
            't.customer_id',
            't.description',
            't.start_time',
            't.end_time',
            't.duration_minutes',
            't.billable_hours',
            't.hourly_rate',
            't.total_amount',
            't.is_billable',
            't.is_running',
            't.is_invoiced',
            't.invoice_id',
            't.status',
            't.notes',
            't.tags',
            't.created_by',
            't.created_at',
            't.updated_at',
            "CONCAT(COALESCE(e.first_name, ''), ' ', COALESCE(e.surname, '')) AS employee_full_name",
            'e.first_name AS employee_first_name',
            'e.surname AS employee_surname',
            'p.name AS project_name',
            'task.name AS task_name',
            'c.company_name AS customer_name',
        ]);
    }

    /**
     * Apply filter array to the builder.
     */
    private function applyFilters(BaseBuilder $builder, array $filters): void
    {
        if (!empty($filters['status'])) {
            $builder->where('t.status', $filters['status']);
        }
        if (!empty($filters['employee_id'])) {
            $builder->where('t.employee_id', $filters['employee_id']);
        }
        if (!empty($filters['project_id'])) {
            $builder->where('t.project_id', $filters['project_id']);
        }
        if (!empty($filters['task_id'])) {
            $builder->where('t.task_id', $filters['task_id']);
        }
        if (!empty($filters['customer_id'])) {
            $builder->where('t.customer_id', $filters['customer_id']);
        }
        if (array_key_exists('is_billable', $filters) && $filters['is_billable'] !== '') {
            $builder->where('t.is_billable', (int)$this->toBool($filters['is_billable']));
        }
        if (array_key_exists('is_invoiced', $filters) && $filters['is_invoiced'] !== '') {
            $value = strtolower((string)$filters['is_invoiced']);
            if ($value === 'yes' || $value === '1') {
                $builder->where('t.is_invoiced', 1);
            } elseif ($value === 'no' || $value === '0') {
                $builder->where('t.is_invoiced', 0);
            }
        }
        if (!empty($filters['from_date'])) {
            $builder->where('t.start_time >=', $this->normalizeDateTime($filters['from_date']));
        }
        if (!empty($filters['to_date'])) {
            $endOfDay = $this->normalizeDateTime($filters['to_date'] . ' 23:59:59');
            $builder->where('t.start_time <=', $endOfDay);
        }
    }

    /**
     * Normalise incoming request data.
     */
    private function getRequestData(): array
    {
        $json = $this->request->getJSON(true);
        if (is_array($json) && !empty($json)) {
            return $json;
        }
        return $this->request->getPost() ?: [];
    }

    private function normalizeDateTime($value): ?string
    {
        if (empty($value)) {
            return null;
        }
        $timestamp = strtotime((string)$value);
        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
    }

    private function normalizeFlags(array $payload): array
    {
        foreach (['is_billable', 'is_running', 'is_invoiced'] as $flag) {
            if (array_key_exists($flag, $payload)) {
                $payload[$flag] = (int)$this->toBool($payload[$flag]);
            }
        }
        return $payload;
    }

    private function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        $truthy = ['1', 1, 'true', 'on', 'yes'];
        return in_array($value, $truthy, true);
    }
}
