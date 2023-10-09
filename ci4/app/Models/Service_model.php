<?php namespace App\Models;
use CodeIgniter\Model;
 
class Service_model extends Model
{
    protected $table = 'services';
    protected $businessUuid;
     
    public function __construct()
    {
        parent::__construct();
        $this->businessUuid = session('uuid_business');
    }

    public function getRowsWithService($id = false)
    {
        if($id === false){
			$this->join('categories', 'services.cid = categories.id', 'LEFT');
			$this->join('tenants', 'services.tid = tenants.id', 'LEFT');
			$this->select('categories.name as category');			
			$this->select('tenants.name as tenant');
			$this->select('services.*');
			$this->where([$this->table . '.uuid_business_id' => $this->businessUuid]);
            return $this->findAll();
        }else{
            return $this->getWhere(['uuid' => $id, 'uuid_business_id' => $this->businessUuid]);
        }   
    }
    public function getRows($id = false)
    {
        if($id === false){
			$this->where([$this->table . '.uuid_business_id' => $this->businessUuid]);
            return $this->findAll();
        }else{
            return $this->getWhere(['uuid' => $id, 'uuid_business_id' => $this->businessUuid]);
        }   
    }
	
	public function getApiRows($id = false)
    {
        if($id === false){
			$this->join('categories', 'services.cid = categories.id', 'LEFT');
			$this->join('tenants', 'services.tid = tenants.id', 'LEFT');
			$this->select('categories.name as category');			
			$this->select('tenants.name as tenant');
			$this->select('services.*');
            $this->where($this->table . '.uuid_business_id', $this->businessUuid);
            return $this->where('status', 1)->findAll();
        }else{
            return $this->getWhere(['id' => $id,'status'=>1, 'uuid_business_id' => $this->businessUuid]);
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
	
	public function updateData($id = null, $data = null)
	{
		$query = $this->db->table($this->table)->update($data, array('id' => $id));
		return $query;
	}
	
	public function getLastInserted() {
		return $this->db->insertID();
	}

    public function insertOrUpdate($table, $id = null, $data = null)
	{
        unset($data["id"]);
        $field = is_numeric($id)?'id':'uuid';
        if(@$id){
            $query = $this->db->table($table)->update($data, array($field => $id));
            if( $query){
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
            }
        }else{
            $query = $this->db->table($table)->insert($data);
            $id =  $this->db->insertID();
            if($query){
                session()->setFlashdata('message', 'Data updated Successfully!');
                session()->setFlashdata('alert-class', 'alert-success');
            }

        }
	
		return $id;
	}
}