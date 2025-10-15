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
			try {
				// Check if view exists, if not fall back to services table
				$viewExists = $this->db->query("SHOW FULL TABLES WHERE TABLE_TYPE LIKE 'VIEW' AND Tables_in_" . $this->db->database . " = 'view_services_with_secrets'")->getResultArray();

				if (empty($viewExists)) {
					log_message('warning', 'Service_model::getRowsWithService - view_services_with_secrets does not exist, falling back to services table');
					// Fallback to services table
					$builder = $this->db->table('services');
					$builder->join('categories', 'services.cid = categories.id', 'LEFT');
					$builder->join('tenants', 'services.tid = tenants.id', 'LEFT');
					$builder->select('categories.name as category');
					$builder->select('tenants.name as tenant');
					$builder->select('services.*');
					$builder->where(['services.uuid_business_id' => $this->businessUuid]);
				} else {
					// Use view_services_with_secrets for enhanced data
					$builder = $this->db->table('view_services_with_secrets');
					$builder->join('categories', 'view_services_with_secrets.cid = categories.id', 'LEFT');
					$builder->join('tenants', 'view_services_with_secrets.tid = tenants.id', 'LEFT');
					$builder->select('categories.name as category');
					$builder->select('tenants.name as tenant');
					$builder->select('view_services_with_secrets.*');
					$builder->where(['view_services_with_secrets.uuid_business_id' => $this->businessUuid]);
				}

				$query = $builder->get();

				if ($query === false || $query === null) {
					$error = $this->db->error();
					log_message('error', 'Service_model::getRowsWithService - Query failed. Error: ' . json_encode($error));
					return [];
				}

				return $query->getResultArray();

			} catch (\Exception $e) {
				log_message('error', 'Service_model::getRowsWithService - Exception: ' . $e->getMessage());
				return [];
			}
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
			try {
				// Check if view exists, if not fall back to services table
				$viewExists = $this->db->query("SHOW FULL TABLES WHERE TABLE_TYPE LIKE 'VIEW' AND Tables_in_" . $this->db->database . " = 'view_services_with_secrets'")->getResultArray();

				if (empty($viewExists)) {
					log_message('warning', 'Service_model::getApiRows - view_services_with_secrets does not exist, falling back to services table');
					// Fallback to services table
					$builder = $this->db->table('services');
					$builder->join('categories', 'services.cid = categories.id', 'LEFT');
					$builder->join('tenants', 'services.tid = tenants.id', 'LEFT');
					$builder->select('categories.name as category');
					$builder->select('tenants.name as tenant');
					$builder->select('services.*');
					$builder->where('services.uuid_business_id', $this->businessUuid);
					$builder->where('services.status', 1);
				} else {
					// Use view_services_with_secrets for enhanced API data
					$builder = $this->db->table('view_services_with_secrets');
					$builder->join('categories', 'view_services_with_secrets.cid = categories.id', 'LEFT');
					$builder->join('tenants', 'view_services_with_secrets.tid = tenants.id', 'LEFT');
					$builder->select('categories.name as category');
					$builder->select('tenants.name as tenant');
					$builder->select('view_services_with_secrets.*');
					$builder->where('view_services_with_secrets.uuid_business_id', $this->businessUuid);
					$builder->where('view_services_with_secrets.status', 1);
				}

				$query = $builder->get();

				if ($query === false || $query === null) {
					$error = $this->db->error();
					log_message('error', 'Service_model::getApiRows - Query failed. Error: ' . json_encode($error));
					return [];
				}

				return $query->getResultArray();

			} catch (\Exception $e) {
				log_message('error', 'Service_model::getApiRows - Exception: ' . $e->getMessage());
				return [];
			}
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