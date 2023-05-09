<?php namespace App\Models;
use CodeIgniter\Model;
 
class Domain_model extends Model
{
    protected $table = 'domains';
    private $whereCond = array();

    public function __construct()
    {
        parent::__construct();

        $this->whereCond[$this->table.'.uuid_business_id'] = session('uuid_business');
    }

    public function getRows($id = false)
    {
        $whereCond = $this->whereCond;
        if($id === false){
			$this->join('services', 'domains.sid = services.id', 'LEFT');
			$this->select('services.name as sname');
			$this->select('domains.*');
            $this->where($whereCond);
            return $this->findAll();
        }else{
            $whereCond = array_merge(['uuid' => $id], $whereCond);
            return $this->getWhere($whereCond);
        }   
    }
	
	public function saveData($data)
    {
        $query = $this->db->table($this->table)->insert($data);
        return $query;
    }
	
	public function deleteData($id)
    {
        $query = $this->db->table($this->table)->delete(array('uuid' => $id));
        return $query;
    }
	
	public function updateData($id = null, $data = null)
	{
		$query = $this->db->table($this->table)->update($data, array('uuid' => $id));
		return $query;
	}
}