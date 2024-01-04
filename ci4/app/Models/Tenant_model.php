<?php namespace App\Models;
use CodeIgniter\Model;
 
class Tenant_model extends Model
{
    protected $table = 'tenants';
    private $whereCond = array();

    public function __construct()
    {
        parent::__construct();
        if ($this->db->fieldExists('uuid_business_id', $this->table)) {

            $this->whereCond[$this->table.'.uuid_business_id'] = session('uuid_business');
        }
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
    public function getRowsByUUId($uuid = false)
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
	
	public function getJoins()
    {
        $whereCond = $this->whereCond;
        $this->join('tenants_services', 'tenants_services.tid = tenants.id', 'LEFT');
        $this->join('services', 'tenants_services.sid = services.id', 'LEFT');
        $this->groupBy('tenants.id');
        $this->select('GROUP_CONCAT(services.name) as service_name');
        $this->select('tenants.*');
        if (!empty($whereCond)) {

            $this->where($whereCond);
        }

        return $this->findAll();         
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
	
	public function updateData($data = null, $id = null)
	{
		$query = $this->db->table($this->table)->update($data, array('id' => $id));
		return $query;
	}
	public function updateDataByUUID($data = null, $uuid = null)
	{
		$query = $this->db->table($this->table)->update($data, array('uuid' => $uuid));
		return $query;
	}
	
	public function getLastInserted() {
		return $this->db->insertID();
	}
}