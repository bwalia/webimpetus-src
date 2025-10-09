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
			// Use view_services_with_secrets for enhanced data
			$builder = $this->db->table('view_services_with_secrets');
			$builder->join('categories', 'view_services_with_secrets.cid = categories.id', 'LEFT');
			$builder->join('tenants', 'view_services_with_secrets.tid = tenants.id', 'LEFT');
			$builder->select('categories.name as category');
			$builder->select('tenants.name as tenant');
			$builder->select('view_services_with_secrets.*');
			$builder->where(['view_services_with_secrets.uuid_business_id' => $this->businessUuid]);
            return $builder->get()->getResultArray();
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
			// Use view_services_with_secrets for enhanced API data
			$builder = $this->db->table('view_services_with_secrets');
			$builder->join('categories', 'view_services_with_secrets.cid = categories.id', 'LEFT');
			$builder->join('tenants', 'view_services_with_secrets.tid = tenants.id', 'LEFT');
			$builder->select('categories.name as category');
			$builder->select('tenants.name as tenant');
			$builder->select('view_services_with_secrets.*');
            $builder->where('view_services_with_secrets.uuid_business_id', $this->businessUuid);
            $builder->where('view_services_with_secrets.status', 1);
            return $builder->get()->getResultArray();
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

    public function getServciesRows($limit, $offset, $order, $dir, $query, $uuidBusineess)
    {
			// Use view_services_with_secrets for enhanced data with secret counts and tags
			$builder = $this->db->table('view_services_with_secrets');
			$builder->join('categories', 'view_services_with_secrets.cid = categories.id', 'LEFT');
			$builder->join('tenants', 'view_services_with_secrets.tid = tenants.id', 'LEFT');
			$builder->select('categories.name as category');
			$builder->select('tenants.name as tenant');
			$builder->select('view_services_with_secrets.*');

            if ($query) {
                $builder->like('view_services_with_secrets.name', $query);
            }

			$builder->where(['view_services_with_secrets.uuid_business_id' => $uuidBusineess]);

            $count = $builder->countAllResults(false);
            $builder->limit($limit, $offset);
            $builder->orderBy($order, $dir);

            $record = $builder->get()->getResultArray();

            return [
                'data' => $record,
                'total' => $count
            ];
    }
}