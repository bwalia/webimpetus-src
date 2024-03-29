<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceDomainsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'service__domains';
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

    public function checkRecordExists($domainUuid, $serviceUuid)
    {
        return $this->getWhere(['domain_uuid' => $domainUuid, 'service_uuid' => $serviceUuid])->getResultArray();
    }

    public function getRows($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['domain_uuid' => $id])->getResultArray();
        }   
    }
    public function getRowsByService($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['service_uuid' => $id, 'domain_uuid !=' => NULL,  'domain_uuid <>' => ''])->getResultArray();
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
    public function deleteDataByDomain($id)
    {
        $query = $this->db->table($this->table)->delete(array('domain_uuid' => $id));
        return $query;
    }
    public function deleteDataByService($id)
    {
        $query = $this->db->table($this->table)->delete(array('service_uuid' => $id));
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
}
