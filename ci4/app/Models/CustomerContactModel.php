<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class CustomerContactModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'customer__contact';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    private $whereCond = array();

    public function __construct()
    {
        parent::__construct();
        if ($this->db->fieldExists('uuid_business_id', $this->table)) {

            $this->whereCond['uuid_business_id'] = session('uuid_business');
        }
    }

    public function getRows($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['id' => $id]);
        }   
    }

    public function getRowsByUUID($uuid = false)
    {
        $whereCond = $this->whereCond;

        if ($uuid === false) {
            if (empty($whereCond)) {
                return $this->findAll();
            } else {
                return $this->getWhere($whereCond)->getResultArray();
            }
        } else {
            $whereCond = array_merge(array('uuid' => $uuid), $whereCond);
            return $this->getWhere($whereCond);
        } 
    }

    public function getRowsByCustomerUUID($cUuid = false)
    {
        if($cUuid === false){
            return $this->findAll();
        }else{
            return $this->where(['customer_uuid' => $cUuid])->get()->getResultArray();
        }   
    }
	
	public function getRowsByService($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['service_uuid' => $id])->getResultArray();
        }   
    }

    public function saveData($data)
    {
        $query = $this->db->table($this->table)->insert($data);
        return $query;
    }

    public function deleteData($id)
    {
        $query = $this->db->table($this->table)->delete(array('id' => $id));
        return $query;
    }
    public function deleteDataByUUID($id)
    {
        $query = $this->db->table($this->table)->delete(array('uuid' => $id));
        return $query;
    }
    public function deleteDataByCustomer($id)
    {
        $query = $this->db->table($this->table)->delete(array('customer_uuid' => $id));
        return $query;
    }
    public function deleteDataByContact($id)
    {
        $query = $this->db->table($this->table)->delete(array('contact_uuid' => $id));
        return $query;
    }

    public function updateData($id = null, $data = null)
    {
        $query = $this->db->table($this->table)->update($data, array('id' => $id));
        return $query;
    }

    public function getLastInserted()
    {
        return $this->db->insertID();
    }

    public function insertOrUpdate($table, $id = null, $data = null)
    {
        unset($data["id"]);
        $field = is_numeric($id) ? 'id' : 'uuid';
        if (@$id) {
            $query = $this->db->table($table)->update($data, array($field => $id));
            if ($query) {
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
            }
        } else {
            $query = $this->db->table($table)->insert($data);
            $id =  $this->db->insertID();
            if ($query) {
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
            }
        }

        return $id;
    }

    public function getRowsWithServiceName()
    {
        $this->select('service__domains.*');
        $this->select('services.name as sname');
        $this->join('services', 'service__domains.service_uuid = services.uuid', 'LEFT');
        return $this->findAll();
    }

    public function getContacts($customerUUID)
    {
        $query = [];
        if ($customerUUID) {
            $query = $this->db->table("customer__contact")
                    ->select("contact_uuid")
                    ->join("contacts", "customer__contact.contact_uuid = contacts.uuid")
                    ->select("contacts.first_name, contacts.surname, contacts.email, contacts.direct_phone, contacts.mobile")
                    ->where("customer_uuid", $customerUUID)
                    ->get()
                    ->getResultArray();
        }
        return $query;
    }
}
