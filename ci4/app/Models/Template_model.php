<?php

namespace App\Models;

use CodeIgniter\Model;

class Template_model extends Model
{
    protected $table = 'templates';
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
            return $this->findAll();
        } else {
            return $this->getWhere(['id' => $id]);
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

    public function updateData($id = null, $data = null)
    {
        $query = $this->db->table($this->table)->update($data, array('id' => $id));
        return $query;
    }

    public function getMatchRows($data)
    {
        return $this->getWhere($data)->getResultArray();
    }
}
