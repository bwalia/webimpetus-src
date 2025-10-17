<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;

use App\Models\Sales_invoice_model;
use CodeIgniter\RESTful\ResourceController;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="SalesInvoices",
 *     description="Manage customer sales invoices."
 * )
 */
class Sales_invoices extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     *
     * @OA\Get(
     *     path="/api/v2/sales_invoices",
     *     tags={"SalesInvoices"},
     *    
     *     summary="List sales invoices",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="_format",
     *         in="query",
     *         description="Optional response format override",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="params",
     *         in="query",
     *         description="JSON encoded payload controlling pagination, sorting and filters.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated collection of sales invoices",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="message", type="integer")
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
            $data['data'] = $api->sales_invoice_model->getApiV2Invoice($_GET['uuid_business_id']);
            $data['total'] = $api->common_model->getCount('sales_invoices', $arr);
            $data['message'] = 200;
            return $this->respond($data);
        } else {
            $salesModel = new Sales_invoice_model();
            $limit = (int)($_GET['limit'] ?? 20);
            $offset = (int)($_GET['offset'] ?? 0);
            $query = $_GET['query'] ?? false;
            $order = $_GET['order'] ?? "invoice_number";
            $dir = $_GET['dir'] ?? "asc";
            $uuidBusineess = $_GET['uuid_business_id'];

            $sqlQuery = $salesModel
                ->where(['uuid_business_id' => $uuidBusineess])
                ->limit($limit, $offset)
                ->orderBy($order, $dir)
                ->get()
                ->getResultArray();
            if ($query) {
                $sqlQuery = $salesModel
                    ->where(['uuid_business_id' => $uuidBusineess])
                    ->like("invoice_number", $query)
                    ->limit($limit, $offset)
                    ->orderBy($order, $dir)
                    ->get()
                    ->getResultArray();
            }

            $countQuery = $salesModel
                ->where(["uuid_business_id" => $uuidBusineess])
                ->countAllResults();
            if ($query) {
                $countQuery = $salesModel
                    ->where(["uuid_business_id" => $uuidBusineess])
                    ->like("invoice_number", $query)
                    ->countAllResults();
            }

            return $this->respond([
                'data' => $sqlQuery,
                'recordsTotal' => $countQuery,
            ]);
        }
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->sales_invoice_model->getApiV2SingleInvoice($id);
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
     *     path="/api/v2/sales_invoices",
     *     tags={"SalesInvoices"},
     *     summary="Create a sales invoice",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"terms","uuid_business_id","date","due_date","supplier"},
     *                 @OA\Property(property="terms", type="string"),
     *                 @OA\Property(property="uuid_business_id", type="string"),
     *                 @OA\Property(property="date", type="string"),
     *                 @OA\Property(property="due_date", type="string"),
     *                 @OA\Property(property="supplier", type="string"),
     *                 @OA\Property(property="project_code", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sales invoice created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function create()
    {
        $api =  new Api_v2();
        return $this->respond($api->addSalesInvoice());
    }

    /**
     * Return the editable properties of a resource object
     *
     * @OA\Get(
     *     path="/api/v2/sales_invoices/{uuid}",
     *     tags={"SalesInvoices"},
     *     summary="Retrieve a sales invoice",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sales invoice details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Invoice not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
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
     *     path="/api/v2/sales_invoices/{uuid}",
     *     tags={"SalesInvoices"},
     *     summary="Update a sales invoice",
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
     *                 required={"uuid","uuid_business_id"},
     *                 @OA\Property(property="uuid", type="string"),
     *                 @OA\Property(property="uuid_business_id", type="string"),
     *                 @OA\Property(property="terms", type="string"),
     *                 @OA\Property(property="project_code", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sales invoice updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=404, description="Invoice not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update($id = null)
    {
        $api =  new Api_v2();
        return $this->respond($api->updateSalesInvoice());
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     *
     * @OA\Delete(
     *     path="/api/v2/sales_invoices/{uuid}",
     *     tags={"SalesInvoices"},
     *     summary="Delete a sales invoice",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deletion status",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="boolean"),
     *             @OA\Property(property="status", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Invoice not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function delete($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->common_model->deleteTableData('sales_invoices', $id, 'uuid');
        $data['status'] = 200;
        return $this->respond($data);
    }
}
