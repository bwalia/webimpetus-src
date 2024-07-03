<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;
use App\Libraries\UUID;
use App\Models\Companies;
use App\Models\Contact;
use App\Models\CustomerContactModel;
use App\Models\Customers_model;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Get(
 *     path="/api/v2/contacts",
 *     tags={"Contacts"},
 *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Get the contacts data"
 *     )
 * )
 * 
 *@OA\Get(
 *     path="/api/v2/contacts/{uuid}",
 *      tags={"Contacts"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Get the single contact data"
 *     )
 * )
 * 
 * 
 * 
 * * 
 *  * * *@OA\Post(
 *     path="/api/v2/contacts",
 * tags={"Contacts"},
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
 *         description="Created a contact data"
 *     )
 * )
 * 
 *@OA\Put(
 *     path="/api/v2/contacts/{uuid}",
 * tags={"Contacts"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *          name="uuid",
 *          in="query",
 *          required=true,
 *          description="The contact uuid",
 *          @OA\Schema(
 *              type="string"
 *          ),
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Updated a contact data"
 *     )
 * )
 * 
 *@OA\Delete(
 *     path="/api/v2/contacts/{uuid}",
 * tags={"Contacts"},
 * *     security={
 *       {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response="200",
 *         description="Delete a contact"
 *     )
 * )
 */
class Contacts extends ResourceController
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
        if (!empty($_GET['uuid_business_id'])) {
            $arr['uuid_business_id'] = $_GET['uuid_business_id'];
        } else {
            $data['data'] = 'You must need to specify the User Business ID';
            return $this->respond($data, 403);
        }
        $data['data'] = $api->common_model->getApiData('contacts', $arr, 'CONCAT_WS(" ", first_name, surname) as name');
        $data['total'] = $api->common_model->getCount('contacts', $arr);
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
        $data['data'] = $api->common_model->getRow('contacts', $id, 'uuid');
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
        $companyModel = new Companies();
        $customerContactModel = new CustomerContactModel();
        $customers_model = new Customers_model();
        $contactModel = new Contact();
        $uuid = $_POST['uuid'] ?? false;
		$data = $_POST;
       
        if(!isset($data['allow_web_access'])){
            $data['allow_web_access'] = 0;
        }
        if(empty($uuid)){
            $data['uuid'] = UUID::v5(UUID::v4(), 'contacts_saving');
            $data['uuid_business_id'] = session('uuid_business');
        }
        if(strlen($data['password']) > 0){
            $data['password'] = md5($data['password']);
        }
        if ($data['linked_module_types'] == "customers") {
            $data['client_id'] = $data['customer_id'];
        }
        if ($data['linked_module_types'] == "companies") {
            $data['client_id'] = $data['company_id'];
        }

        if (isset($data['uuid_business'])) {
            $data["uuid_business_id"] = $data['uuid_business'];
            unset($data['uuid_business']);
        }

        unset($data['companyUUID']);
        unset($data['customerUUID']);
        unset($data['customer_id']);
        unset($data['company_id']);
		$response = $contactModel->insertOrUpdateByUUID($uuid, $data);

        if ($response) {
            $companyUUID = $_POST['companyUUID'] ?? false;
            if (($companyUUID && isset($companyUUID) && $companyUUID != "") || $data['linked_module_types'] == "companies") {
                if (!isset($companyUUID) || !$companyUUID || $companyUUID == "") {
                    $companyUUID = $_POST['company_id'];
                    if (!$this->isValidUUID($companyUUID)) {
                        $companyData = $companyModel->select('uuid')->where('id', $companyUUID)->first();
                        $companyUUID = $companyData['uuid'];
                    }
                }
                $companyModel->deleteRelationDataByContactCompany($data['uuid'], $companyUUID);
                $customerContactModel->deleteDataByContact($data['uuid']);
                $relationData = [
                    'company_uuid' => $companyUUID,
                    'contact_uuid' => $data['uuid'],
                    'uuid' => UUID::v5(UUID::v4(), 'company__contact')
                ];
                $companyModel->insertRelationData($relationData);
            }
            $customerUUID = $_POST['customerUUID'] ?? false;
            if (($customerUUID && isset($customerUUID) && $customerUUID != "") || $data['linked_module_types'] == "customers") {
                $customerUUID = $_POST['customer_id'];
                if (!$this->isValidUUID($customerUUID)) {
                    $customerData = $customers_model->select('uuid')->where('id', $customerUUID)->first();
                    $customerUUID = $customerData['uuid'];
                }
                $customerContactModel->deleteDataByContactCustomer($data['uuid'], $customerUUID);
                $companyModel->deleteRelationDataByContact($data['uuid']);
                $cusConData = [
                    'customer_uuid' => $customerUUID,
                    'contact_uuid' => $data['uuid'],
                    'uuid' => UUID::v5(UUID::v4(), 'customer__contact')
                ];
                $customerContactModel->saveData($cusConData);
            }
        }
        $response_data['data'] = $data;
        $response_data['status'] = 200;
        return $this->respond($response_data, 200);
    }

    public function isValidUUID($uuid) {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        return preg_match($pattern, $uuid) === 1;
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
        return $this->respond($api->updateContact());
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->common_model->deleteTableData('contacts', $id, 'uuid');
        $data['status'] = 200;
        return $this->respond($data);
    }
}
