<?php

namespace App\Models;

use CodeIgniter\Model;

class Payments_model extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'uuid', 'uuid_business_id', 'payment_number', 'payment_date',
        'payment_type', 'payee_name', 'payee_type', 'payee_uuid',
        'invoice_uuid', 'invoice_number', 'amount', 'currency',
        'payment_method', 'bank_account_uuid', 'reference',
        'description', 'status', 'is_posted', 'journal_entry_uuid',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'modified_at';

    /**
     * Get next payment number
     */
    public function getNextPaymentNumber($businessUuid, $prefix = 'PAY')
    {
        $lastPayment = $this->where('uuid_business_id', $businessUuid)
            ->where('payment_number LIKE', $prefix . '%')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastPayment) {
            $number = (int)str_replace($prefix . '-', '', $lastPayment['payment_number']);
            return $prefix . '-' . str_pad($number + 1, 6, '0', STR_PAD_LEFT);
        }

        return $prefix . '-000001';
    }

    /**
     * Get payments with related data
     */
    public function getPaymentsWithDetails($businessUuid, $filters = [])
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, a.account_name as bank_account_name');
        $builder->join('accounts a', 'a.uuid = p.bank_account_uuid', 'LEFT');
        $builder->where('p.uuid_business_id', $businessUuid);

        if (!empty($filters['status'])) {
            $builder->where('p.status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $builder->where('p.payment_date >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $builder->where('p.payment_date <=', $filters['to_date']);
        }

        return $builder->orderBy('p.payment_date', 'DESC')->get()->getResultArray();
    }

    /**
     * Get payment by UUID with details
     */
    public function getPaymentByUuid($uuid)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, a.account_name as bank_account_name, a.account_code as bank_account_code');
        $builder->join('accounts a', 'a.uuid = p.bank_account_uuid', 'LEFT');
        $builder->where('p.uuid', $uuid);

        return $builder->get()->getRowArray();
    }

    /**
     * Post payment to journal
     */
    public function postToJournal($paymentUuid)
    {
        $payment = $this->where('uuid', $paymentUuid)->first();

        if (!$payment || $payment['is_posted']) {
            return false;
        }

        // Validate bank account exists
        if (empty($payment['bank_account_uuid'])) {
            log_message('error', 'Payment posting failed: No bank account specified for payment ' . $paymentUuid);
            return false;
        }

        try {
            // Create journal entry
            $journalModel = new \App\Models\JournalEntries_model();

            $entryNumber = $journalModel->getNextEntryNumber($payment['uuid_business_id'], 'PAY');
            $entryUuid = $this->generateUUID();

            $journalEntry = [
                'uuid' => $entryUuid,
                'uuid_business_id' => $payment['uuid_business_id'],
                'entry_number' => $entryNumber,
                'entry_date' => $payment['payment_date'],
                'entry_type' => 'General',
                'reference_type' => 'Payment',
                'reference_id' => $paymentUuid,
                'description' => 'Payment to ' . $payment['payee_name'] . ' - ' . $payment['payment_number'],
                'total_debit' => $payment['amount'],
                'total_credit' => $payment['amount'],
                'is_balanced' => 1,
                'is_posted' => 1,
                'posted_at' => date('Y-m-d H:i:s'),
                'created_by' => $payment['created_by']
            ];

            $journalModel->insert($journalEntry);

            // Create journal lines using database builder
            $db = \Config\Database::connect();

            // Debit: Accounts Payable or Expense
            $debitAccountUuid = $this->getDefaultAccountByType('Accounts Payable', $payment['uuid_business_id']);

            if (!$debitAccountUuid) {
                // Try Expenses account as fallback
                $debitAccountUuid = $this->getDefaultAccountByType('Expenses', $payment['uuid_business_id']);
            }

            if (!$debitAccountUuid) {
                log_message('error', 'Payment posting failed: No Accounts Payable or Expenses account found');
                return false;
            }

            $line1 = [
                'uuid' => $this->generateUUID(),
                'uuid_journal_entry_id' => $entryUuid,
                'uuid_account_id' => $debitAccountUuid,
                'line_number' => 1,
                'description' => 'Payment to ' . $payment['payee_name'],
                'debit_amount' => $payment['amount'],
                'credit_amount' => 0
            ];

            $db->table('journal_entry_lines')->insert($line1);

            // Credit: Bank Account
            $line2 = [
                'uuid' => $this->generateUUID(),
                'uuid_journal_entry_id' => $entryUuid,
                'uuid_account_id' => $payment['bank_account_uuid'],
                'line_number' => 2,
                'description' => 'Payment via ' . $payment['payment_method'],
                'debit_amount' => 0,
                'credit_amount' => $payment['amount']
            ];

            $db->table('journal_entry_lines')->insert($line2);

            // Update payment
            $this->where('uuid', $paymentUuid)->set([
                'is_posted' => 1,
                'journal_entry_uuid' => $entryUuid
            ])->update();

            return true;

        } catch (\Exception $e) {
            log_message('error', 'Payment posting failed: ' . $e->getMessage());
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
