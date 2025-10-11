<?php

namespace App\Models;

use CodeIgniter\Model;

class Accounts_model extends Model
{
    protected $table = 'accounts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'uuid', 'uuid_business_id', 'account_code', 'account_name',
        'account_type', 'account_subtype', 'parent_account_id',
        'is_system_account', 'normal_balance', 'description',
        'opening_balance', 'current_balance', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'modified_at';

    protected $validationRules = [
        'account_code' => 'required|max_length[20]',
        'account_name' => 'required|max_length[255]',
        'account_type' => 'required|in_list[Asset,Liability,Equity,Revenue,Expense]',
        'normal_balance' => 'required|in_list[Debit,Credit]'
    ];

    /**
     * Get accounts by type
     */
    public function getAccountsByType($businessUuid, $accountType)
    {
        return $this->where('uuid_business_id', $businessUuid)
            ->where('account_type', $accountType)
            ->where('is_active', 1)
            ->orderBy('account_code', 'ASC')
            ->findAll();
    }

    /**
     * Get account balance
     */
    public function getAccountBalance($accountUuid, $startDate = null, $endDate = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('journal_entry_lines jel');
        $builder->select('
            SUM(jel.debit_amount) as total_debit,
            SUM(jel.credit_amount) as total_credit,
            (SUM(jel.debit_amount) - SUM(jel.credit_amount)) as balance
        ');
        $builder->join('journal_entries je', 'je.uuid = jel.uuid_journal_entry_id');
        $builder->where('jel.uuid_account_id', $accountUuid);
        $builder->where('je.is_posted', 1);

        if ($startDate) {
            $builder->where('je.entry_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('je.entry_date <=', $endDate);
        }

        $result = $builder->get()->getRowArray();
        return $result;
    }

    /**
     * Get chart of accounts tree
     */
    public function getChartOfAccountsTree($businessUuid)
    {
        $accounts = $this->where('uuid_business_id', $businessUuid)
            ->where('is_active', 1)
            ->orderBy('account_code', 'ASC')
            ->findAll();

        return $this->buildTree($accounts);
    }

    private function buildTree($accounts, $parentId = null)
    {
        $branch = [];
        foreach ($accounts as $account) {
            if ($account['parent_account_id'] == $parentId) {
                $children = $this->buildTree($accounts, $account['id']);
                if ($children) {
                    $account['children'] = $children;
                }
                $branch[] = $account;
            }
        }
        return $branch;
    }

    /**
     * Update account balance
     */
    public function updateAccountBalance($accountUuid)
    {
        $balance = $this->getAccountBalance($accountUuid);

        $this->where('uuid', $accountUuid)
            ->set('current_balance', $balance['balance'])
            ->update();
    }
}
