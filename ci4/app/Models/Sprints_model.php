<?php

namespace App\Models;

use CodeIgniter\Model;

class Sprints_model extends Model
{
    protected $table = 'sprints';

    public function __construct()
    {
        parent::__construct();
        if(property_exists($this, 'businessUuid') && session()->has('uuid_business')) {
            $this->businessUuid = session('uuid_business');
            }
        }
    public function getRows($id = false)
    {
        if ($id === false) {
            return $this->findAll();
        } else {
            return $this->getWhere(['id' => $id]);
        }
    }

    public function getSprintList()
    {
        $builder = $this->db->table($this->table);
        $builder->where($this->table . ".uuid_business_id",  $this->businessUuid);
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

    public function getCurrentSprint()
    {
        $builder = $this->db->table($this->table);
        $builder->where($this->table . ".uuid_business_id",  $this->businessUuid);
        $today_min = date("Y-m-d 00:00:00");

        $array = ['start_date <=' => $today_min, 'end_date >=' => $today_min];

        $builder->where($array);
        $builder->orderBy('id', 'DESC');

        $result =  $builder->get()->getResultArray();
        $sprint_id = 0;

        if (sizeof($result) > 0) {
            $sprint_id = $result[0]["id"];
        }

        return $sprint_id;
    }


    public function getNextSprint($currentSprint)
    {
        $today = date("Y-m-d 00:00:00");
        $result = $this->db->table($this->table)
            ->where($this->table . ".uuid_business_id",  $this->businessUuid)
            ->where('start_date >=', $today)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return sizeof($result) > 0 ? $result[0]["id"] : $currentSprint;
    }
}
