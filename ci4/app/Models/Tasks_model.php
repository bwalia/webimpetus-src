<?php

namespace App\Models;

use CodeIgniter\Model;

class Tasks_model extends Model
{
    protected $table = 'tasks';

    protected $businessUuid;
    private $whereCond = array();

    public function __construct()
    {
        parent::__construct();

        $this->businessUuid = session('uuid_business');
        $this->whereCond['uuid_business_id'] = $this->businessUuid;
    }
    public function getRows($id = false)
    {
        if ($id === false) {
            return $this->findAll();
        } else {
            return $this->getWhere(['id' => $id]);
        }
    }

    public function getTaskList($whereConditions = null, $page = 1, $perPage = 10, $keyword = '')
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*, customers.company_name, projects.name as project_name");
        $builder->join('customers', 'customers.id = ' . $this->table . '.reported_by', 'left');
        $builder->join('projects', 'projects.id = ' . $this->table . '.projects_id', 'left');
        $builder->where($this->table . ".uuid_business_id",  $this->businessUuid);
        if (!empty($whereConditions)) {
            $builder->where($whereConditions);
        }
        if ($keyword && $keyword !== '') {
            $builder->like($this->table. '.name', $keyword);
        } else {
            $currentPage = $page == 1 ? 0 : $page - 1;
            $offset = $currentPage * $perPage;
            $builder->limit($perPage);
            $builder->offset($offset);
        }
        

        $countBuilder = $this->db->table($this->table);
        $countBuilder->select($this->table . ".*, customers.company_name, projects.name as project_name");
        $countBuilder->join('customers', 'customers.id = ' . $this->table . '.reported_by', 'left');
        $countBuilder->join('projects', 'projects.id = ' . $this->table . '.projects_id', 'left');
        $countBuilder->where($this->table . ".uuid_business_id",  $this->businessUuid);
        if (!empty($whereConditions)) {
            $countBuilder->where($whereConditions);
        }
        $count = $countBuilder->countAllResults();
        return [
            'data' => $builder->get()->getResultArray(),
            'count' => $count
        ];
    }

    public function updateData($id = null, $data = null)
    {
        $query = $this->db->table($this->table)->update($data, array('id' => $id));
        return $query;
    }

    public function progress()
    {
        //$db = db_connect();
        $sql = 'SELECT t.projects_id,IFNULL(t.total,0) AS total,IFNULL(c.done,0) AS completed
        FROM (SELECT projects_id,COUNT(id) AS total FROM tasks GROUP BY projects_id) AS t LEFT JOIN (SELECT projects_id,COUNT(id) AS done FROM tasks WHERE status = "completed"  GROUP BY projects_id) AS c 
        ON t.projects_id = c.projects_id
        ORDER BY t.projects_id';

        $query = $this->db->query($sql);
        
        $result = [];

        foreach ($query->getResult() as $row) {
           $result[$row->projects_id] = ($row->completed/$row->total)*100;
        }

        return $result;
    }

    public function allTaskStatus()
    {
        $sql = "SELECT DISTINCT(status) AS status FROM tasks";
        $query = $this->db->query($sql);
        
        $result = [];

        foreach ($query->getResult() as $row) {
            array_push($result,array("key"=>$row->status,"value"=>$row->status));
        }
        return $result;
    }

    public function getApiTaskList($businessUuid =false, $whereConditions = null)
    {
        $_GET['perPage'] = !empty($_GET['perPage'])?$_GET['perPage']:0;
        $offset = !empty($_GET['perPage']) && !empty($_GET['page'])?($_GET['page']-1)*$_GET['perPage']:0;
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*,".$this->table.".uuid as id, ".$this->table.".id as internal_id, customers.company_name, projects.name as project_name");
        $builder->join('customers', 'customers.id = ' . $this->table . '.reported_by', 'left');
        $builder->join('projects', 'projects.id = ' . $this->table . '.projects_id', 'left');
        if($businessUuid !==false) $builder->where($this->table . ".uuid_business_id",  $businessUuid);
        if (!empty($whereConditions)) {
            $builder->where($whereConditions);
        }
        // echo $this->db->getLastQuery();
        if(!empty($_GET['field']) && !empty($_GET['order'])){
            $builder->orderBy($this->table .'.'.$_GET['field'],$_GET['order']);
        }else {
            $builder->orderBy($this->table . ".created_at","ASC");
        }  
        return $builder->get($_GET['perPage'],$offset)->getResultArray();
    }

    public function getTasksCount($businessUuid =false,$whereConditions = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".id");
        $builder->join('customers', 'customers.id = ' . $this->table . '.reported_by', 'left');
        $builder->join('projects', 'projects.id = ' . $this->table . '.projects_id', 'left');
        if($businessUuid !==false) $builder->where($this->table . ".uuid_business_id",  $businessUuid);
        if (!empty($whereConditions)) {
            $builder->where($whereConditions);
        }

        // echo $this->db->getLastQuery();

        return $builder->countAllResults();
    }

    public function getTaskByUUID($id = false)
    {
        $this->select($this->table . ".*,".$this->table.".uuid as id, ".$this->table.".id as internal_id");
        return $this->getWhere(['uuid' => $id])->getRow();
    }

    public function tasksByPId($bId, $pId, $eId, $params) {
        // return ['data' => $pId, 'total' => 2];
        if ($pId == '0' || !$pId || !isset($pId)) {
            $where = [
                "uuid_business_id" => $bId,
                "assigned_to" => $eId
            ];
        } else if ($pId == 'allTasks') {
            $where = [
                "uuid_business_id" => $bId,
            ];
        } else {
            $where = [
                "uuid_business_id" => $bId,
                "projects_id" => $pId,
                "assigned_to" => $eId
            ];
        }
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
        $builder->where($where);
        
        $total =  $this->db->table($this->table)->where($where)->countAllResults();
        return [
            'data' => $builder->get()->getResultArray(),
            'total' => $total
        ];
    }

    public function tasksStatusByEId($bId, $eId, $params)
    {
        $where = [
            "uuid_business_id" => $bId,
            "assigned_to" => $eId
        ];
        $record = $this->select('status')->where($where)->get()->getResultArray();
        $allTasksStatus = $this->select('status')->where(["uuid_business_id" => $bId])->get()->getResultArray();
        $totalProjects =  $this->db->table("projects")->where(["uuid_business_id" => $bId])->countAllResults();
        $totalAssignedProjects =  $this->db->table("projects")->where(["uuid_business_id" => $bId, 'employees_id' => $eId])->countAllResults();
        return [
            "data" => $record,
            "total_tasks_business" => $this->select('status')->where(["uuid_business_id" => $bId])->countAllResults(),
            "total_projects_business" => $totalProjects,
            "total_projects_assigned" => $totalAssignedProjects,
            "all_tasks_status" => $allTasksStatus
        ];
    }

    public function updateStatusByUUID($uuid = null, $status = null)
    {
        $sql = "UPDATE tasks SET status = '$status' WHERE uuid = '$uuid'";
        $query = $this->db->query($sql);
        return $query;
    }

}
