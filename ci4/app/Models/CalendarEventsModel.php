<?php

namespace App\Models;

use CodeIgniter\Model;

class CalendarEventsModel extends Model
{
    protected $table = 'calendar_events';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'uuid',
        'title',
        'description',
        'event_type',
        'start_datetime',
        'end_datetime',
        'all_day',
        'user_id',
        'customer_id',
        'project_id',
        'color',
        'priority',
        'is_recurring',
        'recurrence_rule',
        'uuid_business_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'start_datetime' => 'required'
    ];
    protected $validationMessages = [
        'title' => [
            'required' => 'Event title is required',
            'min_length' => 'Event title must be at least 3 characters'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    private $businessUuid;
    private $whereCond;

    public function __construct()
    {
        parent::__construct();
        $this->businessUuid = session('uuid_business');
        $this->whereCond[$this->table . '.uuid_business_id'] = $this->businessUuid;
    }

    /**
     * Get all events for current business
     */
    public function getRows($where = [])
    {
        $builder = $this->db->table($this->table);
        $builder->where($this->whereCond);

        if (!empty($where)) {
            $builder->where($where);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get events by date range
     */
    public function getEventsByDateRange($startDate, $endDate)
    {
        $builder = $this->db->table($this->table);
        $builder->where($this->whereCond);
        $builder->where('start_datetime >=', $startDate);
        $builder->where('start_datetime <=', $endDate);
        $builder->orderBy('start_datetime', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get event by UUID
     */
    public function getByUUID($uuid)
    {
        $builder = $this->db->table($this->table);
        $builder->where($this->whereCond);
        $builder->where('uuid', $uuid);

        return $builder->get()->getRowArray();
    }

    /**
     * Insert or update by UUID
     */
    public function insertOrUpdateByUUID($uuid, $data)
    {
        $data['uuid_business_id'] = $this->businessUuid;

        if (empty($uuid)) {
            // New record
            return $this->insert($data);
        } else {
            // Update existing
            $builder = $this->db->table($this->table);
            $builder->where('uuid', $uuid);
            $builder->where('uuid_business_id', $this->businessUuid);
            return $builder->update($data);
        }
    }
}
