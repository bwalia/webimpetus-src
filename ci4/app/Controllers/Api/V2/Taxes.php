<?php

namespace App\Controllers\Api\V2;
use App\Controllers\Api_v2;

use CodeIgniter\RESTful\ResourceController;

class Taxes extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $api =  new Api_v2();
        $params = !empty($_GET['params'])?json_decode($_GET['params'],true):[];

        //Pagination Params
        $_GET['page'] = !empty($params['pagination']) && !empty($params['pagination']['page'])?$params['pagination']['page']:1;
        $_GET['perPage'] = !empty($params['pagination']) && !empty($params['pagination']['perPage'])?$params['pagination']['perPage']:10;

        //Sorting params
        $_GET['field'] = !empty($params['sort']) && !empty($params['sort']['field'])?$params['sort']['field']:'';
        $_GET['order'] = !empty($params['sort']) && !empty($params['sort']['order'])?$params['sort']['order']:'';

        //filter by business uuid
        $_GET['q'] = !empty($params['filter']) && !empty($params['filter']['q'])?$params['filter']['q']:'';

        $_GET['uuid_business_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_business_id'])?$params['filter']['uuid_business_id']:false;
        $arr = [];
        if(!empty($_GET['uuid_business_id'])){
            $arr['uuid_business_id'] = $_GET['uuid_business_id'];
        }
        $data['data'] = $api->common_model->getApiData('taxes',$arr,'tax_code as name');
        $data['total'] = $api->common_model->getCount('taxes',$arr);
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
        $data['data'] = $api->common_model->getRow('taxes',$id,'id');
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
        return $this->respond($api->addTax());
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
        return $this->respond($api->updateTax());
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {       
        $api =  new Api_v2();
        $data['data'] = $api->common_model->deleteTableData('taxes',$id,'id');
        $data['status'] = 200;
        return $this->respond($data);    
    }
}
