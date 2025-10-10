<?php

namespace App\Controllers\Api\V2;

use CodeIgniter\RESTful\ResourceController;
use App\Models\Launchpad_model;
use CodeIgniter\API\ResponseTrait;

/**
 * @OA\Tag(
 *     name="Launchpad",
 *     description="API endpoints for managing bookmarks and quick links"
 * )
 */
class Launchpad extends ResourceController
{
    use ResponseTrait;

    protected $launchpad_model;
    protected $format = 'json';

    public function __construct()
    {
        $this->launchpad_model = new Launchpad_model();
    }

    /**
     * @OA\Get(
     *     path="/api/v2/launchpad",
     *     tags={"Launchpad"},
     *     summary="Get user's bookmarks",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="uuid_business_id",
     *          in="query",
     *          required=true,
     *          description="Business UUID",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="uuid_user_id",
     *          in="query",
     *          required=true,
     *          description="User UUID",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *          name="include_shared",
     *          in="query",
     *          required=false,
     *          description="Include shared bookmarks (default: true)",
     *          @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Get all user bookmarks"
     *     )
     * )
     */
    public function index()
    {
        $uuid_user_id = $this->request->getVar('uuid_user_id');
        $uuid_business_id = $this->request->getVar('uuid_business_id');

        if (!$uuid_user_id || !$uuid_business_id) {
            return $this->respond(['error' => 'uuid_user_id and uuid_business_id are required'], 400);
        }

        $includeShared = $this->request->getVar('include_shared') !== 'false';

        $data = $this->launchpad_model->getUserBookmarks($uuid_user_id, $uuid_business_id, $includeShared);

        return $this->respond(['data' => $data]);
    }

    /**
     * @OA\Get(
     *     path="/api/v2/launchpad/{uuid}",
     *     tags={"Launchpad"},
     *     summary="Get a single bookmark",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          description="Bookmark UUID",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Get bookmark by UUID"
     *     )
     * )
     */
    public function show($uuid = null)
    {
        $data = $this->launchpad_model->where('uuid', $uuid)->first();

        if (!$data) {
            return $this->failNotFound('Bookmark not found');
        }

        return $this->respond(['data' => $data]);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/launchpad",
     *     tags={"Launchpad"},
     *     summary="Create a new bookmark",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "url", "uuid_business_id", "uuid_user_id"},
     *             @OA\Property(property="title", type="string", example="GitHub"),
     *             @OA\Property(property="url", type="string", example="https://github.com"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="uuid_business_id", type="string"),
     *             @OA\Property(property="uuid_user_id", type="string"),
     *             @OA\Property(property="icon_url", type="string", example="https://github.com/favicon.ico"),
     *             @OA\Property(property="color", type="string", example="#667eea"),
     *             @OA\Property(property="category", type="string", example="Development"),
     *             @OA\Property(property="tags", type="string", example="code,git,repository"),
     *             @OA\Property(property="is_favorite", type="integer", example=0),
     *             @OA\Property(property="is_public", type="integer", example=0)
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Bookmark created successfully"
     *     )
     * )
     */
    public function create()
    {
        $input = $this->request->getJSON(true);

        if (!isset($input['uuid_business_id']) || !isset($input['uuid_user_id'])) {
            return $this->fail('uuid_business_id and uuid_user_id are required', 400);
        }

        $input['created'] = date('Y-m-d H:i:s');
        $input['modified'] = date('Y-m-d H:i:s');

        try {
            $this->launchpad_model->insert($input);
            return $this->respondCreated(['status' => true, 'message' => 'Bookmark created successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v2/launchpad/{uuid}",
     *     tags={"Launchpad"},
     *     summary="Update a bookmark",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          description="Bookmark UUID",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="url", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="color", type="string"),
     *             @OA\Property(property="is_favorite", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Bookmark updated successfully"
     *     )
     * )
     */
    public function update($uuid = null)
    {
        $input = $this->request->getJSON(true);
        $input['modified'] = date('Y-m-d H:i:s');

        try {
            $this->launchpad_model->where('uuid', $uuid)->set($input)->update();
            return $this->respond(['status' => true, 'message' => 'Bookmark updated successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/launchpad/{uuid}",
     *     tags={"Launchpad"},
     *     summary="Delete a bookmark",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          description="Bookmark UUID",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Bookmark deleted successfully"
     *     )
     * )
     */
    public function delete($uuid = null)
    {
        try {
            $this->launchpad_model->where('uuid', $uuid)->delete();
            return $this->respond(['status' => true, 'message' => 'Bookmark deleted successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v2/launchpad/click/{uuid}",
     *     tags={"Launchpad"},
     *     summary="Record bookmark click",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          description="Bookmark UUID",
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"uuid_user_id"},
     *             @OA\Property(property="uuid_user_id", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Click recorded successfully"
     *     )
     * )
     */
    public function click($uuid = null)
    {
        $input = $this->request->getJSON(true);

        if (!isset($input['uuid_user_id'])) {
            return $this->fail('uuid_user_id is required', 400);
        }

        try {
            $this->launchpad_model->recordClick($uuid, $input['uuid_user_id']);
            return $this->respond(['status' => true, 'message' => 'Click recorded']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v2/launchpad/share",
     *     tags={"Launchpad"},
     *     summary="Share bookmark with user",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"uuid_bookmark_id", "uuid_shared_with_user_id", "uuid_shared_by_user_id"},
     *             @OA\Property(property="uuid_bookmark_id", type="string"),
     *             @OA\Property(property="uuid_shared_with_user_id", type="string"),
     *             @OA\Property(property="uuid_shared_by_user_id", type="string"),
     *             @OA\Property(property="can_edit", type="integer", example=0)
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Bookmark shared successfully"
     *     )
     * )
     */
    public function share()
    {
        $input = $this->request->getJSON(true);

        try {
            $result = $this->launchpad_model->shareBookmark(
                $input['uuid_bookmark_id'],
                $input['uuid_shared_with_user_id'],
                $input['uuid_shared_by_user_id'],
                $input['can_edit'] ?? 0
            );

            if ($result === false) {
                return $this->respond(['status' => false, 'message' => 'Already shared'], 400);
            }

            return $this->respond(['status' => true, 'message' => 'Bookmark shared successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v2/launchpad/recent",
     *     tags={"Launchpad"},
     *     summary="Get recently clicked bookmarks",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="uuid_user_id",
     *          in="query",
     *          required=true,
     *          description="User UUID",
     *          @OA\Schema(type="string")
     *     ),
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
     *          description="Number of results (default: 10)",
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Get recent bookmarks"
     *     )
     * )
     */
    public function recent()
    {
        $uuid_user_id = $this->request->getVar('uuid_user_id');
        $uuid_business_id = $this->request->getVar('uuid_business_id');
        $limit = $this->request->getVar('limit') ?? 10;

        if (!$uuid_user_id || !$uuid_business_id) {
            return $this->respond(['error' => 'uuid_user_id and uuid_business_id are required'], 400);
        }

        $data = $this->launchpad_model->getRecentBookmarks($uuid_user_id, $uuid_business_id, $limit);

        return $this->respond(['data' => $data]);
    }
}
