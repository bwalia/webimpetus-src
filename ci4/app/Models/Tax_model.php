<?php

namespace App\Models;

use CodeIgniter\Model;

class Tax_model extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'taxes';
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


    public function __construct()
    {
        parent::__construct();

        $this->businessUuid = session('uuid_business');
    }

    public function getRows($id = false)
    {
        if ($id === false) {
            return $this->findAll();
        } else {
            return $this->getWhere(['id' => $id]);
        }
    }

    public function getList()
    {
        $builder = $this->db->table($this->table);
        $builder->where($this->table . ".uuid_business_id",  $this->businessUuid);
        return $builder->get()->getResultArray();
    }

    public function deleteData($id)
    {
        $query = $this->db->table($this->table)->delete(array('id' => $id));
        return $query;
    }

    public function updateData($id = null, $data = null)
    {
        $query = $this->db->table($this->table)->update($data, array('id' => $id));
        return $query;
    }
}
