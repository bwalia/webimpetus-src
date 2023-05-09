<?php

namespace App\Models;

use CodeIgniter\Model;

class Product extends Model
{

    private $whereCond = array();
    private $doesUuidBusinessIdFieldExists = false;

    function __construct()
    {
        parent::__construct();
        $this->session = session();
    }

    protected $DBGroup          = 'default';
    protected $table            = 'products';
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

    public function insertOrUpdate($table = null, $data = null, $condition = null)
    {
        if ($table == null) {
            $table = $this->table;
        }
        unset($data["id"]);

        if (@$condition) {
            $query = $this->db->table($table)->update($data, $condition);
            if ($query) {
                return true;
            }
        } else {
            $query = $this->db->table($table)->insert($data);
            if ($query) {
                return true;
            }
        }

        return false;
    }


    public function saveProductData($data)
    {
        $query = $this->db->table($this->table)->insert($data);
        return $query;
    }

    public function saveCategoryData($data)
    {
        $query = $this->db->table("product_categories")->insert($data);
        return $query;
    }

    public function saveKeyValueData($data)
    {
        $query = $this->db->table("key_values")->insertBatch($data);
        return $query;
    }


    public function getKeyValueData($uuid)
    {
        $sql = "SELECT * 
        FROM key_values
        WHERE uuid_product = '" . $uuid . "'";
        $query = $this->db->query($sql);
        $row = $query->getResultArray();
        return $row;
    }

    public function getProduct($id = 0)
    {
        $sql = "SELECT pr.*,pc.uuid_product,pc.uuid_category 
        FROM products AS pr
        LEFT JOIN product_categories AS pc
        ON  pr.uuid = pc.uuid_product
        WHERE pr.uuid = '" . $id . "' AND pr.uuid_business_id = '" . session('uuid_business') . "'";

        $query = $this->db->query($sql);
        $row = $query->getRow();
        return $row;
    }


    public function deleteTableData($tableName, $condition = null)
    {
        if ($condition != null) {
            $query = $this->db->table($tableName)->delete($condition);
            return $query;
        }
        return false;
    }

    public function deleteProduct($id = null)
    {
        $condition = array('uuid' => $id, 'uuid_business_id' => session('uuid_business'));
        $product = $this->db->table($this->table)->where($condition)->get()->getRow();
        if ($product) {
            $product_uuid = $product->uuid;
            $this->db->transBegin();

            $this->db->table("products")->delete($condition);

            $cat_condition = ["uuid_product" => $product_uuid];
            $this->db->table("product_categories")->delete($cat_condition);

            $key_condition = ["uuid_product" => $product_uuid];
            $this->db->table('key_values')->delete($key_condition);

            $this->db->transComplete();

            return true;
        }
        return false;
    }
}
