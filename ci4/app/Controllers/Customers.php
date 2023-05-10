<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Core\Common_model;
use App\Libraries\UUID;

class Customers extends CommonController
{

    function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getAdditionalData($id)
    {
        //die(session('uuid_business'));
        $model = new Common_model();
        $builder = $this->db->table("contacts");
        $builder->select("id as contact_id,first_name,surname,email as contact_email");
        $builder->where("client_id", $id);
        $builder->where("uuid_business_id", session('uuid_business'));
        $data["contacts"]  = $builder->get()->getResultArray();
        //echo $this->db->getLastQuery();
        //print_r($data["contacts"]);die;
        return  $data;
    }

    public function update()
    {
        $post = $this->request->getPost();
        $data["company_name"] = @$post["company_name"];
        $data["acc_no"] = @$post["acc_no"];
        $data["status"] = @$post["status"];
        $data["contact_firstname"] = @$post["contact_firstname"];
        $data["contact_lastname"] = @$post["contact_lastname"];
        $data["email"] = @$post["email"];
        $data["address1"] = @$post["address1"];
        $data["address2"] = @$post["address2"];
        $data["city"] = @$post["city"];
        $data["country"] = @$post["country"];
        $data["postal_code"] = @$post["postal_code"];
        $data["phone"] = @$post["phone"];
        $data["notes"] = $post["notes"];
        $data["supplier"] = @$post["supplier"];
        $data["website"] = @$post["website"];
        $data["categories"] = json_encode(@$post["categories"]);
        $data["uuid_business_id"] = session('uuid_business');

        $data["uuid"] = $uuid = $post["uuid"];
        if (empty($data["uuid"])) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'customers');
        }
        $response = $this->model->insertOrUpdateByUUID($uuid, $data);
        if (!$response) {
            session()->setFlashdata('message', 'Something wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        } else {

            // $row_data = $this->model->getRowsByUUID($data["uuid"])->getRow();
            // $id = $row_data->uuid;

            $i = 0;
            foreach ($post["first_name"] as $firstName) {
                $contact["first_name"] = $firstName;
                $contact["client_id"] = $uuid;
                $contact["surname"] = $post["surname"][$i];
                $contact["email"] = $post["contact_email"][$i];
                $contact["uuid_business_id"] = session('uuid_business');
                $contactId =  @$post["contact_id"][$i];
                if(empty($contactId))$contact["uuid"] = UUID::v5(UUID::v4(), 'contacts');
                if (strlen(trim($firstName)) > 0 || strlen(trim($contact["surname"]) > 0) || strlen(trim($contact["email"]) > 0)) {
                    $this->insertOrUpdate("contacts", $contactId, $contact);
                }
                $i++;
            }

            $this->model->deleteTableData("customer_categories", $uuid, "customer_id");

            if (isset($post["categories"])) {
                foreach ($post["categories"] as $key => $categories_id) {

                    $c_data = [];

                    $c_data['customer_id'] = $uuid;
                    $c_data['categories_id'] = $categories_id;

                    $this->model->insertTableData($c_data, "customer_categories");
                }
            }
        }

        return redirect()->to('/' . $this->table);
    }


    public function insertOrUpdate($table, $id = null, $data = null)
    {
        unset($data["id"]);

        if (@$id > 0) {

            $builder = $this->db->table($table);
            $builder->where('id', $id);
            $result = $builder->update($data);

            if ($result) {
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
                return $id;
            }
        } else {
            $query = $this->db->table($table)->insert($data);
            if ($query) {
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
                return $this->db->insertID();
            }
        }

        return false;
    }


    public function deleteCustomer()
    {
        $customerId = $this->request->getPost("customerId");
        $res = $this->model->deleteTableData("contacts", $customerId);
        return $res;
    }
}
