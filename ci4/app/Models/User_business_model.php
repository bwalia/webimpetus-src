<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class User_business_model extends Model
{
    protected $table = 'user_business';
    private $whereCond = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function getRows($id = false)
    {
        if ($id === false) {
            return $this->findAll();
        } else {
            return $this->getWhere(['id' => $id]);
        }
    }

    public function insertOrUpdate($id = null, $data = null)
    {

        unset($data["id"]);

        $user_business = $this->db->table($this->table)->where("user_id", $data['user_id'])->get()->getRowArray();
        if ($user_business) {
            $query = $this->db->table($this->table)->update($data, array('user_id' => $data['user_id']));
            session()->setFlashdata('message', 'Data updated Successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
            return $user_business['id'];
        } else if (@$id) {
            $query = $this->db->table($this->table)->update($data, array('uuid' => $id));
            if ($query) {
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
                return $id;
            }
        } else {
            $query = $this->db->table($this->table)->insert($data);
            if ($query) {
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
                return $this->db->insertID();
            }
        }
        return false;
    }


    public function saveUserbusines($data)
    {
        $this->db->table($this->table)->insert($data);
        return $this->db->insertID();
    }

    public function deleteBusiness($id)
    {
        $query = $this->db->table($this->table)->delete(array('id' => $id));
        return $query;
    }

    public function updateBusiness($data, $id)
    {
        $query = $this->db->table($this->table)->update($data, array('id' => $id));
        return $query;
    }

    public function getResult($id = false)
    {
        $query = $this->db->table($this->table)->where('id', $id)->get()->getResultObject();
        return $query;
    }
    public function getResultByUUID($uuid = false)
    {
        $query = $this->db->table($this->table)->where('uuid', $uuid)->get()->getResultObject();
        return $query;
    }

    public function getApiBusiness($where = array())
    {
        $table = $this->table;
        $selectFields = array(
            $table . '.*',
            'users.name',
        );
        $this->select($selectFields);
        $this->join('users', 'users.id = ' . $table . '.user_id');

        if(!empty($where)) $this->where($where);

        // if(!empty($_GET['field']) && !empty($_GET['order'])){
        //     $this->orderBy($table . '.' .$_GET['field'],$_GET['order']);
        // }else {
        //     $this->orderBy($table . '.id','ASC');
        // }         
        
        $_GET['perPage'] = !empty($_GET['perPage'])?$_GET['perPage']:10;
        return $this->paginate($_GET['perPage']);
    }
}
