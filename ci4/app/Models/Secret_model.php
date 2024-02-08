<?php namespace App\Models;
use CodeIgniter\Model;
 
class Secret_model extends Model
{
    protected $table = 'secrets';
	protected $table2 = 'secrets_services';
    protected $businessUuid;

    public function __construct()
    {
        parent::__construct();
        $this->businessUuid = session('uuid_business');
    }
     
    public function getRows($id = false)
    {
        if($id === false){
            return $this->where('uuid_business_id', $this->businessUuid)->findAll();
        }else{
            return $this->getWhere(['id' => $id, 'uuid_business_id' => $this->businessUuid]);
        }   
    }
    public function getRowsByUUID($uuid = false)
    {
        if($uuid === false){
            return $this->where('uuid_business_id', $this->businessUuid)->findAll();
        }else{
            return $this->getWhere(['uuid' => $uuid, 'uuid_business_id' => $this->businessUuid]);
        }   
    }
	
	public function saveData($data)
    {
        $query = $this->db->table($this->table)->insert($data);
        return $query;
    }

    public function saveSecretRelatedData($data)
    {
        $query = $this->db->table($this->table2)->insert($data);
        return $query;
    }

	// public function saveDefaultData($data)
    // {
    //     $query = $this->db->table($this->table2)->insert($data);
    //     return $query;
    // }
	
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
		
	public function getSecret($code=""){
		return !empty($this->select('key_value')->getWhere(['key_name' => $code])->getRow())?$this->select('key_value')->getWhere(['key_name' => $code])->getRow()->text:'';
	}

	public function getLastInserted() {
		return $this->db->insertID();
	}
	
	public function serviceData($data)
    {
        $query = $this->db->table($this->table2)->insert($data);
        return $query;
    }
	
	public function getServicesFromSecret($id)
    {        
        return $this->db->table($this->table)->where(['service_id' => $id])->get()->getResult('array');
    }
		
	public function getServices($id)
    {        
        return $this->db->table($this->table2)
        ->join("secrets", "secrets_services.secret_id=secrets.id")
        ->join("services", "secrets_services.service_id=services.uuid")
        ->where(['secrets.id' => $id])->get()->getResult('array');
    }
	
	public function getSecrets($id)
    { 
		$this->join('secrets_services', 'secrets.id=secrets_services.secret_id', 'LEFT');			
		$this->select('secrets.*');		
			
		return $this->where(['service_id' => $id, 'secrets.uuid_business_id' => $this->businessUuid])->get()->getResult('array');
    }
	
	public function deleteService($id)
    {
        $query = $this->db->table($this->table2)->delete(array('secret_id' => $id));
        return $query;
    }
	
	public function deleteServiceFromServiceID($id)
    {
        $query = $this->db->table($this->table2)->delete(array('service_id' => $id));
        return $query;
    }
	
	public function getSecretsForDeployService($id)
    {
		$this->join('secrets_services', 'secrets_services.service_id=secrets.service_id', 'LEFT');
		$this->join('secrets_default', 'secrets_default.id=secrets_services.secrets_default_id', 'LEFT');
		$this->groupBy('secrets_default.id');
		$this->orderBy('secrets_default.id');
		$this->select('secrets_services.*');
		$this->select('secrets_default.*');
		
		return $this->where(['secrets_services.service_id' => $id, $this->table . '.uuid_business_id' => $this->businessUuid])->get()->getResult('array');
    }

    public function saveOrUpdateData($service_id, $data){
        if(strlen(trim($data["key_name"])) == 0){
            return 0;
        }
     
        $builder = $this->db->table("secrets");
        $builder->where("secrets.uuid", $data["uuid"]);				
        $records = $builder->get()->getRowArray();
        // echo $service_id; print_r($records);die;
        if( !empty($records)) {
            $query = $this->db->table($this->table)->update($data, array('id' => $records["id"]));
            return $records["id"];
        }else{
            $query = $this->db->table($this->table)->insert($data);
            return $this->db->insertID();
        }
    }

    public function getAllSecrets(){
        $table = $this->table;
        $this->select($table . '.*,services.name');
        $this->join('secrets_services', $table.'.id=secrets_services.secret_id', 'LEFT');			
        $this->join('services', 'services.uuid=secrets_services.service_id', 'LEFT');						
        $this->where($table . '.uuid_business_id', $this->businessUuid);
        $records = $this->get()->getResultArray();		
        return $records;
    }

    public function getTemplatesById($sid = false) {
        if ($sid === false) {
            return [];
        }
        $serviceTemplates = $this->db->table('templates__services')->where(['service_id' => $sid])->get()->getRowArray();
        return $serviceTemplates;
    }
}