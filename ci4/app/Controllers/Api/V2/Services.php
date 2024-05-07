<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Service_model;
use CodeIgniter\RESTful\ResourceController;

class Services extends ResourceController
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
            if (!empty($_GET['uuid_business_id'])) {
                $arr['uuid_business_id'] = $_GET['uuid_business_id'];
            } else {
                $data['data'] = 'You must need to specify the User Business ID';
                return $this->respond($data, 403);
            }
            $data['data'] = $api->common_model->getApiData('services', $arr);
            $data['total'] = $api->common_model->getCount('services', $arr);
            $data['message'] = 200;
            return $this->respond($data);
        } else {
            $servicesModel = new Service_model();
            $limit = $_GET['limit'] ?? 20;
            $offset = $_GET['offset'] ?? 0;
            $query = $_GET['query'] ?? false;
            $order = $_GET['order'] ?? "name";
            $dir = $_GET['dir'] ?? "asc";
            $uuidBusineess = $_GET['uuid_business_id'];

            $servicesData = $servicesModel->getServciesRows($limit, $offset, $order, $dir, $query, $uuidBusineess);

            return $this->respond([
                'data' => $servicesData['data'],
                'recordsTotal' => $servicesData['total'],
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
        $service = $api->common_model->getRow('services', $id, 'uuid', true);
        $service['blocks'] = $api->common_model->getCommonData('blocks_list', ['uuid_linked_table' => $id]);
        $data['data'] = $service;
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
        //
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
        //
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
}
