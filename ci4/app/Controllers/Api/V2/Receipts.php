<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Receipts_model;
use App\Libraries\UUID;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/receipts",
 *     tags={"Receipts"},
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
 *     @OA\Parameter(
 *          name="limit",
 *          in="query",
 *          required=false,
 *          description="Limit number of results",
 *          @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *          name="offset",
 *          in="query",
 *          required=false,
 *          description="Offset for pagination",
 *          @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get all receipts"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/receipts/{uuid}",
 *     tags={"Receipts"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Receipt UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single receipt"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/receipts",
 *     tags={"Receipts"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="receipt_date", type="string", format="date"),
 *             @OA\Property(property="receipt_type", type="string", enum={"Customer Payment", "Sales Receipt", "Deposit", "Other"}),
 *             @OA\Property(property="payer_name", type="string"),
 *             @OA\Property(property="amount", type="number", format="decimal"),
 *             @OA\Property(property="currency", type="string", enum={"GBP", "USD", "EUR", "INR"}),
 *             @OA\Property(property="payment_method", type="string"),
 *             @OA\Property(property="bank_account_uuid", type="string"),
 *             @OA\Property(property="reference", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="status", type="string", enum={"Draft", "Pending", "Cleared", "Cancelled"})
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Receipt created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/receipts/{uuid}",
 *     tags={"Receipts"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Receipt UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Receipt updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/receipts/{uuid}",
 *     tags={"Receipts"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Receipt UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Receipt deleted"
 *     )
 * )
 */
class Receipts extends ResourceController
{
    protected $modelName = 'App\Models\Receipts_model';
    protected $format = 'json';

    public function index()
    {
        $model = new Receipts_model();
        $uuid_business_id = $this->request->getVar('uuid_business_id');

        if (!$uuid_business_id) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        $limit = $this->request->getVar('limit') ?? 1000;
        $offset = $this->request->getVar('offset') ?? 0;

        $data = $model
            ->where('uuid_business_id', $uuid_business_id)
            ->orderBy('receipt_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll($limit, $offset);

        return $this->respond(['data' => $data]);
    }

    public function show($uuid = null)
    {
        $model = new Receipts_model();
        $data = $model->getReceiptByUuid($uuid);

        if (!$data) {
            return $this->failNotFound('Receipt not found');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $model = new Receipts_model();
        $data = $this->request->getJSON(true);

        if (!isset($data['uuid_business_id'])) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        // Generate UUID if not provided
        if (!isset($data['uuid'])) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'receipts_api');
        }

        // Generate receipt number if not provided
        if (!isset($data['receipt_number'])) {
            $data['receipt_number'] = $model->getNextReceiptNumber($data['uuid_business_id']);
        }

        // Set defaults
        if (!isset($data['status'])) {
            $data['status'] = 'Draft';
        }

        if (!isset($data['currency'])) {
            $data['currency'] = 'GBP';
        }

        if (!isset($data['is_posted'])) {
            $data['is_posted'] = 0;
        }

        if ($model->insert($data)) {
            return $this->respondCreated([
                'message' => 'Receipt created successfully',
                'uuid' => $data['uuid'],
                'receipt_number' => $data['receipt_number']
            ]);
        }

        return $this->fail($model->errors());
    }

    public function update($uuid = null)
    {
        $model = new Receipts_model();
        $data = $this->request->getJSON(true);

        $receipt = $model->where('uuid', $uuid)->first();
        if (!$receipt) {
            return $this->failNotFound('Receipt not found');
        }

        // Don't allow updating posted receipts
        if ($receipt['is_posted'] == 1) {
            return $this->respond(['error' => 'Cannot update posted receipt'], 400);
        }

        if ($model->where('uuid', $uuid)->set($data)->update()) {
            return $this->respond(['message' => 'Receipt updated successfully']);
        }

        return $this->fail($model->errors());
    }

    public function delete($uuid = null)
    {
        $model = new Receipts_model();

        $receipt = $model->where('uuid', $uuid)->first();
        if (!$receipt) {
            return $this->failNotFound('Receipt not found');
        }

        // Don't allow deleting posted receipts
        if ($receipt['is_posted'] == 1) {
            return $this->respond(['error' => 'Cannot delete posted receipt'], 400);
        }

        if ($model->where('uuid', $uuid)->delete()) {
            return $this->respondDeleted(['message' => 'Receipt deleted successfully']);
        }

        return $this->fail('Failed to delete receipt');
    }
}
