<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Accounts_model;
use App\Models\AccountingPeriods_model;

class Accounts extends CommonController
{
    protected $accounts_model;
    protected $periods_model;

    public function __construct()
    {
        parent::__construct();
        $this->accounts_model = new Accounts_model();
        $this->periods_model = new AccountingPeriods_model();
    }

    /**
     * List all accounts
     */
    public function index()
    {
        $this->data['page_title'] = "Chart of Accounts";
        $this->data['tableName'] = "accounts";

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('accounts/list', $this->data);
    }

    /**
     * Edit/Add account
     */
    public function edit($uuid = null)
    {
        $this->data['page_title'] = $uuid ? "Edit Account" : "Add Account";
        $this->data['tableName'] = "accounts";

        if ($uuid) {
            $account = $this->accounts_model
                ->where('uuid', $uuid)
                ->where('uuid_business_id', $this->businessUuid)
                ->first();

            if (!$account) {
                return redirect()->to('/accounts')->with('error', 'Account not found');
            }

            $this->data['account'] = (object) $account;
        } else {
            $this->data['account'] = (object) [];
        }

        // Get parent accounts for dropdown
        $this->data['parent_accounts'] = $this->accounts_model
            ->where('uuid_business_id', $this->businessUuid)
            ->where('is_active', 1)
            ->orderBy('account_code', 'ASC')
            ->findAll();

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('accounts/edit', $this->data);
    }

    /**
     * Update/Insert account
     */
    public function update()
    {
        $input = $this->request->getPost();
        $existingId = $this->request->getPost('id');

        // Check permissions: update for existing records, create for new records
        if ($existingId && !$this->checkPermission('update')) {
            session()->setFlashdata('message', 'You do not have permission to update records in this module!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/accounts');
        }

        if (!$existingId && !$this->checkPermission('create')) {
            session()->setFlashdata('message', 'You do not have permission to create records in this module!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/accounts');
        }

        // Generate UUID if new
        if (empty($input['uuid'])) {
            $input['uuid'] = $this->generateUUID();
            $input['created_at'] = date('Y-m-d H:i:s');
        }

        $input['uuid_business_id'] = $this->businessUuid;
        $input['is_active'] = $this->request->getPost('is_active') ? 1 : 0;
        $input['is_system_account'] = $this->request->getPost('is_system_account') ? 1 : 0;
        $input['modified_at'] = date('Y-m-d H:i:s');

        // Set normal balance based on account type
        if (empty($input['normal_balance'])) {
            $input['normal_balance'] = $this->getNormalBalance($input['account_type']);
        }

        try {
            if ($this->request->getPost('id')) {
                // Update existing
                $this->accounts_model->where('uuid', $input['uuid'])->set($input)->update();
                $message = 'Account updated successfully';
            } else {
                // Insert new
                $this->accounts_model->insert($input);
                $message = 'Account created successfully';
            }

            return redirect()->to('/accounts')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error saving account: ' . $e->getMessage());
        }
    }

