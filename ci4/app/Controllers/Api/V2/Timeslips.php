<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;

use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/timeslips",
 *     tags={"Timeslips"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Get the timeslips data"
 *     )
 * )
 * 
 *@OA\Get(
 *     path="/api/v2/timeslips/{uuid}",
 *     tags={"Timeslips"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Get the single timeslip data"
 *     )
 * )
 * * * *@OA\Post(
 *     path="/api/v2/timeslips",
 * tags={"Timeslips"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid_business_id",
 *          in="query",
 *          required=true,
 *          description="business uuid required",
 *          @OA\Schema(
 *              type="string"
 *          ),
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Created a timeslip"
 *     )
 * )
 * 
 * 
 *@OA\Put(
 *     path="/api/v2/timeslips/{uuid}",
 *     tags={"Timeslips"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="query",
 *          required=true,
 *          description="The timeslip uuid",
 *          @OA\Schema(
 *              type="string"
 *          ),
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Updated a timeslip data"
 *     )
 * )
 * 
 *@OA\Delete(
 *     path="/api/v2/timeslips/{uuid}",
 *     tags={"Timeslips"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Delete a timeslip"
 *     )
 * )
 */

class Timeslips extends ResourceController
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

        //filter by business uuid, week, month and year
        $_GET['list_week'] = !empty($params['filter']) && !empty($params['filter']['list_week']) ? $params['filter']['list_week'] : '';
        $_GET['list_month'] = !empty($params['filter']) && !empty($params['filter']['list_month']) ? $params['filter']['list_month'] : '';
        $_GET['list_year'] = !empty($params['filter']) && !empty($params['filter']['list_year']) ? $params['filter']['list_year'] : '';
        $_GET['uuid_business_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_business_id']) ? $params['filter']['uuid_business_id'] : $_GET['uuid_business_id'];
        $_GET['q'] = !empty($params['filter']) && !empty($params['filter']['q']) ? $params['filter']['q'] : '';
        return $this->respond($api->timeslips()); //
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $api =  new Api_v2();
        return $this->respond($api->timeslips($id));
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
        return $this->respond($api->addTimeslip());
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
        return $this->respond($api->updateTimeslip());
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->timeSlipsModel->deleteData($id);
        $data['message'] = 200;
        return $this->respond($data);
    }
}
