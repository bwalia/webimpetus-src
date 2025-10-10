<?php

namespace App\Models;

use CodeIgniter\Model;

class Domain_model extends Model
{
    protected $table = 'domains';
    private $whereCond = array();

    public function __construct()
    {
        parent::__construct();

        $this->whereCond[$this->table . '.uuid_business_id'] = session('uuid_business');
    }

    public function getRows($id = false, $withRelations = false)
    {
        $whereCond = $this->whereCond;
        if ($id === false) {
            $this->select('domains.*');

            // Only join if explicitly requested (avoid N+1 but also avoid unnecessary joins)
            if ($withRelations) {
                $this->join('service__domains', 'domains.uuid = service__domains.domain_uuid', 'LEFT');
                $this->select('services.name as sname');
                $this->join('services', 'service__domains.service_uuid = services.uuid', 'LEFT');
            }

            $this->where($whereCond);
            $this->orderBy('domains.id', 'DESC');
            return $this->findAll();
        } else {
            $whereCond = array_merge(['uuid' => $id], $whereCond);
            return $this->getWhere($whereCond);
        }
    }
    public function getFilteredRows($query = false, $limit = 10, $offset = 0, $order = "name", $dir = "ASC", $count = false)
    {
        $data = [];
        $whereCond = $this->whereCond;

        // Select only needed columns to reduce data transfer
        $this->select('domains.id, domains.uuid, domains.name, domains.customer_uuid, domains.domain_path, domains.domain_path_type, domains.domain_service_name, domains.domain_service_port, domains.notes');

        // Join with service__domains and services for service name
        $this->join('service__domains', 'domains.uuid = service__domains.domain_uuid', 'LEFT');
        $this->select('services.name as sname');
        $this->join('services', 'service__domains.service_uuid = services.uuid', 'LEFT');

        $this->where($whereCond);

        if ($query) {
            $this->like("domains.name", $query);
        }

        // Use cloned builder for count to avoid query conflicts
        $countBuilder = clone $this->builder();
        $total = $countBuilder->countAllResults(false);

        // Optimize ORDER BY - use indexed column when possible
        if ($order === 'name') {
            $this->orderBy('domains.name', $dir);
        } elseif ($order === 'id') {
            $this->orderBy('domains.id', $dir);
        } else {
            $this->orderBy('domains.' . $order, $dir);
        }

        $this->limit($limit, $offset);
        $results = $this->get()->getResultArray();

        $data['count'] = $total;
        $data['data'] = $results;
        return $data;
    }

    public function saveData($data)
    {
        $query = $this->db->table($this->table)->insert($data);
        return $query;
    }

    public function deleteData($id)
    {
        $query = $this->db->table($this->table)->delete(array('uuid' => $id));
        return $query;
    }

    public function updateData($id = null, $data = null)
    {
        $query = $this->db->table($this->table)->update($data, array('uuid' => $id));
        return $query;
    }
}
