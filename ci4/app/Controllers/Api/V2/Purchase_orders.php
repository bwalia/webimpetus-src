<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Models\Purchase_orders_model;
use CodeIgniter\RESTful\ResourceController;

class Purchase_orders extends ResourceController
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
        if ($params && !empty($params)) {
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
            $data['data'] = $api->purchase_orders_model->getApiV2Invoice($_GET['uuid_business_id']);
            $data['total'] = $api->common_model->getCount('purchase_orders', $arr);
            $data['message'] = 200;
            return $this->respond($data);
        } else {
            $purchaseOrderModel = new Purchase_orders_model();
            $limit = $_GET['limit'] ?? 20;
            $offset = $_GET['offset'] ?? 0;
            $query = $_GET['query'] ?? false;
            $order = $_GET['order'] ?? "order_number";
            $dir = $_GET['dir'] ?? "asc";
            $uuidBusineess = $_GET['uuid_business_id'];

            $sqlQuery = $purchaseOrderModel
                ->select("purchase_orders.uuid, purchase_orders.id, purchase_orders.order_number, purchase_orders.date, purchase_orders.project_code, purchase_orders.total, purchase_orders.balance_due, purchase_orders.paid_date, purchase_orders.status")
                ->join('customers', 'customers.id = purchase_orders.client_id', 'left')
                ->where(['purchase_orders.uuid_business_id' => $uuidBusineess]);
            if ($query) {
                $sqlQuery = $sqlQuery->like("order_number", $query);
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
        $data['data'] = $api->purchase_orders_model->getApiV2SingleInvoice($id);
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
        return $this->respond($api->addPurchaseOrder());
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
        return $this->respond($api->updatePurchaseOrder());
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->common_model->deleteTableData('purchase_orders', $id, 'uuid');
        $data['status'] = 200;
        return $this->respond($data);
    }
}
