<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Controllers\Auth;
use App\Models\Blocks_model;
use App\Libraries\UUID;
use CodeIgniter\RESTful\ResourceController;

class Blocks extends ResourceController
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

        if ($params) {
            $_GET['page'] = !empty($params['pagination']) && !empty($params['pagination']['page']) ? $params['pagination']['page'] : 1;
            $_GET['perPage'] = !empty($params['pagination']) && !empty($params['pagination']['perPage']) ? $params['pagination']['perPage'] : 10;

            //Sorting params
            $_GET['field'] = !empty($params['sort']) && !empty($params['sort']['field']) ? $params['sort']['field'] : '';
            $_GET['order'] = !empty($params['sort']) && !empty($params['sort']['order']) ? $params['sort']['order'] : '';

            //filter by business uuid
            $_GET['q'] = !empty($params['filter']) && !empty($params['filter']['q']) ? $params['filter']['q'] : '';

            $_GET['uuid_business_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_business_id']) ? $params['filter']['uuid_business_id'] : $_GET['uuid_business_id'] ?? false;
            $arr = [];
            if (!empty($_GET['uuid_business_id'])) {
                $arr['uuid_business_id'] = $_GET['uuid_business_id'];
            } else {
                $data['data'] = 'You must need to specify the User Business ID';
                return $this->respond($data, 403);
            }
            $data['data'] = $api->common_model->getApiData('blocks_list', $arr);
            $data['total'] = $api->common_model->getCount('blocks_list', $arr);
            $data['message'] = 200;
            return $this->respond($data, $data['message']);
        } else {
            $blockModel = new Blocks_model();
            $limit = $_GET['limit'] ?? 20;
            $offset = $_GET['offset'] ?? 0;
            $query = $_GET['query'] ?? false;
            $order = $_GET['order'] ?? "title";
            $dir = $_GET['dir'] ?? "asc";
            $uuidBusineess = $_GET['uuid_business_id'] ?? false;

            if (!$uuidBusineess) {
                return $this->respond([
                    'data' => [],
                    'message' => 'uuid_business_id is required',
                    'recordsTotal' => 0
                ], 400);
            }

            $sqlQuery = $blockModel
                ->select("uuid, id, title, status, code")
                ->where(['uuid_business_id' => $uuidBusineess]);
            if ($query) {
                $sqlQuery = $sqlQuery->like("title", $query);
            }

            $countQuery = $sqlQuery->countAllResults(false);
            $sqlQuery = $sqlQuery->limit($limit, $offset)->orderBy($order, $dir);

            return $this->respond([
                'data' => $sqlQuery->get()->getResultArray(),
                'recordsTotal' => $countQuery,
            ]);
        }
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->common_model->getRow('blocks_list', $id, 'id');
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
        // $api =  new Api_v2();
        // return $this->respond($api->addBlock());
        $blocksModel = new Blocks_model();
		$data = array(
			'code' => $_POST['code'],
			'title' => $_POST['title'],
			'status' => $_POST['status'],
			'text' => $_POST['text'],
			"uuid_business_id" => $_POST['uuid_business'],
		);

		$uuid = $_POST['uuid'] ?? false;
		if (!$uuid) {
			$isSaved = $blocksModel->saveData($data);
            if ($isSaved) {
                return $this->respond([
                    'data' => $data,
                    'message' => 'Data entered successfully'
                ]);
            }
		} else {
            return $this->respond([
                'message' => 'You are sending uuid in payload. Please use PUT method to update the records.'
            ]);
		}
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
        // return $this->respond($api->updateBlock());
        $blocksModel = new Blocks_model();
		$data = array(
			'code' => $_POST['code'],
			'title' => $_POST['title'],
			'status' => $_POST['status'],
			'text' => $_POST['text'],
			"uuid_business_id" => $_POST['uuid_business'],
		);

		$uuid = $_POST['uuid'] ?? false;
		if ($uuid) {
			$isSaved = $blocksModel->updateDataByUUID($uuid, $data);
            if ($isSaved) {
                return $this->respond([
                    'data' => $data,
                    'message' => 'Data Update successfully'
                ]);
            }
		} else {
            return $this->respond([
                'message' => 'You are not sending uuid in payload. Please use POST method to create the records.'
            ]);
		}
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->common_model->deleteTableData('blocks_list', $id, 'id');
        $data['status'] = 200;
        return $this->respond($data);
    }
}
