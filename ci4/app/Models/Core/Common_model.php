<?php

namespace App\Models\Core;

use CodeIgniter\Model;

class Common_model extends Model
{
    protected $table = '';
    private $whereCond = array();
    private $doesUuidBusinessIdFieldExists = false;
    private $session = "";

    function __construct()
    {
        parent::__construct();
        $this->session = session();
        $this->table = $this->getTableNameFromUri();

        if ($this->db->tableExists($this->table)) {
            if ($this->db->fieldExists('uuid_business_id', $this->table)) {

                $this->whereCond['uuid_business_id'] = session('uuid_business');
                $this->doesUuidBusinessIdFieldExists = true;
            }
        }
    }


    public function getTableNameFromUri()
    {
        $uri = service('uri');
        $tableNameFromUri = $uri->getSegment(1);
        return $tableNameFromUri;
    }


    public function getRows($id = false)
    {
        $whereCond = $this->whereCond;

        if ($id === false) {

            if (empty($whereCond)) {

                return $this->findAll();
            } else {

                return $this->getWhere($whereCond)->getResultArray();
            }
        } else {

            $whereCond = array_merge(array('id' => $id), $whereCond);
            return $this->getWhere($whereCond);
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
    public function getExistsRowsByUUID($uuid = false)
    {
        $whereCond = $this->whereCond;

        if ($uuid) {
            $whereCond = array('uuid' => $uuid);
            return $this->getWhere($whereCond);
        }
    }

    public function getCats($id = false)
    {
        return $this->findAll();
    }


    public function deleteData($id)
    {
        $query = $this->db->table($this->table)->delete(array('id' => $id));
        return $query;
    }

    public function deleteDataByUUID($uuid)
    {
        $query = $this->db->table($this->table)->delete(array('uuid' => $uuid));
        return $query;
    }

    public function insertBusiness($data = null)
    {
        $query = $this->db->table('businesses')->insert($data);
    }

    public function insertOrUpdate($id = null, $data = null)
    {
        unset($data["id"]);
        if ($this->doesUuidBusinessIdFieldExists) {
            $data['uuid_business_id'] = session('uuid_business');
        }
        if (@$id) {
            $query = $this->db->table($this->table)->update($data, array('id' => $id));
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


    public function insertOrUpdateByUUID($uuid = null, $data = null)
    {
        if ($this->doesUuidBusinessIdFieldExists) {
            $data['uuid_business_id'] = session('uuid_business');
        }
        if (!empty($uuid)) {
            $query = $this->db->table($this->table)->update($data, array('uuid' => $uuid));
            if ($query) {
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
                return $uuid;
            }
        } else {
            $query = $this->db->table($this->table)->insert($data);
            if ($query) {
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
                return $data['uuid'];
            }
        }
        return false;
    }

    public function getAllDataFromTable($tableName)
    {
        $query = $this->db->table($tableName)->get()->getResultArray();
        return $query;
    }

    public function getUser($id = false)
    {
        $whereCond = $this->whereCond;
        $builder = $this->db->table("users");
        if ($id === false) {
            $whereCond = array_merge(['role!=' => 1], $whereCond);
            return $builder->where($whereCond)->get()->getResultArray();
        } else {
            $whereCond = array_merge(['id' => $id], $whereCond);
            return $builder->getWhere($whereCond)->getRowArray();
        }
    }

    public function updateColumn($tableName, $id = null, $data = null)
    {
        $query = $this->db->table($tableName, $this->table)->update($data, array('id' => $id));
        return $query;
    }

    public function updateData($id = null, $data = null)
    {
        $query = $this->db->table($this->table)->update($data, array('id' => $id));
        return $query;
    }
    public function updateDataByUUID($uuid = null, $data = null)
    {
        $query = $this->db->table($this->table)->update($data, array('uuid' => $uuid));
        return $query;
    }
    public function updateTableData($id = null, $data = null, $tableName = "")
    {
        $query = $this->db->table($tableName)->update($data, array('id' => $id));
        return $query;
    }

    public function updateTableDataByUUID($uuid = null, $data = null, $tableName = "")
    {
        $query = $this->db->table($tableName)->update($data, array('uuid' => $uuid));
        return $query;
    }

    public function insertTableData($data = null, $tableName = "")
    {
        $query = $this->db->table($tableName)->insert($data);
        return $this->db->insertID();
    }
    public function saveDataInTable($data, $tableName = "")
    {
        $query = $this->db->table($tableName)->insert($data);
        return $query;
    }
    public function getDataWhere($tableName, $value, $field = "id")
    {
        $result = $this->db->table($tableName)->getWhere([
            $field => $value
        ])->getResultArray();

        return $result;
    }
    public function getRow($tableName, $value, $field = "id", $arrType = false)
    {
        $fields = $this->getFieldNames($tableName);
        //print_r($fields);die;
        if(in_array('uuid',$fields)){
            $arr = "*,uuid as id";
        }else{
            $arr = "*";
        }
        $result = $this->db->table($tableName)->select($arr)->getWhere([
            $field => $value
        ]);

        return $arrType==false?$result->getRow():$result->getRowArray();
    }

    public function deleteTableData($tableName, $id, $field = "id")
    {
        $query = $this->db->table($tableName)->delete(array($field => $id));
        return $query;
    }
    
    public function unlinkData($tableName, $id, $field, $data) {
        if ($tableName === "secrets_services" || $tableName === "service__domains") {
            $query = $this->db->table($tableName)->delete(array($field => $id));
            return $query;
        }
        $query = $this->db->table($tableName)->update($data, array($field => $id));
        return $query;
    }

    public function getMenuCode($value)
    {
        $result = $this->db->table("menu")->getWhere([
            "link" => $value
        ])->getRowArray();

        $maxOrder = findMaxFieldValue("menu", "sort_order");

        if (!empty($result['id'])) {

            $this->updateTableData($result['id'], ["sort_order" => $result['sort_order'] + 1], "menu");
        }

        return @$result['id'];
    }

    public function loadBillToData($clientId)
    {
        $customersData = $this->db->table('customers')->getWhere(array('id' => $clientId))->getRowArray();
        if (!empty($customersData)) {

            $billData = array();
            $billData[] = trim($customersData['address1']);
            $billData[] = trim($customersData['address2']);
            $billData[] = implode(', ', array_filter(array(
                trim($customersData['postal_code']),
                trim($customersData['city']),
            ), 'strlen'));
            $billData[] = trim($customersData['country']);
            $billData = array_filter($billData, 'strlen');
            $billDataStr = implode("\n", $billData);

            return array(
                'status' => 1,
                'value' => $billDataStr,
            );
        }

        return array(
            'status' => 1,
            'value' => '',
        );
    }

    public function calculateDueDate($term, $currentDate)
    {
        if (!empty($term)) {

            $dayToBeDued = (int) filter_var($term, FILTER_SANITIZE_NUMBER_INT);
            if (!empty($dayToBeDued)) {

                $dueDateStamp = strtotime("+" . $dayToBeDued . " day", strtotime($currentDate));
                $value = date('m/d/Y', $dueDateStamp);

                return array(
                    'status' => 1,
                    'value' => $value,
                );
            }
        }

        return array(
            'status' => 1,
            'value' => '',
        );
    }

    public function getCategories($id = false)
    {
        $whereCond = $this->whereCond;
        $builder = $this->db->table("categories");
        if ($id === false) {
            //$whereCond = array_merge(['role!='=>1], $whereCond);
            return $builder->get()->getResultArray();
        } else {
            $whereCond = array_merge(['id' => $id], $whereCond);
            return $builder->getWhere($whereCond)->getRowArray();
        }
    }

    public function CommonInsertOrUpdate($table, $id = null, $data = null)
    {
        unset($data["id"]);

        $field = is_numeric($id)?'id':'uuid';

        if (@$id!='' && @$id!=null) {
            $builder = $this->db->table($table);
            $builder->where($field, $id);
            $result = $builder->update($data);
            return $id;
        } else {
            $builder = $this->db->table($table);
            $result = $builder->insert($data);
            return $this->db->insertID();
        }
        return false;
    }

    public function CommonfindMaxFieldValue($tableName, $field)
    {

        $db = \Config\Database::connect();
        $builder = $db->table($tableName);
        $query = $builder->selectMax($field);
        $order_number = $query->get()->getRowArray()[$field];

        return $order_number;
    }

    public function getCommonData($tableName,$where = [])
    {
        if(!empty($where)){
            return $this->db->table($tableName)->where($where)->get()->getResultArray();
        }else {
            $query = $this->db->table($tableName)->get()->getResultArray();
            return $query;
        }
    }

    public function getApiData($tableName,$where = [],$concat = false)
    {
        $_GET['perPage'] = !empty($_GET['perPage'])?$_GET['perPage']:0;
        $offset = !empty($_GET['perPage']) && !empty($_GET['page'])?($_GET['page']-1)*$_GET['perPage']:0;
        $fields = $this->getFieldNames($tableName);
        //print_r($fields);die;
        if(in_array('uuid',$fields) && $tableName!=='categories'){
            $arr = "*,uuid as id,";
        }else{
            $arr = "*,";
        }
        $builder = $this->db->table($tableName);
        if($concat!==false){
            $arr = $arr.$concat;
        }
        $builder->select($arr);
        if(!empty($where)){
            $builder->where($where);
        }
        if(!empty($_GET['field']) && !empty($_GET['order'])){
            $builder->orderBy($tableName .'.'.$_GET['field'],$_GET['order']);
        }  
        return $builder->get($_GET['perPage'],$offset)->getResultArray();
           
    }

    public function getCount($tableName,$where = []){
        return $count = $this->db->table($tableName)->getWhere($where)->getNumRows();
    }

    public function getAdminBusiness(){
        return $this->db->table('businesses')
            ->orderBy('id', 'asc')
            ->limit(1)
            ->get()
            ->getRowArray();
    }
}
