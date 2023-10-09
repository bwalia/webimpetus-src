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
			$this->select('domains.*');
            $this->join('service__domains', 'domains.uuid = service__domains.domain_uuid', 'LEFT');
			$this->select('services.name as sname');
			$this->join('services', 'service__domains.service_uuid = services.uuid', 'LEFT');
            $this->where($whereCond);
            $this->groupBy('domains.uuid');
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