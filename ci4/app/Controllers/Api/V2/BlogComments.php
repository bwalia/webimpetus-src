<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Database\ConnectionInterface;

/**
 * @OA\Get(
 *     path="/api/v2/blog-comments",
 *     tags={"Blog Comments"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="blog_uuid",
 *          in="query",
 *          required=false,
 *          description="Filter by blog UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get all blog comments"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/v2/blog-comments/{id}",
 *     tags={"Blog Comments"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Comment UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Get single comment"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/v2/blog-comments",
 *     tags={"Blog Comments"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="blog_uuid", type="string"),
 *             @OA\Property(property="Name", type="string"),
 *             @OA\Property(property="Email", type="string"),
 *             @OA\Property(property="Comment", type="string"),
 *             @OA\Property(property="Approved", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="Comment created"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/v2/blog-comments/{id}",
 *     tags={"Blog Comments"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Comment UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="Comment", type="string"),
 *             @OA\Property(property="Approved", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Comment updated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/v2/blog-comments/{id}",
 *     tags={"Blog Comments"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Comment UUID",
 *          @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Comment deleted"
 *     )
 * )
 */
class BlogComments extends ResourceController
{
    protected $format = 'json';
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $blogUuid = $this->request->getVar('blog_uuid');

        $builder = $this->db->table('blog_comments');

        if ($blogUuid) {
            $builder->where('blog_uuid', $blogUuid);
        }

        $data = $builder->orderBy('Created', 'DESC')->get()->getResultArray();
        return $this->respond($data);
    }

    public function show($id = null)
    {
        $data = $this->db->table('blog_comments')
            ->where('uuid', $id)
            ->get()
            ->getRowArray();

        if (!$data) {
            return $this->failNotFound('Comment not found');
        }

        return $this->respond($data);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        // Generate UUID if not provided
        if (!isset($data['uuid'])) {
            $data['uuid'] = $this->generateUUID();
        }

        // Set Created timestamp
        if (!isset($data['Created'])) {
            $data['Created'] = date('Y-m-d H:i:s');
        }

        // Default to not approved
        if (!isset($data['Approved'])) {
            $data['Approved'] = 0;
        }

        if ($this->db->table('blog_comments')->insert($data)) {
            return $this->respondCreated(['message' => 'Comment created successfully', 'uuid' => $data['uuid']]);
        }

        return $this->fail('Failed to create comment');
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        $comment = $this->db->table('blog_comments')->where('uuid', $id)->get()->getRowArray();

        if (!$comment) {
            return $this->failNotFound('Comment not found');
        }

        if ($this->db->table('blog_comments')->where('uuid', $id)->update($data)) {
            return $this->respond(['message' => 'Comment updated successfully']);
        }

        return $this->fail('Failed to update comment');
    }

    public function delete($id = null)
    {
        $comment = $this->db->table('blog_comments')->where('uuid', $id)->get()->getRowArray();

        if (!$comment) {
            return $this->failNotFound('Comment not found');
        }

        if ($this->db->table('blog_comments')->where('uuid', $id)->delete()) {
            return $this->respondDeleted(['message' => 'Comment deleted successfully']);
        }

        return $this->fail('Failed to delete comment');
    }

    private function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
