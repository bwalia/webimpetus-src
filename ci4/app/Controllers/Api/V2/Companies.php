<?php

namespace App\Controllers\Api\V2;

use CodeIgniter\RESTful\ResourceController;
use App\Controllers\Companies as CompaniesController;
use App\Models\Companies as CompaniesModel;
use App\Libraries\UUID;

class Companies extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $companiesModel = new CompaniesModel();
        $limit = $_GET['limit'] ?? 20;
        $offset = $_GET['offset'] ?? 0;
        $query = $_GET['query'] ?? false;
        $order = $_GET['order'] ?? "company_name";
        $dir = $_GET['dir'] ?? "asc";
        $uuidBusineess = $_GET['uuid_business_id'];

        $sqlQuery = $companiesModel
            ->where(['uuid_business_id' => $uuidBusineess])
            ->limit($limit, $offset)
            ->orderBy($order, $dir)
            ->get()
            ->getResultArray();
        if ($query) {
            $sqlQuery = $companiesModel
                ->where(['uuid_business_id' => $uuidBusineess])
                ->like("company_name", $query)
                ->limit($limit, $offset)
                ->orderBy($order, $dir)
                ->get()
                ->getResultArray();
        }

        $countQuery = $companiesModel
            ->where(["uuid_business_id" => $uuidBusineess])
            ->countAllResults();
        if ($query) {
            $countQuery = $companiesModel
                ->where(["uuid_business_id" => $uuidBusineess])
                ->like("company_name", $query)
                ->countAllResults();
        }

        return $this->respond([
            'data' => $sqlQuery,
            'total' => $countQuery
        ]);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $company = new CompaniesModel();
        $company = $company->where('uuid', $id)->get()->getRowArray();
        return $this->respond(['data' => $company]);
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
        $companiesModel = new CompaniesModel();
        
        $postData = $_POST;
        if (!$postData || !isset($postData) || empty($postData)) {
            $postData = $this->request->getJSON();
            $postData = (array) $postData;
        }
        $uuid = $postData['uuid'] ?? false;
        
        if (!$uuid || empty($uuid) || !isset($uuid)) {
            $postData['uuid'] = UUID::v5(UUID::v4(), 'roles');
        }

        $postData['uuid_business_id'] = $postData['uuid_business'];

        unset($postData['contactID']);
        unset($postData['uuid_business']);
        $id = $companiesModel->insertOrUpdateByUUID($uuid, $postData);

        if ($id) {
            $companiesModel->deleteRelationData($postData['uuid']);
            $jsonData = $this->request->getJSON();
            $jsonData = (array) $jsonData;
            $contactID = $jsonData['contactID'];
            $relationData = [
                'company_uuid' => $postData['uuid'],
                'contact_uuid' => $contactID,
                'uuid' => UUID::v5(UUID::v4(), 'company__contact')
            ];
            $companiesModel->insertRelationData($relationData);
        }

        return $this->respond(['data' => $postData]);
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
        $companiesModel = new CompaniesModel();
        $uuid = $_POST['uuid'] ?? false;
        $postData = $_POST;

        if (!$uuid || empty($uuid) || !isset($uuid)) {
            return $this->respond(['data'=> ['message' => 'UUID is must be present while updating records']], 400);
        }
        $postData['uuid_business_id'] = $_POST['uuid_business_id'];

        unset($postData['contactID']);
        $id = $companiesModel->insertOrUpdateByUUID($uuid, $postData);

        if ($id) {
            $companiesModel->deleteRelationData($postData['uuid']);
            $contactID = $_POST['contactID'];
            $relationData = [
                'company_uuid' => $postData['uuid'],
                'contact_uuid' => $contactID,
                'uuid' => UUID::v5(UUID::v4(), 'company__contact')
            ];
            $companiesModel->insertRelationData($relationData);
        }

        return $this->respond(['data' => $postData]);
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $companiesModel = new CompaniesModel();
        if ($id) {
            $companiesModel->delete(['uuid' => $id ]);
            $isDeleted = $companiesModel->deleteRelationData($id);
            if ($isDeleted) {
                $this->respond(['data'=> ['message'=> 'Deleted Successfully']], 200);
            }
        }
        $this->respond(['data'=> ['message'=> 'Something is wrong here']], 400);
    }
}
