<?php

namespace App\Controllers\Api\V2;

use CodeIgniter\RESTful\ResourceController;
use App\Controllers\Api_v2;
class ScimGroupController extends ResourceController
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

            if(empty($_GET['uuid_business_id']) || !isset($_GET['uuid_business_id']) || !$_GET['uuid_business_id']){
                $data['data'] = 'You must need to specify the User Business ID';
                return $this->respond($data, 403);
            }
            $data['data'] = $api->common_model->getApiData('businesses', $arr);
            $data['total'] = $api->common_model->getCount('businesses', $arr);
            $data['message'] = 200;
            return $this->respond($data);
        } else {
            $db = \Config\Database::connect();
            $builder = $db->table('businesses');

            $limit = (int)($_GET['limit'] ?? 20);
            $offset = (int)($_GET['offset'] ?? 0);
            $query = $_GET['query'] ?? false;
            $order = $_GET['order'] ?? "name";
            $dir = $_GET['dir'] ?? "asc";

            $sqlQuery = $builder->select('uuid, id, name, created_at');

            if ($query) {
                $sqlQuery = $sqlQuery->like("name", $query);
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
        //
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
