<?php namespace App\Models;
use CodeIgniter\Model;
 
class Tenant_service_model extends Model
{
    protected $table = 'tenants_services';
    private $whereCond = array();

    public function __construct()
    {
        parent::__construct();
        if ($this->db->fieldExists('uuid_business_id', $this->table)) {

            $this->whereCond['uuid_business_id'] = session('uuid_business');
        }
    }
     
    public function getRows($id = false, $st =1)
    {
        $whereCond = $this->whereCond;
        if($st === 1){

            $whereCond = array_merge(['tid'=>$id], $whereCond);
            return $this->select('sid')->where($whereCond)->findAll();
        }else{

            $whereCond = array_merge(['id' => $id], $whereCond);
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
        $query = $this->db->table($this->table)->delete(array('tid' => $id));
        return $query;
    }
	
	public function updateData($data = null, $id = null)
	{
		$query = $this->db->table($this->table)->update($data, array('id' => $id));
		return $query;
	}
}