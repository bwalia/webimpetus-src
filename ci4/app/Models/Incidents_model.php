<?php

namespace App\Models;
use CodeIgniter\Model;

class Incidents_model extends Model
{
    protected $table = 'incidents';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid', 'uuid_business_id', 'title', 'description', 'incident_number',
        'priority', 'status', 'category', 'assigned_to', 'reporter_id', 'customer_id',
        'reported_date', 'due_date', 'resolved_date', 'resolution_notes',
        'impact', 'urgency', 'related_kb_id', 'attachments', 'tags',
        'created_at', 'updated_at'
    ];

    public function getRows($id = false)
    {
        if($id === false){
            return $this->where('uuid_business_id', session("uuid_business"))->findAll();
        }else{
            return $this->where(['id' => $id, 'uuid_business_id' => session("uuid_business")])->first();
        }
    }

	public function getIncidents()
    {
        $builder = $this->db->table($this->table . " as i");
        $builder->select("i.*, u1.name as assigned_name, u2.name as reporter_name");
        $builder->join('users u1', 'u1.id = i.assigned_to', 'left');
        $builder->join('users u2', 'u2.id = i.reporter_id', 'left');
        $builder->where('i.uuid_business_id', session("uuid_business"));
        $builder->orderBy('i.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function getRowsByUUID($uuid = false)
    {
        $whereCond = ['uuid_business_id' => session("uuid_business")];

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

	public function deleteData($id)
    {
        $query = $this->db->table($this->table)->delete(array('id' => $id));
        return $query;
    }

    public function deleteDataByUUID($uuid)
    {
        $query = $this->db->table($this->table)->delete(array('uuid' => $uuid));
        return $query;
    }

	public function updateData($id = null, $data = null)
	{
		$query = $this->db->table($this->table)->update($data, array('id' => $id));
		return $query;
	}

	public function insertOrUpdateByUUID($uuid = null, $data = null)
	{
        unset($data["id"]);

        if(@$uuid){
            $query = $this->db->table($this->table)->update($data, array('uuid' => $uuid));
            return $uuid;
        }else{
            $query = $this->db->table($this->table)->insert($data);
            return $data['uuid'];
        }
	}

    public function getApiIncidents($id=false)
    {
        $builder = $this->db->table($this->table . " as i");
        $builder->select("i.*, u1.name as assigned_name, u2.name as reporter_name");
        $builder->join('users u1', 'u1.id = i.assigned_to', 'left');
        $builder->join('users u2', 'u2.id = i.reporter_id', 'left');
        if($id!=false) $builder->where('i.uuid_business_id', $id);
        $builder->orderBy('i.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }
}
