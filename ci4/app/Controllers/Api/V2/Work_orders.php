<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Work_orders_model;
use CodeIgniter\RESTful\ResourceController;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="WorkOrders",
 *     description="Manage work orders raised for suppliers."
 * )
 */
class Work_orders extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     *
     * @OA\Get(
     *     path="/api/v2/work_orders",
     *     tags={"WorkOrders"},
     *     summary="List work orders",
     *     description="Returns paginated work orders for a business. Supports the list filter payload used by the front-end and QA harness.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="_format",
     *         in="query",
     *         description="Optional response format override (defaults to json).",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="params",
     *         in="query",
     *         description="JSON encoded object supporting pagination, sorting, and filter keys (uuid_business_id, q).",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of work orders",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="uuid", type="string"),
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="order_number", type="string"),
     *                     @OA\Property(property="date", type="string", format="date"),
     *                     @OA\Property(property="project_code", type="string"),
     *                     @OA\Property(property="total", type="number", format="float"),
     *                     @OA\Property(property="balance_due", type="number", format="float"),
     *                     @OA\Property(property="paid_date", type="string", format="date", nullable=true),
     *                     @OA\Property(property="status", type="string")
     *                 )
     *             ),
     *             @OA\Property(property="recordsTotal", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Missing business identifier"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index()
    {
        $api =  new Api_v2();
        $params = !empty($_GET['params']) ? json_decode($_GET['params'], true) : [];
        if ($params) {
            //Pagination Params
            $_GET['page'] = !empty($params['pagination']) && !empty($params['pagination']['page']) ? $params['pagination']['page'] : 1;
            $_GET['perPage'] = !empty($params['pagination']) && !empty($params['pagination']['perPage']) ? $params['pagination']['perPage'] : 10;
    
            //Sorting params
            $_GET['field'] = !empty($params['sort']) && !empty($params['sort']['field']) ? $params['sort']['field'] : '';
            $_GET['order'] = !empty($params['sort']) && !empty($params['sort']['order']) ? $params['sort']['order'] : '';
    
            //filter by business uuid
            $_GET['q'] = !empty($params['filter']) && !empty($params['filter']['q']) ? $params['filter']['q'] : '';
    
            $_GET['uuid_business_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_business_id']) ? $params['filter']['uuid_business_id'] : $_GET['uuid_business_id'] ?? false;
            $arr = [];
            if (!empty($_GET['uuid_business_id'])) {
                $arr['uuid_business_id'] = $_GET['uuid_business_id'];
            } else {
                $data['data'] = 'You must need to specify the User Business ID';
                return $this->respond($data, 403);
            }
            $data['data'] = $api->work_orders_model->getApiV2Invoice($_GET['uuid_business_id']);
            $data['total'] = $api->common_model->getCount('work_orders', $arr);
            $data['message'] = 200;
            return $this->respond($data);
        } else {
            $workOrderModel = new Work_orders_model();
            $limit = (int)($_GET['limit'] ?? 20);
            $offset = (int)($_GET['offset'] ?? 0);
            $query = $_GET['query'] ?? false;
            $order = $_GET['order'] ?? "order_number";
            $dir = $_GET['dir'] ?? "asc";
            $uuidBusineess = $_GET['uuid_business_id'];

            $sqlQuery = $workOrderModel
                ->select("uuid, id, order_number, date, project_code, total, balance_due, paid_date, status")
                ->where(['uuid_business_id' => $uuidBusineess]);
            if ($query) {
                $sqlQuery = $sqlQuery->like("order_number", $query);
            }

            $countQuery = $sqlQuery->countAllResults(false);
            $sqlQuery = $sqlQuery->limit($limit, $offset)->orderBy($order, $dir);

            return $this->respond([
                'data' => $sqlQuery->get()->getResultArray(),
                'recordsTotal' => $countQuery,
            ]);
        }
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     *
     * @OA\Get(
     *     path="/api/v2/work_orders/{uuid}",
     *     tags={"WorkOrders"},
     *     summary="Retrieve a work order",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Work order details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Work order not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->work_orders_model->getApiV2SingleInvoice($id);
        $data['message'] = 200;
        return $this->respond($data);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     *
     * @OA\Post(
     *     path="/api/v2/work_orders",
     *     tags={"WorkOrders"},
     *     summary="Create a work order",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"client_id","uuid_business_id"},
     *                 @OA\Property(property="client_id", type="string", description="Customer identifier."),
     *                 @OA\Property(property="uuid_business_id", type="string", description="Business UUID."),
     *                 @OA\Property(property="bill_to", type="string"),
     *                 @OA\Property(property="order_number", type="string"),
     *                 @OA\Property(property="date", type="string", format="date"),
     *                 @OA\Property(property="project_code", type="string"),
     *                 @OA\Property(property="status", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Work order created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", @OA\Property(property="uuid", type="string"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function create()
    {
        $api =  new Api_v2();
        return $this->respond($api->addWorkOrder());
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     *
     * @OA\Put(
     *     path="/api/v2/work_orders/{uuid}",
     *     tags={"WorkOrders"},
     *     summary="Update a work order",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"uuid","uuid_business_id","client_id"},
     *                 @OA\Property(property="uuid", type="string"),
     *                 @OA\Property(property="uuid_business_id", type="string"),
     *                 @OA\Property(property="client_id", type="string"),
     *                 @OA\Property(property="bill_to", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="project_code", type="string"),
     *                 @OA\Property(property="date", type="string", format="date")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Work order updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=404, description="Work order not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update($id = null)
    {
        $api =  new Api_v2();
        return $this->respond($api->updateWorkOrder());
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     *
     * @OA\Delete(
     *     path="/api/v2/work_orders/{uuid}",
     *     tags={"WorkOrders"},
     *     summary="Delete a work order",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deletion result",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="boolean"),
     *             @OA\Property(property="status", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Work order not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function delete($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->common_model->deleteTableData('work_orders', $id, 'uuid');
        $data['status'] = 200;
        return $this->respond($data);
    }
}
