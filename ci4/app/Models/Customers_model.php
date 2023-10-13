<?php namespace App\Models;
use CodeIgniter\Model;
 
class Customers_model extends Model
{
    protected $table = 'customers';

    public function getRows($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['id' => $id]);
        }   
    }

    public function getCustomers($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->where(['uuid_business_id' => $id])->get()->getResultArray();
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
	
	public function insertOrUpdate($id = null, $data = null)
	{

        unset($data["id"]);

        if(@$id){
            $query = $this->db->table($this->table)->update($data, array('id' => $id));
            return $id;
           
        }else{
            $query = $this->db->table($this->table)->insert($data);
            return $this->db->insertID();
        }	
		//return $query;
	}
	public function insertOrUpdateByUUID($uuid = null, $data = null)
	{

        unset($data["id"]);

        if(@$uuid){
            $query = $this->db->table($this->table)->update($data, array('uuid' => $uuid));
            return $uuid;
           
        }else{
            $query = $this->db->table($this->table)->insert($data);
            return $this->db->insertID();
        }	
		//return $query;
	}

    
}