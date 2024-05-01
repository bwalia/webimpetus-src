<?php namespace App\Models;
use CodeIgniter\Model;
 
class Customers_model extends Model
{
    protected $table = 'customers';


    public function search($keyword)
    {
        if (!empty($keyword)) {
            return $this->like('company_name', $keyword);
        }
        return $this;
    }

    public function getRows($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['id' => $id]);
        }   
    }
    public function getBusinessRows($uuid = false)
    {
        if($uuid === false){
            return $this->getWhere(['uuid_business_id' => session('uuid_business')]);
        }else{
            return $this->getWhere(['uuid' => $uuid]);
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