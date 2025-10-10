<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountingPeriods_model extends Model
{
    protected $table = 'accounting_periods';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'uuid', 'uuid_business_id', 'period_name', 'start_date',
        'end_date', 'is_current', 'is_closed', 'closed_at',
        'closed_by', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'modified_at';

    /**
     * Get current accounting period
     */
    public function getCurrentPeriod($businessUuid)
    {
        return $this->where('uuid_business_id', $businessUuid)
            ->where('is_current', 1)
            ->first();
    }

    /**
     * Get period by date
     */
    public function getPeriodByDate($businessUuid, $date)
    {
        return $this->where('uuid_business_id', $businessUuid)
            ->where('start_date <=', $date)
            ->where('end_date >=', $date)
            ->first();
    }

    /**
     * Close accounting period
     */
    public function closePeriod($periodUuid, $userUuid)
    {
        return $this->where('uuid', $periodUuid)
            ->set([
                'is_closed' => 1,
                'closed_at' => date('Y-m-d H:i:s'),
                'closed_by' => $userUuid
            ])
            ->update();
    }

    /**
     * Set current period
     */
    public function setCurrentPeriod($periodUuid, $businessUuid)
    {
        // First, unset all current periods for this business
        $this->where('uuid_business_id', $businessUuid)
            ->set('is_current', 0)
            ->update();

        // Then set the new current period
        return $this->where('uuid', $periodUuid)
            ->set('is_current', 1)
            ->update();
    }
}
