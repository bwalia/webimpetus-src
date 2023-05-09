<?php

namespace App\Models;

use CodeIgniter\Model;

class ContentImage extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'content_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    // protected $useTimestamps = false;
    // protected $dateFormat    = 'datetime';
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

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


    protected $businessUuid;
    private $whereCond = array();

    public function __construct()
    {
        parent::__construct();
        $this->businessUuid = session('uuid_business');
        $this->whereCond['uuid_business_id'] = $this->businessUuid;
    }


    public function getRows($id = false)
    {
        if ($id === false) {
            return $this->where($this->whereCond)->findAll();
        } else {
            $whereCond = array_merge(['id' => $id], $this->whereCond);
            return $this->getWhere($whereCond);
        }
    }


    public function getImages($id = false)
    {
        if ($id === false) {
            return $this->where($this->whereCond)->findAll();
        } else {
            $whereCond = array_merge(['content_id' => $id], $this->whereCond);
            return $this->getWhere($whereCond);
        }
    }

}
