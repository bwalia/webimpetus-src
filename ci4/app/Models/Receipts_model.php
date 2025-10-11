<?php

namespace App\Models;

use CodeIgniter\Model;

class Receipts_model extends Model
{
    protected $table = 'receipts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'uuid', 'uuid_business_id', 'receipt_number', 'receipt_date',
        'receipt_type', 'payer_name', 'payer_type', 'payer_uuid',
        'invoice_uuid', 'invoice_number', 'amount', 'currency',
        'payment_method', 'bank_account_uuid', 'reference',
        'description', 'status', 'is_posted', 'journal_entry_uuid',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'modified_at';

    /**
     * Get next receipt number
     */
    public function getNextReceiptNumber($businessUuid, $prefix = 'REC')
    {
        $lastReceipt = $this->where('uuid_business_id', $businessUuid)
            ->where('receipt_number LIKE', $prefix . '%')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastReceipt) {
            $number = (int)str_replace($prefix . '-', '', $lastReceipt['receipt_number']);
            return $prefix . '-' . str_pad($number + 1, 6, '0', STR_PAD_LEFT);
        }

        return $prefix . '-000001';
    }

    /**
     * Get receipts with related data
     */
    public function getReceiptsWithDetails($businessUuid, $filters = [])
    {
        $builder = $this->db->table($this->table . ' r');
        $builder->select('r.*, a.account_name as bank_account_name');
        $builder->join('accounts a', 'a.uuid = r.bank_account_uuid', 'LEFT');
        $builder->where('r.uuid_business_id', $businessUuid);

        if (!empty($filters['status'])) {
            $builder->where('r.status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $builder->where('r.receipt_date >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $builder->where('r.receipt_date <=', $filters['to_date']);
        }

        return $builder->orderBy('r.receipt_date', 'DESC')->get()->getResultArray();
    }

    /**
     * Get receipt by UUID with details
     */
    public function getReceiptByUuid($uuid)
    {
        $builder = $this->db->table($this->table . ' r');
        $builder->select('r.*, a.account_name as bank_account_name, a.account_code as bank_account_code');
        $builder->join('accounts a', 'a.uuid = r.bank_account_uuid', 'LEFT');
        $builder->where('r.uuid', $uuid);

        return $builder->get()->getRowArray();
    }

    /**
     * Post receipt to journal
     */
    public function postToJournal($receiptUuid)
    {
        $receipt = $this->where('uuid', $receiptUuid)->first();

        if (!$receipt || $receipt['is_posted']) {
            return false;
        }

        // Validate bank account exists
        if (empty($receipt['bank_account_uuid'])) {
            log_message('error', 'Receipt posting failed: No bank account specified for receipt ' . $receiptUuid);
            return false;
        }

        try {
            // Create journal entry
            $journalModel = new \App\Models\JournalEntries_model();

            $entryNumber = $journalModel->getNextEntryNumber($receipt['uuid_business_id'], 'REC');
            $entryUuid = $this->generateUUID();

            $journalEntry = [
                'uuid' => $entryUuid,
                'uuid_business_id' => $receipt['uuid_business_id'],
                'entry_number' => $entryNumber,
                'entry_date' => $receipt['receipt_date'],
                'entry_type' => 'General',
                'reference_type' => 'Receipt',
                'reference_id' => $receiptUuid,
                'description' => 'Receipt from ' . $receipt['payer_name'] . ' - ' . $receipt['receipt_number'],
                'total_debit' => $receipt['amount'],
                'total_credit' => $receipt['amount'],
                'is_balanced' => 1,
                'is_posted' => 1,
                'posted_at' => date('Y-m-d H:i:s'),
                'created_by' => $receipt['created_by']
            ];

            $journalModel->insert($journalEntry);

            // Create journal lines using database builder
            $db = \Config\Database::connect();

            // Debit: Bank Account
            $line1 = [
                'uuid' => $this->generateUUID(),
                'uuid_journal_entry_id' => $entryUuid,
                'uuid_account_id' => $receipt['bank_account_uuid'],
                'line_number' => 1,
                'description' => 'Receipt from ' . $receipt['payer_name'],
                'debit_amount' => $receipt['amount'],
                'credit_amount' => 0
            ];

            $db->table('journal_entry_lines')->insert($line1);

            // Credit: Accounts Receivable or Sales
            $creditAccountUuid = $this->getDefaultAccountByType('Accounts Receivable', $receipt['uuid_business_id']);

            if (!$creditAccountUuid) {
                // Try Sales account as fallback
                $creditAccountUuid = $this->getDefaultAccountByType('Sales', $receipt['uuid_business_id']);
            }

            if (!$creditAccountUuid) {
                log_message('error', 'Receipt posting failed: No Accounts Receivable or Sales account found');
                return false;
            }

            $line2 = [
                'uuid' => $this->generateUUID(),
                'uuid_journal_entry_id' => $entryUuid,
                'uuid_account_id' => $creditAccountUuid,
                'line_number' => 2,
                'description' => 'Receipt via ' . $receipt['payment_method'],
                'debit_amount' => 0,
                'credit_amount' => $receipt['amount']
            ];

            $db->table('journal_entry_lines')->insert($line2);

            // Update receipt
            $this->where('uuid', $receiptUuid)->set([
                'is_posted' => 1,
                'journal_entry_uuid' => $entryUuid
            ])->update();

            return true;

        } catch (\Exception $e) {
            log_message('error', 'Receipt posting failed: ' . $e->getMessage());
            return false;
        }
    }

    private function getDefaultAccountByType($type, $businessUuid)
    {
        $accountsModel = new \App\Models\Accounts_model();
        $account = $accountsModel
            ->where('uuid_business_id', $businessUuid)
            ->where('account_name', $type)
            ->first();

        return $account['uuid'] ?? null;
    }

    private function generateUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
