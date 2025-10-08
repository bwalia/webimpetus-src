<?php

namespace App\Models;
use CodeIgniter\Model;

class Knowledge_base_model extends Model
{
    protected $table = 'knowledge_base';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid', 'uuid_business_id', 'title', 'content', 'article_number',
        'category', 'keywords', 'status', 'author_id', 'visibility',
        'helpful_count', 'view_count', 'related_incidents', 'attachments',
        'tags', 'published_date', 'created_at', 'updated_at'
    ];

    public function getRows($id = false)
    {
        if($id === false){
            return $this->where('uuid_business_id', session("uuid_business"))->findAll();
        }else{
            return $this->where(['id' => $id, 'uuid_business_id' => session("uuid_business")])->first();
        }
    }

	public function getKnowledgeBase()
    {
        $builder = $this->db->table($this->table . " as kb");
        $builder->select("kb.*, u.name as author_name");
        $builder->join('users u', 'u.id = kb.author_id', 'left');
        $builder->where('kb.uuid_business_id', session("uuid_business"));
        $builder->orderBy('kb.created_at', 'DESC');
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

    public function getApiKnowledgeBase($id=false)
    {
        $builder = $this->db->table($this->table . " as kb");
        $builder->select("kb.*, u.name as author_name");
        $builder->join('users u', 'u.id = kb.author_id', 'left');
        if($id!=false) $builder->where('kb.uuid_business_id', $id);
        $builder->orderBy('kb.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function searchKnowledgeBase($query)
    {
        $builder = $this->db->table($this->table . " as kb");
        $builder->select("kb.*, u.name as author_name");
        $builder->join('users u', 'u.id = kb.author_id', 'left');
        $builder->where('kb.uuid_business_id', session("uuid_business"));
        $builder->where('kb.status', 'published');
        $builder->groupStart();
            $builder->like('kb.title', $query);
            $builder->orLike('kb.content', $query);
            $builder->orLike('kb.keywords', $query);
        $builder->groupEnd();
        $builder->orderBy('kb.view_count', 'DESC');
        return $builder->get()->getResultArray();
    }
}
