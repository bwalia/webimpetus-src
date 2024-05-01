<?php namespace App\Models;
use CodeIgniter\Model;
 
class Cat_model extends Model
{
    protected $table = 'categories';
	protected $table2 = 'content_category';
    protected $businessUuid;
	private $whereCond = array();

    public function __construct()
    {
        parent::__construct();
        $this->businessUuid = session('uuid_business');
		$this->whereCond['uuid_business_id'] = $this->businessUuid;
    }

    public function search($keyword)
    {
        if (!empty($keyword)) {
            return $this->like('name', $keyword);
        }
        return $this;
    }
    
    public function getRows($id = false)
    {
        if($id === false){
            return $this->where($this->whereCond)->findAll();
        }else{
            $whereCond = array_merge(['id' => $id], $this->whereCond);
            return $this->getWhere($whereCond);
        }   
    }
	
	public function getCats($id = false)
    {
		return $this->where($this->whereCond)->findAll();
	}
	
	public function deleteCatData($id)
    {
        $query = $this->db->table($this->table2)->delete(array('contentid' => $id));
        return $query;
    }
	
	public function getCatIds($id)
    {        
        $whereCond = array_merge(['contentid' => $id], $this->whereCond);
        return $this->db->table($this->table2)->where($whereCond)->get()->getResult('array');
    }
	
	public function saveData2($data)
    {
        $query = $this->db->table($this->table2)->insert($data);
        return $query;
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

    public function getRowByContactId($contactId, $bId)
    {
        if($contactId === false){
            return [];
        }else{
            $whereCond = array('contact_uuid' => $contactId, 'uuid_business_id' => $bId);
            return $this->getWhere($whereCond);
        }   
    }
    public function getContentByCat($catId, $bId)
    {
        if($catId === false){
            return [];
        }else{
            $whereCond = array('categoryid' => $catId, 'uuid_business_id' => $bId);
            return $this->db->table($this->table2)->getWhere($whereCond);
        }   
    }
}