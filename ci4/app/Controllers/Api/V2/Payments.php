<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Payments_model;
use App\Libraries\UUID;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/payments",
 *     tags={"Payments"},
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
 *         description="Get all payments"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/payments/{uuid}",
 *     tags={"Payments"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Payment UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single payment"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/payments",
 *     tags={"Payments"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="payment_date", type="string", format="date"),
 *             @OA\Property(property="payment_type", type="string", enum={"Supplier Payment", "Expense Payment", "Refund", "Other"}),
 *             @OA\Property(property="payee_name", type="string"),
 *             @OA\Property(property="amount", type="number", format="decimal"),
 *             @OA\Property(property="currency", type="string", enum={"GBP", "USD", "EUR", "INR"}),
 *             @OA\Property(property="payment_method", type="string"),
 *             @OA\Property(property="bank_account_uuid", type="string"),
 *             @OA\Property(property="reference", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="status", type="string", enum={"Draft", "Pending", "Completed", "Cancelled"})
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Payment created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/payments/{uuid}",
 *     tags={"Payments"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Payment UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Payment updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/payments/{uuid}",
 *     tags={"Payments"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="path",
 *          required=true,
 *          description="Payment UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Payment deleted"
 *     )
 * )
 */
class Payments extends ResourceController
{
    protected $modelName = 'App\Models\Payments_model';
    protected $format = 'json';

    public function index()
    {
        $model = new Payments_model();
        $uuid_business_id = $this->request->getVar('uuid_business_id');

        if (!$uuid_business_id) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        $limit = $this->request->getVar('limit') ?? 1000;
        $offset = $this->request->getVar('offset') ?? 0;

        $data = $model
            ->where('uuid_business_id', $uuid_business_id)
            ->orderBy('payment_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll($limit, $offset);

        return $this->respond(['data' => $data]);
    }

    public function show($uuid = null)
    {
        $model = new Payments_model();
        $data = $model->getPaymentByUuid($uuid);

        if (!$data) {
            return $this->failNotFound('Payment not found');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $model = new Payments_model();
        $data = $this->request->getJSON(true);

        if (!isset($data['uuid_business_id'])) {
            return $this->respond(['error' => 'uuid_business_id is required'], 400);
        }

        // Generate UUID if not provided
        if (!isset($data['uuid'])) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'payments_api');
        }

        // Generate payment number if not provided
        if (!isset($data['payment_number'])) {
            $data['payment_number'] = $model->getNextPaymentNumber($data['uuid_business_id']);
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
                'message' => 'Payment created successfully',
                'uuid' => $data['uuid'],
                'payment_number' => $data['payment_number']
            ]);
        }

        return $this->fail($model->errors());
    }

    public function update($uuid = null)
    {
        $model = new Payments_model();
        $data = $this->request->getJSON(true);

        $payment = $model->where('uuid', $uuid)->first();
        if (!$payment) {
            return $this->failNotFound('Payment not found');
        }

        // Don't allow updating posted payments
        if ($payment['is_posted'] == 1) {
            return $this->respond(['error' => 'Cannot update posted payment'], 400);
        }

        if ($model->where('uuid', $uuid)->set($data)->update()) {
            return $this->respond(['message' => 'Payment updated successfully']);
        }

        return $this->fail($model->errors());
    }

    public function delete($uuid = null)
    {
        $model = new Payments_model();

        $payment = $model->where('uuid', $uuid)->first();
        if (!$payment) {
            return $this->failNotFound('Payment not found');
        }

        // Don't allow deleting posted payments
        if ($payment['is_posted'] == 1) {
            return $this->respond(['error' => 'Cannot delete posted payment'], 400);
        }

        if ($model->where('uuid', $uuid)->delete()) {
            return $this->respondDeleted(['message' => 'Payment deleted successfully']);
        }

        return $this->fail('Failed to delete payment');
    }
}