    /**
     * Delete account
     */
    public function delete($uuid)
    {
        try {
            // Check if account has transactions
            $db = \Config\Database::connect();
            $hasTransactions = $db->table('journal_entry_lines')
                ->where('uuid_account_id', $uuid)
                ->countAllResults() > 0;

            if ($hasTransactions) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Cannot delete account with existing transactions. Please deactivate it instead.'
                ]);
            }

            $this->accounts_model
                ->where('uuid', $uuid)
                ->where('uuid_business_id', $this->businessUuid)
                ->delete();

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Account deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error deleting account: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get accounts list for DataTables
     */
    public function accountsList()
    {
        $accounts = $this->accounts_model
            ->where('uuid_business_id', $this->businessUuid)
            ->orderBy('account_code', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'data' => $accounts
        ]);
    }

    /**
     * Initialize default chart of accounts
     */
    public function initializeChartOfAccounts()
    {
        $defaultAccounts = $this->getDefaultChartOfAccounts();

        $inserted = 0;
        foreach ($defaultAccounts as $account) {
            $account['uuid'] = $this->generateUUID();
            $account['uuid_business_id'] = $this->businessUuid;

            try {
                $this->accounts_model->insert($account);
                $inserted++;
            } catch (\Exception $e) {
                // Skip if account code already exists
                continue;
            }
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => "Initialized {$inserted} accounts successfully",
            'count' => $inserted
        ]);
    }

    /**
     * Get normal balance for account type
     */
    private function getNormalBalance($accountType)
    {
        $debitTypes = ['Asset', 'Expense'];
        return in_array($accountType, $debitTypes) ? 'Debit' : 'Credit';
    }

    /**
     * Default chart of accounts template
     */
    private function getDefaultChartOfAccounts()
    {
        return [
            // Assets
            ['account_code' => '1000', 'account_name' => 'Current Assets', 'account_type' => 'Asset', 'account_subtype' => 'Current Asset', 'normal_balance' => 'Debit', 'is_system_account' => 1],
            ['account_code' => '1010', 'account_name' => 'Cash', 'account_type' => 'Asset', 'account_subtype' => 'Current Asset', 'normal_balance' => 'Debit', 'is_system_account' => 1],
            ['account_code' => '1020', 'account_name' => 'Bank Account', 'account_type' => 'Asset', 'account_subtype' => 'Current Asset', 'normal_balance' => 'Debit', 'is_system_account' => 1],
            ['account_code' => '1100', 'account_name' => 'Accounts Receivable', 'account_type' => 'Asset', 'account_subtype' => 'Current Asset', 'normal_balance' => 'Debit', 'is_system_account' => 1],
            ['account_code' => '1200', 'account_name' => 'Inventory', 'account_type' => 'Asset', 'account_subtype' => 'Current Asset', 'normal_balance' => 'Debit', 'is_system_account' => 0],
            ['account_code' => '1500', 'account_name' => 'Fixed Assets', 'account_type' => 'Asset', 'account_subtype' => 'Fixed Asset', 'normal_balance' => 'Debit', 'is_system_account' => 0],
            ['account_code' => '1510', 'account_name' => 'Equipment', 'account_type' => 'Asset', 'account_subtype' => 'Fixed Asset', 'normal_balance' => 'Debit', 'is_system_account' => 0],
            ['account_code' => '1520', 'account_name' => 'Furniture & Fixtures', 'account_type' => 'Asset', 'account_subtype' => 'Fixed Asset', 'normal_balance' => 'Debit', 'is_system_account' => 0],
            ['account_code' => '1530', 'account_name' => 'Accumulated Depreciation', 'account_type' => 'Asset', 'account_subtype' => 'Fixed Asset', 'normal_balance' => 'Credit', 'is_system_account' => 0],

            // Liabilities
            ['account_code' => '2000', 'account_name' => 'Current Liabilities', 'account_type' => 'Liability', 'account_subtype' => 'Current Liability', 'normal_balance' => 'Credit', 'is_system_account' => 1],
            ['account_code' => '2100', 'account_name' => 'Accounts Payable', 'account_type' => 'Liability', 'account_subtype' => 'Current Liability', 'normal_balance' => 'Credit', 'is_system_account' => 1],
            ['account_code' => '2200', 'account_name' => 'Tax Payable', 'account_type' => 'Liability', 'account_subtype' => 'Current Liability', 'normal_balance' => 'Credit', 'is_system_account' => 0],
            ['account_code' => '2500', 'account_name' => 'Long-term Liabilities', 'account_type' => 'Liability', 'account_subtype' => 'Long-term Liability', 'normal_balance' => 'Credit', 'is_system_account' => 0],
            ['account_code' => '2510', 'account_name' => 'Loans Payable', 'account_type' => 'Liability', 'account_subtype' => 'Long-term Liability', 'normal_balance' => 'Credit', 'is_system_account' => 0],

            // Equity
            ['account_code' => '3000', 'account_name' => 'Equity', 'account_type' => 'Equity', 'account_subtype' => 'Owner Equity', 'normal_balance' => 'Credit', 'is_system_account' => 1],
            ['account_code' => '3010', 'account_name' => 'Owner\'s Capital', 'account_type' => 'Equity', 'account_subtype' => 'Owner Equity', 'normal_balance' => 'Credit', 'is_system_account' => 1],
            ['account_code' => '3020', 'account_name' => 'Retained Earnings', 'account_type' => 'Equity', 'account_subtype' => 'Owner Equity', 'normal_balance' => 'Credit', 'is_system_account' => 1],
            ['account_code' => '3030', 'account_name' => 'Owner\'s Draw', 'account_type' => 'Equity', 'account_subtype' => 'Owner Equity', 'normal_balance' => 'Debit', 'is_system_account' => 0],

            // Revenue
            ['account_code' => '4000', 'account_name' => 'Revenue', 'account_type' => 'Revenue', 'account_subtype' => 'Sales Revenue', 'normal_balance' => 'Credit', 'is_system_account' => 1],
            ['account_code' => '4010', 'account_name' => 'Sales Revenue', 'account_type' => 'Revenue', 'account_subtype' => 'Sales Revenue', 'normal_balance' => 'Credit', 'is_system_account' => 1],
            ['account_code' => '4100', 'account_name' => 'Service Revenue', 'account_type' => 'Revenue', 'account_subtype' => 'Service Revenue', 'normal_balance' => 'Credit', 'is_system_account' => 0],
            ['account_code' => '4900', 'account_name' => 'Other Revenue', 'account_type' => 'Revenue', 'account_subtype' => 'Other Revenue', 'normal_balance' => 'Credit', 'is_system_account' => 0],

            // Expenses
            ['account_code' => '5000', 'account_name' => 'Cost of Goods Sold', 'account_type' => 'Expense', 'account_subtype' => 'Cost of Sales', 'normal_balance' => 'Debit', 'is_system_account' => 1],
            ['account_code' => '6000', 'account_name' => 'Operating Expenses', 'account_type' => 'Expense', 'account_subtype' => 'Operating Expense', 'normal_balance' => 'Debit', 'is_system_account' => 1],
            ['account_code' => '6010', 'account_name' => 'Salaries & Wages', 'account_type' => 'Expense', 'account_subtype' => 'Operating Expense', 'normal_balance' => 'Debit', 'is_system_account' => 0],
            ['account_code' => '6020', 'account_name' => 'Rent Expense', 'account_type' => 'Expense', 'account_subtype' => 'Operating Expense', 'normal_balance' => 'Debit', 'is_system_account' => 0],
            ['account_code' => '6030', 'account_name' => 'Utilities Expense', 'account_type' => 'Expense', 'account_subtype' => 'Operating Expense', 'normal_balance' => 'Debit', 'is_system_account' => 0],
            ['account_code' => '6040', 'account_name' => 'Office Supplies', 'account_type' => 'Expense', 'account_subtype' => 'Operating Expense', 'normal_balance' => 'Debit', 'is_system_account' => 0],
            ['account_code' => '6050', 'account_name' => 'Insurance Expense', 'account_type' => 'Expense', 'account_subtype' => 'Operating Expense', 'normal_balance' => 'Debit', 'is_system_account' => 0],
            ['account_code' => '6060', 'account_name' => 'Depreciation Expense', 'account_type' => 'Expense', 'account_subtype' => 'Operating Expense', 'normal_balance' => 'Debit', 'is_system_account' => 0],
            ['account_code' => '6900', 'account_name' => 'Other Expenses', 'account_type' => 'Expense', 'account_subtype' => 'Other Expense', 'normal_balance' => 'Debit', 'is_system_account' => 0],
        ];
    }

    /**
     * Generate UUID v4
     */
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
