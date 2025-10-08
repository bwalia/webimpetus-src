<?php

namespace App\Models;
use CodeIgniter\Model;

class Vat_return_model extends Model
{
    protected $table = 'vat_returns';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid',
        'uuid_business_id',
        'quarter',
        'year',
        'period_start',
        'period_end',
        'uk_vat_total',
        'uk_sales_total',
        'non_uk_vat_total',
        'non_uk_sales_total',
        'total_vat_due',
        'status',
        'submitted_at'
    ];

    /**
     * Get VAT return records
     */
    public function getRows($id = false)
    {
        if($id === false){
            return $this->where('uuid_business_id', session("uuid_business"))
                        ->orderBy('year', 'DESC')
                        ->orderBy('quarter', 'DESC')
                        ->findAll();
        }else{
            return $this->getWhere(['id' => $id, 'uuid_business_id' => session("uuid_business")]);
        }
    }

    /**
     * Get VAT return by UUID
     */
    public function getByUUID($uuid)
    {
        return $this->where(['uuid' => $uuid, 'uuid_business_id' => session("uuid_business")])
                    ->first();
    }

    /**
     * Calculate UK VAT for a specific period
     * UK customers: VAT should be accounted for
     */
    public function calculateUKVAT($startDate, $endDate, $businessUuid)
    {
        $builder = $this->db->table('sales_invoices as si');
        $builder->select('
            COALESCE(SUM(si.total_tax), 0) as total_vat,
            COALESCE(SUM(si.total), 0) as total_sales,
            COUNT(si.id) as invoice_count
        ');
        $builder->join('customers c', 'c.id = si.client_id', 'left');
        $builder->where('si.uuid_business_id', $businessUuid);
        $builder->where('si.created_at >=', $startDate);
        $builder->where('si.created_at <=', $endDate);
        $builder->where('(c.country IS NULL OR c.country = "" OR c.country = "UK" OR c.country = "United Kingdom" OR c.country = "GB")', null, false);

        return $builder->get()->getRow();
    }

    /**
     * Calculate non-UK VAT for a specific period
     * Non-UK customers: VAT should be tracked separately (usually zero-rated or exempt)
     */
    public function calculateNonUKVAT($startDate, $endDate, $businessUuid)
    {
        $builder = $this->db->table('sales_invoices as si');
        $builder->select('
            COALESCE(SUM(si.total_tax), 0) as total_vat,
            COALESCE(SUM(si.total), 0) as total_sales,
            COUNT(si.id) as invoice_count
        ');
        $builder->join('customers c', 'c.id = si.client_id', 'left');
        $builder->where('si.uuid_business_id', $businessUuid);
        $builder->where('si.created_at >=', $startDate);
        $builder->where('si.created_at <=', $endDate);
        $builder->where('c.country IS NOT NULL', null, false);
        $builder->where('c.country != ""', null, false);
        $builder->where('c.country != "UK"', null, false);
        $builder->where('c.country != "United Kingdom"', null, false);
        $builder->where('c.country != "GB"', null, false);

        return $builder->get()->getRow();
    }

    /**
     * Get detailed invoice breakdown for a VAT period
     */
    public function getInvoiceBreakdown($startDate, $endDate, $businessUuid, $isUK = true)
    {
        $builder = $this->db->table('sales_invoices as si');
        $builder->select('
            si.id,
            si.uuid,
            si.invoice_number,
            si.custom_invoice_number,
            si.created_at,
            si.total,
            si.total_tax,
            si.total_due_with_tax,
            c.company_name,
            c.country,
            si.inv_tax_code,
            si.invoice_tax_rate
        ');
        $builder->join('customers c', 'c.id = si.client_id', 'left');
        $builder->where('si.uuid_business_id', $businessUuid);
        $builder->where('si.created_at >=', $startDate);
        $builder->where('si.created_at <=', $endDate);

        if ($isUK) {
            $builder->where('(c.country IS NULL OR c.country = "" OR c.country = "UK" OR c.country = "United Kingdom" OR c.country = "GB")', null, false);
        } else {
            $builder->where('c.country IS NOT NULL', null, false);
            $builder->where('c.country != ""', null, false);
            $builder->where('c.country != "UK"', null, false);
            $builder->where('c.country != "United Kingdom"', null, false);
            $builder->where('c.country != "GB"', null, false);
        }

        $builder->orderBy('si.created_at', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get quarter dates for a given year and quarter
     */
    public function getQuarterDates($year, $quarter)
    {
        $quarters = [
            1 => ['start' => "$year-01-01 00:00:00", 'end' => "$year-03-31 23:59:59"],
            2 => ['start' => "$year-04-01 00:00:00", 'end' => "$year-06-30 23:59:59"],
            3 => ['start' => "$year-07-01 00:00:00", 'end' => "$year-09-30 23:59:59"],
            4 => ['start' => "$year-10-01 00:00:00", 'end' => "$year-12-31 23:59:59"],
        ];

        return $quarters[$quarter] ?? null;
    }

    /**
     * Generate VAT return for a specific quarter
     */
    public function generateQuarterlyReturn($year, $quarter, $businessUuid)
    {
        $dates = $this->getQuarterDates($year, $quarter);
        if (!$dates) {
            return false;
        }

        $ukVat = $this->calculateUKVAT($dates['start'], $dates['end'], $businessUuid);
        $nonUkVat = $this->calculateNonUKVAT($dates['start'], $dates['end'], $businessUuid);

        return [
            'year' => $year,
            'quarter' => $quarter,
            'period_start' => $dates['start'],
            'period_end' => $dates['end'],
            'uk_vat_total' => $ukVat->total_vat,
            'uk_sales_total' => $ukVat->total_sales,
            'uk_invoice_count' => $ukVat->invoice_count,
            'non_uk_vat_total' => $nonUkVat->total_vat,
            'non_uk_sales_total' => $nonUkVat->total_sales,
            'non_uk_invoice_count' => $nonUkVat->invoice_count,
            'total_vat_due' => $ukVat->total_vat + $nonUkVat->total_vat,
        ];
    }

    /**
     * Check if VAT return already exists for a quarter
     */
    public function returnExists($year, $quarter, $businessUuid)
    {
        return $this->where([
            'year' => $year,
            'quarter' => $quarter,
            'uuid_business_id' => $businessUuid
        ])->first();
    }

    /**
     * Delete VAT return
     */
    public function deleteData($id)
    {
        $query = $this->db->table($this->table)->delete(array('id' => $id));
        return $query;
    }

    /**
     * Update VAT return
     */
    public function updateData($id = null, $data = null)
    {
        $query = $this->db->table($this->table)->update($data, array('id' => $id));
        return $query;
    }
}
