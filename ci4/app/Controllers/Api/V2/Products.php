<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/products",
 *     tags={"Products"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid_business_id",
 *          in="query",
 *          required=false,
 *          description="Business UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get all products"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/products/{id}",
 *     tags={"Products"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Product ID",
 *          @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single product"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/products",
 *     tags={"Products"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="uuid_business_id", type="string"),
 *             @OA\Property(property="product_name", type="string"),
 *             @OA\Property(property="product_code", type="string"),
 *             @OA\Property(property="product_description", type="string"),
 *             @OA\Property(property="product_category", type="string"),
 *             @OA\Property(property="product_price", type="number", format="decimal"),
 *             @OA\Property(property="product_cost", type="number", format="decimal"),
 *             @OA\Property(property="stock_quantity", type="integer"),
 *             @OA\Property(property="product_status", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Product created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/products/{id}",
 *     tags={"Products"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Product ID",
 *          @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="product_name", type="string"),
 *             @OA\Property(property="product_description", type="string"),
 *             @OA\Property(property="product_price", type="number", format="decimal"),
 *             @OA\Property(property="stock_quantity", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Product updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/products/{id}",
 *     tags={"Producs"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Product ID",
 *          @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Product deleted"
 *     )
 * )
 */
class Products extends ResourceController
{
    protected $format = 'json';
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $uuidBusinessId = $this->request->getVar('uuid_business_id');

        $builder = $this->db->table('products');

        if ($uuidBusinessId) {
            $builder->where('uuid_business_id', $uuidBusinessId);
        }

        $data = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->db->table('products')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$data) {
            return $this->failNotFound('Product not found');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        // Set timestamps
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        if ($this->db->table('products')->insert($data)) {
            $insertId = $this->db->insertID();
            return $this->respondCreated(['message' => 'Product created successfully', 'id' => $insertId]);
        }

        return $this->fail('Failed to create product');
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        $product = $this->db->table('products')->where('id', $id)->get()->getRowArray();

        if (!$product) {
            return $this->failNotFound('Product not found');
        }

        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->db->table('products')->where('id', $id)->update($data)) {
            return $this->respond(['message' => 'Product updated successfully']);
        }

        return $this->fail('Failed to update product');
    }

    public function delete($id = null)
    {
        $product = $this->db->table('products')->where('id', $id)->get()->getRowArray();

        if (!$product) {
            return $this->failNotFound('Product not found');
        }

        if ($this->db->table('products')->where('id', $id)->delete()) {
            return $this->respondDeleted(['message' => 'Product deleted successfully']);
        }

        return $this->fail('Failed to delete product');
    }
}
