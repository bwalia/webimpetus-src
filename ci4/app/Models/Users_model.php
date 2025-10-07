<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class Users_model extends Model
{
    protected $table = 'users';
    protected $allowedFields    = [
        'uuid',
        'name',
        'email',
        'password',
        'address',
        'status',
        'role',
        'notes',
        'date_time',
        'permissions',
        'uuid_business_id',
        'language_code',
        'token',
        'profile_img',
    ];
    private $whereCond = array();

    public function __construct()
    {
        parent::__construct();
        if ($this->db->fieldExists('uuid_business_id', $this->table)) {
            $this->whereCond['uuid_business_id'] = session('uuid_business');
        }
    }

    public function search($keyword)
    {
        if (!empty($keyword)) {
            return $this->like('name', $keyword);
        }
        return $this;
    }

    public function getUser($id = false, $all = false)
    {
        $whereCond = $this->whereCond;
        if ($id === false) {
            $whereCond = array_merge(['role!=' => 1], $whereCond);
            return $this->where($whereCond)->findAll();
        }else if ($all === true) {
            //$whereCond = array_merge(['role!=' => 1], $whereCond);
            return $this->where($whereCond)->findAll();
        } else {
            $whereCond = array_merge(['id' => $id], $whereCond);
            return $this->getWhere($whereCond);
        }
    }

    public function getUserByUUID($uuid = false)
    {   
        $whereCond = $this->whereCond;
        if ($uuid === false) {
            $whereCond = array_merge(['role!=' => 1], $whereCond);
            return $this->where($whereCond)->findAll();
        } else {
            $whereCond = array_merge(['uuid' => $uuid], $whereCond);
            return $this->getWhere(array_filter($whereCond));
        }
    }
    public function userByUUID($uuid = false)
    {   
        $whereCond = $this->whereCond;
        if ($uuid === false) {
            $whereCond = array_merge(['role!=' => 1], $whereCond);
            return $this->where($whereCond)->findAll();
        } else {
            $whereCond = array_merge(['uuid' => $uuid], $whereCond);
            return $this->where(array_filter($whereCond))->first();
        }
    }


    public function getApiUsers($id = false)
    {   
        $whereCond = $this->whereCond;
        if ($id == false) {
            $whereCond = [];
        } else {
            $whereCond = ['id' => $id];
        }
        return $this->where($whereCond)->first();
    }

    public function getApiV2Users($id = false, $whereCond = [])
    {
        $_GET['perPage'] = !empty($_GET['perPage'])?$_GET['perPage']:10;
        // Cast to int for type safety with CI 4.5+
        $perPage = (int)$_GET['perPage'];
        
        $fields = $this->getFieldNames('users');
        $unset = array('password','id','uuid');
        $fields = array_diff($fields,$unset);
        //print_r($fields);die;
        $this->select(implode(',',$fields).",uuid as id,id as ci4_internal_id,
        CASE WHEN status = 1 THEN 'true' ELSE 'false' END AS status
        ");
        $this->where($whereCond);
        if(!empty($_GET['field']) && !empty($_GET['order'])){
            $this->orderBy($_GET['field'],$_GET['order']);
        }else {
            $this->orderBy('date_time','ASC');
        }  
        return $this->paginate($perPage);
    }
    
    public function getApiV2UsersCount()
    {
        $whereCond = [];
        //$whereCond = ['uuid_business_id' => $id];
        return $this->where($whereCond)->countAllResults();
    }

    public function getApiUserByUUID($uuid = false)
    {
        $fields = $this->getFieldNames('users');
        $unset = array('password','id','uuid');
        $fields = array_diff($fields,$unset);
        //print_r($fields);die;
        $this->select(implode(',',$fields).",uuid as id,id as ci4_internal_id,
        CASE WHEN status = 1 THEN 'true' ELSE 'false' END AS status
        ");
        $whereCond = $this->whereCond;
        if ($uuid === false) {
            $whereCond = array_merge(['role!=' => 1], $whereCond);
            return $this->where($whereCond)->findAll();
        } else {
            $whereCond = array_merge(['uuid' => $uuid], $whereCond);
            return $this->getWhere(array_filter($whereCond));
        }
    }

    public function isRootUserExists()
    {
        return $this->db->query(
            "SELECT roles.role_name, roles.uuid, count(users.id) AS userCount 
            FROM roles INNER JOIN users ON users.role = roles.uuid 
            WHERE roles.role_name = 'Administrator' GROUP BY users.id, roles.uuid, roles.role_name LIMIT 1"
        )->getRowArray();
    }

    public function countUsers()
    {
        //$whereCond = $whereCond = array_merge(['role' => 1], $this->whereCond);
        return $this->db->table($this->table)->select('id')->where(['role' => 1])->countAllResults();
    }

    public function countEmail($email = '')
    {
        $whereCond = $whereCond = array_merge(['email' => $email], $this->whereCond);
        return $this->db->table($this->table)->select('id')->where($whereCond)->countAllResults();
    }

    public function saveUser($data)
    {
        $this->db->table($this->table)->insert($data);
        return $this->db->insertID();
    }

    public function deleteUser($id)
    {
        $query = $this->db->table($this->table)->delete(array('id' => $id));
        return $query;
    }

    public function deleteAPIUser($id)
    {
        $query = $this->db->table($this->table)->delete(array('uuid' => $id));
        return $query;
    }

    public function updateUser($data, $id)
    {
        $query = $this->db->table($this->table)->update($data, array('id' => $id));
        return $query;
    }

    public function findUserByEmailAddress(string $emailAddress)
    {
        $user = $this
            ->asArray()
            ->where(['email' => $emailAddress])
            ->first();

        if (!$user)
            throw new Exception('User does not exist for specified email address');

        return $user;
    }
}
