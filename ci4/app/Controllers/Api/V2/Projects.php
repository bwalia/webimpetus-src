<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;

use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/projects",
 *     tags={"Projects"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Get the projects data"
 *     )
 * )
 * 
 *@OA\Get(
 *     path="/api/v2/projects/{uuid}",
 *      tags={"Projects"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Get the single project data"
 *     )
 * )
 * 
 * * 
 *  * * *@OA\Post(
 *     path="/api/v2/projects",
 * tags={"Projects"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Created a sprint data"
 *     )
 * )
 * 
 * 
 *@OA\Put(
 *     path="/api/v2/projects/{uuid}",
 * tags={"Projects"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="query",
 *          required=true,
 *          description="The project uuid",
 *          @OA\Schema(
 *              type="string"
 *          ),
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Updated a project data"
 *     )
 * )
 * 
 *@OA\Delete(
 *     path="/api/v2/projects/{uuid}",
 * tags={"Projects"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Delete a project"
 *     )
 * )
 */
class Projects extends ResourceController
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
        if(empty($_GET['uuid_business_id']) || !isset($_GET['uuid_business_id']) || !$_GET['uuid_business_id']){
            $data['data'] = 'You must need to specify the User Business ID';
            return $this->respond($data, 403);
        }
        $data['data'] = $api->common_model->getApiData('projects', $arr);
        $data['total'] = $api->common_model->getCount('projects', $arr);
        $data['message'] = 200;
        return $this->respond($data);
    }

    function isUUID($str)
    {
        if (preg_match('/^\d+$/', $str) === 1)
            return false;
        if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $str) === 1)
            return true;
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $selector = "id";
        if ($this->isUUID($id)) {
            $selector = "uuid";
        }

        $api =  new Api_v2();
        $data['data'] = $api->common_model->getRow('projects', $id, $selector);
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
     */
    public function create()
    {
        $api =  new Api_v2();
        return $this->respond($api->addProject());
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
        $api =  new Api_v2();
        return $this->respond($api->updateProject());
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->common_model->deleteTableData('projects', $id, 'id');
        $data['status'] = 200;
        return $this->respond($data);
    }

    public function projectsByBId($bid)
    {
        $api =  new Api_v2();
        $records = $api->projects_model->getBusinessProjectList($bid, $_GET);

        $data['data'] = $records['data'];
        $data['status'] = 200;
        $data['total'] = $records['total'];
        return $this->respond($data);
    }
}
