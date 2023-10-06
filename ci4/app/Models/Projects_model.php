<?php 

namespace App\Models;
use CodeIgniter\Model;
 
class Projects_model extends Model
{
    protected $table = 'projects';
    public $businessUuid;
     
    public function __construct()
    {
        parent::__construct();
		
        $this->businessUuid = session('uuid_business');
    }
    public function getRows($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['id' => $id]);
        }   
    }
	
	public function getProjectList()
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table.".*, customers.company_name");
        $builder->join('customers', 'customers.id = '.$this->table.'.customers_id', 'left');
        $builder->where($this->table.".uuid_business_id",  $this->businessUuid);

        return $builder->get()->getResultArray();
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

    public function getBusinessProjectList($bid, $params)
    {
        $range = json_decode($params['range']);
        $sort = json_decode($params['sort']);
        $limit = (int) implode(', ', $range);
        list($column, $order) = $sort;
        
        $builder = $this->db->table($this->table);
        $builder->select([
            '*',             // Select all columns
            'id AS uuid',    // Rename 'id' to 'uuid'
            'uuid AS id',    // Rename 'uuid' to 'id'
        ]);
        $builder->orderBy($this->table .".$column", "$order");
        $builder->limit($limit);
        $builder->where("uuid_business_id", $bid);
        
        $total =  $this->db->table($this->table)->where("uuid_business_id", $bid)->countAllResults();
        return [
            'data' => $builder->get()->getResultArray(),
            'total' => $total
        ];
    }
}