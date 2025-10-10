<?php

namespace App\Controllers\Api\V2;

use CodeIgniter\RESTful\ResourceController;
use App\Models\Deployments_model;
use CodeIgniter\API\ResponseTrait;

/**
 * @OA\Tag(
 *     name="Deployments",
 *     description="API endpoints for managing service deployments across DTAP environments"
 * )
 */
class Deployments extends ResourceController
{
    use ResponseTrait;

    protected $deployments_model;
    protected $format = 'json';

    public function __construct()
    {
        $this->deployments_model = new Deployments_model();
    }

    /**
     * @OA\Get(
     *     path="/api/v2/deployments",
     *     tags={"Deployments"},
     *     summary="Get all deployments",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="uuid_business_id",
     *          in="query",
     *          required=true,
     *          description="Business UUID",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="environment",
     *          in="query",
     *          required=false,
     *          description="Filter by environment (Development, Testing, Acceptance, Production)",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="deployment_status",
     *          in="query",
     *          required=false,
     *          description="Filter by status",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Get all deployments"
     *     )
     * )
     */
    public function index()
    {
        $uuid_business_id = $this->request->getVar('uuid_business_id');

        if (!$uuid_business_id) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        $filters = [
            'environment' => $this->request->getVar('environment'),
            'deployment_status' => $this->request->getVar('deployment_status'),
            'uuid_service_id' => $this->request->getVar('uuid_service_id'),
        ];

        $data = $this->deployments_model->getDeploymentsWithRelations($uuid_business_id, $filters);

        return $this->respond(['data' => $data]);
    }

    /**
     * @OA\Get(
     *     path="/api/v2/deployments/{uuid}",
     *     tags={"Deployments"},
     *     summary="Get a single deployment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          description="Deployment UUID",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Get deployment by UUID"
     *     )
     * )
     */
    public function show($uuid = null)
    {
        $data = $this->deployments_model->where('uuid', $uuid)->first();

        if (!$data) {
            return $this->failNotFound('Deployment not found');
        }

        return $this->respond(['data' => $data]);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/deployments",
     *     tags={"Deployments"},
     *     summary="Create a new deployment",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"deployment_name", "uuid_business_id", "environment"},
     *             @OA\Property(property="deployment_name", type="string", example="Deploy User Service v2.1"),
     *             @OA\Property(property="uuid_business_id", type="string"),
     *             @OA\Property(property="uuid_service_id", type="string"),
     *             @OA\Property(property="environment", type="string", enum={"Development", "Testing", "Acceptance", "Production"}),
     *             @OA\Property(property="version", type="string", example="2.1.0"),
     *             @OA\Property(property="deployment_type", type="string", enum={"Initial", "Update", "Hotfix", "Rollback", "Configuration"}),
     *             @OA\Property(property="deployment_status", type="string", enum={"Planned", "In Progress", "Completed", "Failed", "Rolled Back"}),
     *             @OA\Property(property="deployment_date", type="string", format="date-time"),
     *             @OA\Property(property="deployed_by", type="string"),
     *             @OA\Property(property="uuid_task_id", type="string"),
     *             @OA\Property(property="uuid_incident_id", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="deployment_notes", type="string"),
     *             @OA\Property(property="rollback_plan", type="string"),
     *             @OA\Property(property="git_commit_hash", type="string"),
     *             @OA\Property(property="git_branch", type="string"),
     *             @OA\Property(property="priority", type="string", enum={"Low", "Medium", "High", "Critical"})
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Deployment created successfully"
     *     )
     * )
     */
    public function create()
    {
        $input = $this->request->getJSON(true);

        if (!isset($input['uuid_business_id'])) {
            return $this->fail('uuid_business_id is required', 400);
        }

        $input['created'] = date('Y-m-d H:i:s');
        $input['modified'] = date('Y-m-d H:i:s');

        try {
            $this->deployments_model->insert($input);
            return $this->respondCreated(['status' => true, 'message' => 'Deployment created successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v2/deployments/{uuid}",
     *     tags={"Deployments"},
     *     summary="Update a deployment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          description="Deployment UUID",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="deployment_name", type="string"),
     *             @OA\Property(property="deployment_status", type="string"),
     *             @OA\Property(property="completed_date", type="string", format="date-time"),
     *             @OA\Property(property="health_check_status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Deployment updated successfully"
     *     )
     * )
     */
    public function update($uuid = null)
    {
        $input = $this->request->getJSON(true);
        $input['modified'] = date('Y-m-d H:i:s');

        try {
            $this->deployments_model->where('uuid', $uuid)->set($input)->update();
            return $this->respond(['status' => true, 'message' => 'Deployment updated successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/deployments/{uuid}",
     *     tags={"Deployments"},
     *     summary="Delete a deployment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          description="Deployment UUID",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Deployment deleted successfully"
     *     )
     * )
     */
    public function delete($uuid = null)
    {
        try {
            $this->deployments_model->where('uuid', $uuid)->delete();
            return $this->respond(['status' => true, 'message' => 'Deployment deleted successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v2/deployments/stats",
     *     tags={"Deployments"},
     *     summary="Get deployment statistics",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="uuid_business_id",
     *          in="query",
     *          required=true,
     *          description="Business UUID",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Get deployment statistics by environment"
     *     )
     * )
     */
    public function stats()
    {
        $uuid_business_id = $this->request->getVar('uuid_business_id');

        if (!$uuid_business_id) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        $stats = $this->deployments_model->getDeploymentStats($uuid_business_id);

        return $this->respond(['data' => $stats]);
    }
}
