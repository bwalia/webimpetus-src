<?php

namespace App\Models;

use CodeIgniter\Model;

class MeetingsModel extends Model
{
    protected $table = 'meetings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'uuid',
        'title',
        'description',
        'location',
        'meeting_type',
        'start_datetime',
        'end_datetime',
        'all_day',
        'organizer_id',
        'attendees',
        'customer_id',
        'project_id',
        'status',
        'reminder_minutes',
        'meeting_url',
        'color',
        'notes',
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
        'start_datetime' => 'required',
        'end_datetime' => 'required'
    ];
    protected $validationMessages = [
        'title' => [
            'required' => 'Meeting title is required',
            'min_length' => 'Meeting title must be at least 3 characters'
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
     * Get all meetings for current business
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
     * Get meetings by date range
     */
    public function getMeetingsByDateRange($startDate, $endDate)
    {
        $builder = $this->db->table($this->table);
        $builder->where($this->whereCond);
        $builder->where('start_datetime >=', $startDate);
        $builder->where('start_datetime <=', $endDate);
        $builder->orderBy('start_datetime', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get upcoming meetings
     */
    public function getUpcomingMeetings($limit = 10)
    {
        $builder = $this->db->table($this->table);
        $builder->where($this->whereCond);
        $builder->where('start_datetime >=', date('Y-m-d H:i:s'));
        $builder->where('status', 'scheduled');
        $builder->orderBy('start_datetime', 'ASC');
        $builder->limit($limit);

        return $builder->get()->getResultArray();
    }

    /**
     * Get meeting by UUID
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
