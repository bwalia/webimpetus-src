<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Vat_returns_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/vat-returns",
 *     tags={"VAT Returns"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid_business_id",
 *          in="query",
 *          required=true,
 *          description="Business UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get all VAT returns"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/vat-returns/{uuid}",
 *     tags={"VAT Returns"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="VAT Return UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single VAT return"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/vat-returns",
 *     tags={"VAT Returns"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="period_key", type="string"),
 *             @OA\Property(property="vat_due_sales", type="number"),
 *             @OA\Property(property="vat_due_acquisitions", type="number"),
 *             @OA\Property(property="total_vat_due", type="number"),
 *             @OA\Property(property="vat_reclaimed_curr_period", type="number"),
 *             @OA\Property(property="net_vat_due", type="number"),
 *             @OA\Property(property="total_value_sales_ex_vat", type="number"),
 *             @OA\Property(property="total_value_purchases_ex_vat", type="number"),
 *             @OA\Property(property="total_value_goods_supplied_ex_vat", type="number"),
 *             @OA\Property(property="total_acquisitions_ex_vat", type="number")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="VAT return created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/vat-returns/{uuid}",
 *     tags={"VAT Returns"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="VAT Return UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="VAT return updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/vat-returns/{uuid}",
 *     tags={"VAT Returns"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="VAT Return UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="VAT return deleted"
 *     )
 * )
 */
class VatReturns extends ResourceController
{
    protected $modelName = 'App\Models\Vat_returns_model';
    protected $format = 'json';

    public function index()
    {
        $model = new Vat_returns_model();
        $uuid_business_id = $this->request->getVar('uuid_business_id');

        if (!$uuid_business_id) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        $data = $model->where('uuid_business_id', $uuid_business_id)->findAll();
        return $this->respond($data);
    }

    public function show($uuid = null)
    {
        $model = new Vat_returns_model();
        $data = $model->where('uuid', $uuid)->first();

        if (!$data) {
            return $this->failNotFound('VAT return not found');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $model = new Vat_returns_model();
        $data = $this->request->getJSON(true);

        if (!isset($data['uuid_business_id'])) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        if ($model->insert($data)) {
            return $this->respondCreated(['message' => 'VAT return created successfully']);
        }

        return $this->fail($model->errors());
    }

    public function update($uuid = null)
    {
        $model = new Vat_returns_model();
        $data = $this->request->getJSON(true);

        $vatReturn = $model->where('uuid', $uuid)->first();
        if (!$vatReturn) {
            return $this->failNotFound('VAT return not found');
        }

        if ($model->where('uuid', $uuid)->set($data)->update()) {
            return $this->respond(['message' => 'VAT return updated successfully']);
        }

        return $this->fail($model->errors());
    }

    public function delete($uuid = null)
    {
        $model = new Vat_returns_model();

        $vatReturn = $model->where('uuid', $uuid)->first();
        if (!$vatReturn) {
            return $this->failNotFound('VAT return not found');
        }

        if ($model->where('uuid', $uuid)->delete()) {
            return $this->respondDeleted(['message' => 'VAT return deleted successfully']);
        }

        return $this->fail('Failed to delete VAT return');
    }
}
