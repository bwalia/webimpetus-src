<?php

namespace App\Controllers\Api\V2;

use CodeIgniter\RESTful\ResourceController;
use App\Controllers\Api_v2;
use App\Models\Users_model;
use DateTime;
use DateTimeZone;
use App\Libraries\UUID;

class ScimUserController extends ResourceController
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

            $data['data'] = $api->userModel->getApiV2Users();
            $data['total'] = $api->userModel->getApiV2UsersCount();
            $data['message'] = 200;
            return $this->respond($data);
        } else {
            $userModel = new Users_model();

            $limit = (int)($_GET['limit'] ?? 20);
            $offset = (int)($_GET['offset'] ?? 0);
            $query = $_GET['query'] ?? false;
            $order = $_GET['order'] ?? "name";
            $dir = $_GET['dir'] ?? "asc";

            $sqlQuery = $userModel
                ->select("uuid, id, name, email, address, status")
                ->where(['status' => 1]);
            if ($query) {
                $sqlQuery = $sqlQuery->like("name", $query);
            }

            $countQuery = $sqlQuery->countAllResults(false);
            $sqlQuery = $sqlQuery->limit($limit, $offset)->orderBy($order, $dir);
            $users = $sqlQuery->get()->getResultArray();

            $scimUsers = [];
            foreach ($users as $user) {
                $username = strtolower(str_replace(' ', '_', $user['name']));
                $active = filter_var($user['status'], FILTER_VALIDATE_BOOLEAN);
                $date = new DateTime('now', new DateTimeZone('UTC'));
                $formattedDate = $date->format('Y-m-d\TH:i:s\Z');
                $scimUsers[] = [
                    'schemas' => ['urn:ietf:params:scim:schemas:core:2.0:User'],
                    'id' => $user['uuid'],
                    'userName' => $username,
                    'name' => [
                        'formatted' => $user['name'],
                        'familyName' => "",
                        'givenName' => $user['name'],
                    ],
                    'displayName' => $user['name'],
                    'active' => $active,
                    'emails' => [
                        [
                            'value' => $user['email'],
                            'type' => 'work',
                            'primary' => true
                        ]
                    ],
                    'meta' => [
                        'resourceType' => 'User',
                        'created' => $formattedDate,
                        'lastModified' => $formattedDate
                    ]
                ];
            }

            return $this->respond([
                'schemas' => ['urn:ietf:params:scim:schemas:core:2.0:ListResponse'],
                'totalResults' => $countQuery,
                'Resources' => $scimUsers
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
        echo '<pre>'; print_r($id); echo '</pre>'; die;
        
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
        $userModel = new Users_model();
        $reqData = $this->request->getJSON();
        log_message('error', json_encode($reqData));
        $email = $reqData->emails[0]->value;

        $user = $userModel->where('email', $email)->first();

        if (!$user && empty($user)) {
            $uuid =UUID::v5(UUID::v4(), 'tasks');
            $usrData = [
                'name' => $reqData->displayName ?? "",
                'address' => $reqData->addresses ?? "",
                'status' => $reqData->active,
                'uuid' => $uuid,
                'email' => $email,
            ];
            
            $createUser = $userModel->insert($usrData);
            return $this->respond([
                "success" => $createUser
            ], 201);
        }

    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {}

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $userModel = new Users_model();
        $reqData = $this->request->getJSON();
        log_message('error', json_encode($reqData));
        $usrData = [
            'name' => $reqData->displayName,
            'address' => $reqData->addresses,
            'status' => $reqData->active,
        ];
        $updateUser = $userModel->where("uuid", $id)->set($usrData)->update();
        return $this->respond([
            "success" => $updateUser
        ], 204);
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
