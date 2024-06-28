<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;
class Companies extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'companies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'is_email_sent'
    ];

    // Dates
    protected $useTimestamps        = true;
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';


    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    private $whereCond = array();

    public function __construct()
    {
        parent::__construct();
        if ($this->db->fieldExists('uuid_business_id', $this->table)) {

            $this->whereCond['uuid_business_id'] = session('uuid_business');
        }
    }

    public function getRows($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['id' => $id]);
        }   
    }

    public function getRowsByUUID($uuid = false)
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

    public function getCompanies($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->where(['uuid_business_id' => $id])->get()->getResultArray();
        }   
    }	

	public function deleteData($id)
    {
        $query = $this->db->table($this->table)->delete(array('id' => $id));
        return $query;
    }
	
	public function insertOrUpdate($id = null, $data = null)
	{

        unset($data["id"]);

        if(@$id){
            $query = $this->db->table($this->table)->update($data, array('id' => $id));
            return $id;
           
        }else{
            $query = $this->db->table($this->table)->insert($data);
            return $this->db->insertID();
        }	
	}
	public function insertOrUpdateByUUID($uuid = null, $data = null)
	{

        unset($data["id"]);

        if(@$uuid){
            $query = $this->db->table($this->table)->update($data, array('uuid' => $uuid));
            return $uuid;
           
        }else{
            $query = $this->db->table($this->table)->insert($data);
            return $this->db->insertID();
        }	
	}
    public function findCompanyByEmailAddress(string $emailAddress)
    {
        $contact = $this
            ->asArray()
            ->where(['email' => $emailAddress])
            ->first();

        if (!$contact) 
            throw new Exception('Company does not exist for specified email address');

        return $contact;
    }

    public function getContacts($companyUUID)
    {
        $query = [];
        if ($companyUUID) {
            $query = $this->db->table("company__contact")
                    ->select("contact_uuid")
                    ->join("contacts", "company__contact.contact_uuid = contacts.uuid")
                    ->select("contacts.first_name, contacts.surname, contacts.email, contacts.direct_phone, contacts.mobile")
                    ->where("company_uuid", $companyUUID)
                    ->get()
                    ->getResultArray();
        }
        return $query;
    }

    public function deleteRelationData($companyUUID)
    {
        $query = $this->db->table("company__contact")->delete(array('company_uuid' => $companyUUID));
        return $query;
    }
    public function deleteRelationDataByContact($contactUUID)
    {
        $query = $this->db->table("company__contact")->delete(array('contact_uuid' => $contactUUID));
        return $query;
    }
    public function deleteRelationDataByContactCompany($contactUUID, $companyUUID)
    {
        $query = $this->db->table("company__contact")->delete(array('contact_uuid' => $contactUUID, 'company_uuid' => $companyUUID));
        return $query;
    }

    public function insertRelationData(array $data)
    {
        $query = $this->db->table("company__contact")->insert($data);
        return $this->db->insertID();
    }

    public function deleteCategoriesRelation($companyId) {
        $query = $this->db->table("companies__categories")->delete(array('company_id' => $companyId));
        return $query;
    }

    public function insertCategoryData(array $data)
    {
        $this->db->table("companies__categories")->insert($data);
        return $this->db->insertID();
    }

    public function selectedCategories($companyUUID)
    {
        $query = [];
        if ($companyUUID) {
            $query = $this->db->table("companies__categories")
                    ->where("company_id", $companyUUID)
                    ->get()
                    ->getResultArray();
        }
        return $query;
    }

}
