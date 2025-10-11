<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalEntries_model extends Model
{
    protected $table = 'journal_entries';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'uuid', 'uuid_business_id', 'entry_number', 'entry_date',
        'entry_type', 'reference_type', 'reference_id', 'description',
        'total_debit', 'total_credit', 'is_balanced', 'is_posted',
        'posted_at', 'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'modified_at';

    /**
     * Get next entry number
     */
    public function getNextEntryNumber($businessUuid, $prefix = 'JE')
    {
        $lastEntry = $this->where('uuid_business_id', $businessUuid)
            ->where('entry_number LIKE', $prefix . '%')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastEntry) {
            $number = (int)str_replace($prefix, '', $lastEntry['entry_number']);
            return $prefix . str_pad($number + 1, 6, '0', STR_PAD_LEFT);
        }

        return $prefix . '000001';
    }

    /**
     * Post journal entry
     */
    public function postEntry($entryUuid)
    {
        return $this->where('uuid', $entryUuid)
            ->set([
                'is_posted' => 1,
                'posted_at' => date('Y-m-d H:i:s')
            ])
            ->update();
    }

    /**
     * Get journal entries with lines
     */
    public function getEntryWithLines($entryUuid)
    {
        $entry = $this->where('uuid', $entryUuid)->first();

        if ($entry) {
            $db = \Config\Database::connect();
            $entry['lines'] = $db->table('journal_entry_lines jel')
                ->select('jel.*, a.account_code, a.account_name')
                ->join('accounts a', 'a.uuid = jel.uuid_account_id')
                ->where('jel.uuid_journal_entry_id', $entryUuid)
                ->orderBy('jel.line_number', 'ASC')
                ->get()
                ->getResultArray();
        }

        return $entry;
    }
}
