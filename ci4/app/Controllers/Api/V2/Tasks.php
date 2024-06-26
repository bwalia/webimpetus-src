<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Tasks_model;

use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/tasks",
 *     tags={"Tasks"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Get the tasks data"
 *     )
 * )
 * 
 *@OA\Get(
 *     path="/api/v2/tasks/{uuid}",
 *      tags={"Tasks"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Get the single task data"
 *     )
 * )
 * 
 * * *@OA\Post(
 *     path="/api/v2/tasks",
 * tags={"Tasks"},
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
 *         description="Created a task"
 *     )
 * )
 * 
 * 
 *@OA\Put(
 *     path="/api/v2/tasks/{uuid}",
 * tags={"Tasks"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="query",
 *          required=true,
 *          description="The task uuid",
 *          @OA\Schema(
 *              type="string"
 *          ),
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Updated a task data"
 *     )
 * )
 * 
 *@OA\Delete(
 *     path="/api/v2/tasks/{uuid}",
 * tags={"Tasks"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Delete a task"
 *     )
 * )
 */
class Tasks extends ResourceController
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
        if(empty($_GET['uuid_business_id']) || !isset($_GET['uuid_business_id']) || !$_GET['uuid_business_id']){
            $data['data'] = 'You must need to specify the User Business ID';
            return $this->respond($data, 403);
        }
        $data['data'] = $api->tasksModel->getApiTaskList($_GET['uuid_business_id']);
        $data['total'] = $api->tasksModel->getTasksCount($_GET['uuid_business_id']);
        $data['message'] = 200;
        return $this->respond($data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->tasksModel->getTaskByUUID($id);
        $data['status'] = 200;
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
        return $this->respond($api->addTask());
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
        return $this->respond($api->updateTask());
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->common_model->deleteTableData('tasks', $id, 'uuid');
        $data['status'] = 200;
        return $this->respond($data);
    }

    public function tasksByPId($bId, $pId, $eId)
    {
        $model = new Tasks_model();
        $records = $model->tasksByPId($bId, $pId, $eId, $_GET);
        $data['data'] = $records['data'];
        $data['status'] = 200;
        $data['total'] = $records['total'];
        return $this->respond($data);
    }

    public function tasksStatusByEId($bId, $eId)
    {
        $model = new Tasks_model();
        $records = $model->tasksStatusByEId($bId, $eId, $_GET);
        $inReviewCount = 0;
        $assignedCount = 0;
        $completedCount = 0;
        $allInReviewCount = 0;
        $allAssignedCount = 0;
        $allCompletedCount = 0;
        $count = 0;
        $allCount = 0;
        foreach($records['data'] as $record) {
            if ($record['status'] == "inReview") {
                $inReviewCount++;
            } else if ($record['status'] == "assigned") {
                $assignedCount++;
            } else if ($record['status'] == "completed") {
                $completedCount++;
            }
            $count++;
        }
        foreach($records['all_tasks_status'] as $allTaskStatus) {
            if ($allTaskStatus['status'] == "inReview") {
                $allInReviewCount++;
            } else if ($allTaskStatus['status'] == "assigned") {
                $allAssignedCount++;
            } else if ($allTaskStatus['status'] == "completed") {
                $allCompletedCount++;
            }
            $allCount++;
        }

        $data['data'] = [
            "inReview" => $inReviewCount,
            "assigned" => $assignedCount,
            "completed" => $completedCount,
            "assignedTasks" => $count,
            "allBusinessTasks" => $records['total_tasks_business'],
            "allBusinessProjects" => $records["total_projects_business"],
            "assignedProjects" => $records['total_projects_assigned'],
            "assignedAverage" => $completedCount / $count * 100,
            "allProjectsAverage" => $allCompletedCount / $records['total_tasks_business'] * 100
        ];
        $data['status'] = 200;
        return $this->respond($data);
    }

    public function updateStatusByUuid()
    {
        $request = $this->request->getJSON();
        $status = $request->status;
        $uuid = $request->id;
        $model = new Tasks_model();
        $records = $model->updateStatusByUUID($uuid, $status);
        return $this->respond($records);
    }
}
