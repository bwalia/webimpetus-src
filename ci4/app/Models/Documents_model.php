<?php 

namespace App\Models;
use CodeIgniter\Model;
 
class Documents_model extends Model
{
    protected $table = 'documents';
     
    public function __construct()
    {
        parent::__construct();
		
        $this->businessUuid = session('uuid_business');
    }

	public function getList()
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table.".*, customers.company_name");
        $builder->join('customers', 'customers.id = '.$this->table.'.client_id', 'left');
        $builder->where($this->table.".uuid_business_id",  $this->businessUuid);

        return $builder->get()->getResultArray();
    }

}