<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Blocks_model;
use App\Models\Cat_model;
use App\Models\Content_model;
use App\Models\Core\Common_model;
use App\Models\Customers_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/webpages",
 *     tags={"Webpages"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Get the webpage data"
 *     )
 * )
 * 
 *@OA\Get(
 *     path="/api/v2/webpages/{uuid}",
 *      tags={"Webpages"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Get the single webpage data"
 *     )
 * )
 * 
 * 
     
 */
class Webpages extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $api =  new Api_v2();
        $params = !empty($_GET['params']) ? json_decode($_GET['params'], true) : [];
        $catId = $_GET['category_id'];
        //Pagination Params
        $_GET['page'] = !empty($params['pagination']) && !empty($params['pagination']['page']) ? $params['pagination']['page'] : 1;
        $_GET['perPage'] = !empty($params['pagination']) && !empty($params['pagination']['perPage']) ? $params['pagination']['perPage'] : 10;

        //Sorting params
        $_GET['field'] = !empty($params['sort']) && !empty($params['sort']['field']) ? $params['sort']['field'] : '';
        $_GET['order'] = !empty($params['sort']) && !empty($params['sort']['order']) ? $params['sort']['order'] : '';

        //filter by business uuid
        $_GET['q'] = !empty($params['filter']) && !empty($params['filter']['q']) ? $params['filter']['q'] : $_GET['q'] ?? '';
        $_GET['category_id'] = !empty($params['filter']) && !empty($params['filter']['category_id']) ? $params['filter']['category_id'] : $catId;
        $_GET['uuid_business_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_business_id']) ? $params['filter']['uuid_business_id'] : $_GET['uuid_business_id'] ?? false;

        echo '<pre>';
        print_r($_GET);
        echo '</pre>';
        die;

        if (empty($_GET['uuid_business_id']) || !isset($_GET['uuid_business_id']) || !$_GET['uuid_business_id']) {
            $data['data'] = 'You must need to specify the User Business ID';
            return $this->respond($data, 403);
        }
        // $data['data'] = $api->webpages($category_id);
        // $data['total'] = $api->userModel->getApiV2UsersCount();
        // $data['message'] = 200;
        return $this->respond($api->webpages($_GET['category_id'] ?? $catId, $_GET['q']));
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $api =  new Api_v2();
        return $this->respond($api->webpagesEdit($id));
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
     */
    public function create()
    {
        // $api =  new Api_v2();
        // return $this->respond($api->addTimeslip());
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
     */
    public function update($id = null)
    {
        // $api =  new Api_v2();
        // return $this->respond($api->addTimeslip());
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        //
    }

    public function getWebPages($bId, $contactId)
    {
        $customerModel = new Customers_model();
        $contentListModel = new Content_model();
        $blockModel = new Blocks_model();
        $data = [];
        $customerDetails = $customerModel->getRows($contactId)->getRowArray();
        if (!empty($customerDetails) && $customerDetails) {
            $categoryIds = $customerDetails['categories'];
            if (!empty($categoryIds) && $categoryIds && isset($categoryIds)) {
                $contentList = $contentListModel->getRowByCatId($categoryIds, $bId)->getRowArray();
                if (!empty($contentList) && $contentList) {
                    $data['content'] = $contentList;
                    $blocks = $blockModel->getRowsByWebId($contentList['id'])->getResultArray();
                    $data['blocks'] = $blocks;
                } else {
                    return $this->respond([
                        'error' => 'No Content Found.!',
                        'status' => 401
                    ]);
                }
            } else {
                return $this->respond([
                    'error' => 'No Category Found.!',
                    'status' => 401
                ]);
            }
        } else {
            return $this->respond([
                'error' => 'No Customer Found.!',
                'status' => 401
            ]);
        }
        return $this->respond([
            'data' => $data,
            'status' => 200
        ]);
    }

    public function getBlogsByCategory($bId, $contactId)
    {
        $categoryModel = new Cat_model();
        $contentListModel = new Content_model();
        $data = [];
        $category = $categoryModel->getRowByContactId($contactId, $bId)->getRowArray();
        if (!empty($category) && $category) {
            $getContents = $categoryModel->getContentByCat($category['id'], $bId)->getResultArray();
            if (!empty($getContents) && $getContents) {
                $contentIds = array_map(function ($v, $k) {
                    return $v['contentid'];
                }, $getContents, array_keys($getContents));

                if (is_array($contentIds)) {
                    $content = $contentListModel->getDataWhereIN($contentIds, "id");
                    $data['blogs'] = $content;
                }
            } else {
                return $this->respond([
                    'error' => 'No Blog Found.!',
                    'status' => 401
                ]);
            }
        } else {
            return $this->respond([
                'error' => 'No Category Found.!',
                'status' => 401
            ]);
        }

        return $this->respond([
            'data' => $data,
            'status' => 200
        ]);
    }

    public function getPublicBlogs($bCode)
    {
        $commonModel = new Common_model();
        $contentListModel = new Content_model();
        $data = [];
        $businessInfo = $commonModel->getSingleRowWhere("businesses", $bCode, "business_code");
        if (!empty($businessInfo) && $businessInfo) {
            $content = $contentListModel->getPublicDataWhere($businessInfo['uuid'], "uuid_business_id", 1);
            if (empty($content) && !$content) {
                return $this->respond([
                    'error' => 'No Public Blog Found.',
                    'status' => 401
                ]);
            }
            $data['content'] = $content;
        } else {
            return $this->respond([
                'error' => 'No Business Found.',
                'status' => 401
            ]);
        }
        return $this->respond([
            'data' => $data,
            'status' => 200
        ]);
    }

    public function getPublicBlog($bCode, $contentId)
    {
        $commonModel = new Common_model();
        $contentListModel = new Content_model();
        $data = [];
        $businessInfo = $commonModel->getSingleRowWhere("businesses", $bCode, "business_code");
        if (!empty($businessInfo) && $businessInfo) {
            $content = $contentListModel->getContentByUUID($contentId)->getRowArray();
            if (empty($content) && !$content) {
                return $this->respond([
                    'error' => 'No Public Blog Found.',
                    'status' => 404
                ]);
            }
            $data['content'] = $content;
        } else {
            return $this->respond([
                'error' => 'No Business Found.',
                'status' => 404
            ]);
        }
        return $this->respond([
            'data' => $data,
            'status' => 200
        ]);
    }

    public function getBlogsByCode($bCode, $contactId, $blogUuid)
    {
        $commonModel = new Common_model();
        $contentListModel = new Content_model();
        $data = [];
        $content = $contentListModel->getPublicDataWhere($blogUuid, "uuid", "");
        if (empty($content) && !$content) {
            return $this->respond([
                'error' => 'No Public Blog Found.',
                'status' => 401
            ]);
        }
        $data['content'] = $content;
        return $this->respond([
            'data' => $data,
            'status' => 200
        ]);
    }
}
