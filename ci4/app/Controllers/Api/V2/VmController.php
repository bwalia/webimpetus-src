<?php

namespace App\Controllers\Api\V2;

use App\Models\VmModel;
use CodeIgniter\RESTful\ResourceController;

class VmController extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $vmModel = new VmModel();
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

            // Business Id
            $_GET['uuid_business_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_business_id']) ? $params['filter']['uuid_business_id'] : $_GET['uuid_business_id'] ?? false;
            if (empty($_GET['uuid_business_id']) || !isset($_GET['uuid_business_id']) || !$_GET['uuid_business_id']) {
                $data['data'] = 'You must need to specify the User Business ID';
                return $this->respond($data, 403);
            }

            $vmData = $vmModel->where('uuid_business_id', $_GET['uuid_business_id']);
            if (!empty($_GET['q']) && isset($_GET['q'])) {
                $vmData->like('vm_name', $_GET['q']);
            }
            if (!empty($_GET['field']) && isset($_GET['field']) && !empty($_GET['order']) && isset($_GET['order'])) {
                $vmData->orderBy($_GET['field'], $_GET['order']);
            }
            $count = $vmModel->countAllResults(false);
            $vmData->paginate($_GET['perPage'], 'default', $_GET['page']);

            $data['data'] = $vmData;
            $data['total'] = $count;
            $data['message'] = 200;
            return $this->respond($data);

        } else {

            $limit = $_GET['limit'] ?? 20;
            $offset = $_GET['offset'] ?? 0;
            $query = $_GET['query'] ?? false;
            $order = $_GET['order'] ?? "vm_name";
            $dir = $_GET['dir'] ?? "asc";
            $uuidBusineess = $_GET['uuid_business_id'];

            $sqlQuery = $vmModel->where(['uuid_business_id' => $uuidBusineess]);
            if ($query) {
                $sqlQuery = $sqlQuery->like("vm_name", $query);
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
