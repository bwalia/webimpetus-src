<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Accounts_model;
use App\Models\JournalEntries_model;
use App\Models\AccountingPeriods_model;

class CashFlow extends CommonController
{
    protected $accounts_model;
    protected $journal_entries_model;
    protected $periods_model;
    protected $businessUuid;

    public function __construct()
    {
        parent::__construct();
        $this->accounts_model = new Accounts_model();
        $this->journal_entries_model = new JournalEntries_model();
        $this->periods_model = new AccountingPeriods_model();
        $this->businessUuid = session('uuid_business');
    }

    public function index()
    {
        $data = [];
        $data['title'] = 'Cash Flow Statement';

        // Get current accounting period
        $currentPeriod = $this->periods_model->getCurrentPeriod($this->businessUuid);

        if ($currentPeriod) {
            $data['start_date'] = $currentPeriod['start_date'];
            $data['end_date'] = $currentPeriod['end_date'];
            $data['period_name'] = $currentPeriod['period_name'];
        } else {
            // Default to current year
            $data['start_date'] = date('Y-01-01');
            $data['end_date'] = date('Y-12-31');
            $data['period_name'] = 'Current Year';
        }

        // Get all accounting periods for dropdown
        $data['periods'] = $this->periods_model->where('uuid_business_id', $this->businessUuid)
            ->orderBy('start_date', 'DESC')
            ->findAll();

        return view('reports/cash_flow', $data);
    }

    public function generate()
    {
        $startDate = $this->request->getPost('start_date') ?? date('Y-01-01');
        $endDate = $this->request->getPost('end_date') ?? date('Y-12-31');

        $cashFlowData = $this->generateCashFlowStatement($startDate, $endDate);

        return $this->response->setJSON([
            'status' => true,
            'data' => $cashFlowData,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }

    private function generateCashFlowStatement($startDate, $endDate)
    {
        // Get beginning and ending cash balances
        $beginningCash = $this->getCashBalance($startDate, true);
        $endingCash = $this->getCashBalance($endDate, false);

        // Operating Activities
        $operatingActivities = $this->getOperatingActivities($startDate, $endDate);

        // Investing Activities
        $investingActivities = $this->getInvestingActivities($startDate, $endDate);

        // Financing Activities
        $financingActivities = $this->getFinancingActivities($startDate, $endDate);

        // Calculate net cash flows
        $netOperating = array_sum(array_column($operatingActivities, 'amount'));
        $netInvesting = array_sum(array_column($investingActivities, 'amount'));
        $netFinancing = array_sum(array_column($financingActivities, 'amount'));

        $netCashChange = $netOperating + $netInvesting + $netFinancing;
        $calculatedEndingCash = $beginningCash + $netCashChange;

        return [
            'beginning_cash' => $beginningCash,
            'operating_activities' => $operatingActivities,
            'net_operating' => $netOperating,
            'investing_activities' => $investingActivities,
            'net_investing' => $netInvesting,
            'financing_activities' => $financingActivities,
            'net_financing' => $netFinancing,
            'net_cash_change' => $netCashChange,
            'ending_cash' => $endingCash,
            'calculated_ending_cash' => $calculatedEndingCash,
            'is_balanced' => abs($endingCash - $calculatedEndingCash) < 0.01
        ];
    }

    private function getCashBalance($date, $isBefore = false)
    {
        $db = \Config\Database::connect();

        // Get cash and bank accounts
        $cashAccounts = $this->accounts_model->where('uuid_business_id', $this->businessUuid)
            ->whereIn('account_code', ['1010', '1020']) // Cash and Bank Account
            ->orLike('account_name', 'Cash', 'both')
            ->orLike('account_name', 'Bank', 'both')
            ->findAll();

        $totalCash = 0;

        foreach ($cashAccounts as $account) {
            $builder = $db->table('journal_entry_lines jel');
            $builder->select('
                SUM(jel.debit_amount) as total_debit,
                SUM(jel.credit_amount) as total_credit
            ');
            $builder->join('journal_entries je', 'je.uuid = jel.uuid_journal_entry_id');
            $builder->where('jel.uuid_account_id', $account['uuid']);
            $builder->where('je.is_posted', 1);

            if ($isBefore) {
                $builder->where('je.entry_date <', $date);
            } else {
                $builder->where('je.entry_date <=', $date);
            }

            $result = $builder->get()->getRowArray();

            if ($result) {
                $debit = $result['total_debit'] ?? 0;
                $credit = $result['total_credit'] ?? 0;

                // Cash is an asset (normal balance: debit)
                $accountBalance = ($account['opening_balance'] ?? 0) + ($debit - $credit);
                $totalCash += $accountBalance;
            }
        }

        return $totalCash;
    }

    private function getOperatingActivities($startDate, $endDate)
    {
        $activities = [];

        // Get Net Income from Profit & Loss
        $netIncome = $this->getNetIncome($startDate, $endDate);
        $activities[] = [
            'description' => 'Net Income',
            'amount' => $netIncome,
            'type' => 'income'
        ];

        // Get changes in operating accounts
        // Accounts Receivable (decrease = cash in, increase = cash out)
        $arChange = $this->getAccountChange('1030', $startDate, $endDate); // Accounts Receivable
        if ($arChange != 0) {
            $activities[] = [
                'description' => 'Change in Accounts Receivable',
                'amount' => -$arChange, // Reversed because increase in AR means less cash
                'type' => 'adjustment'
            ];
        }

        // Inventory (decrease = cash in, increase = cash out)
        $inventoryChange = $this->getAccountChange('1050', $startDate, $endDate);
        if ($inventoryChange != 0) {
            $activities[] = [
                'description' => 'Change in Inventory',
                'amount' => -$inventoryChange,
                'type' => 'adjustment'
            ];
        }

        // Accounts Payable (increase = cash in, decrease = cash out)
        $apChange = $this->getAccountChange('2010', $startDate, $endDate);
        if ($apChange != 0) {
            $activities[] = [
                'description' => 'Change in Accounts Payable',
                'amount' => $apChange, // Not reversed because increase in AP means more cash retained
                'type' => 'adjustment'
            ];
        }

        // Depreciation (add back as non-cash expense)
        $depreciation = $this->getAccountActivity('5040', $startDate, $endDate); // Depreciation Expense
        if ($depreciation > 0) {
            $activities[] = [
                'description' => 'Depreciation Expense',
                'amount' => $depreciation,
                'type' => 'non_cash'
            ];
        }

        return $activities;
    }

    private function getInvestingActivities($startDate, $endDate)
    {
        $activities = [];

        // Fixed Assets purchases (decrease in cash)
        $fixedAssetsChange = $this->getAccountChange('1200', $startDate, $endDate);
        if ($fixedAssetsChange > 0) {
            $activities[] = [
                'description' => 'Purchase of Fixed Assets',
                'amount' => -$fixedAssetsChange,
                'type' => 'purchase'
            ];
        } elseif ($fixedAssetsChange < 0) {
            $activities[] = [
                'description' => 'Sale of Fixed Assets',
                'amount' => -$fixedAssetsChange, // Positive cash flow
                'type' => 'sale'
            ];
        }

        // Investments
        $investmentsChange = $this->getAccountChange('1100', $startDate, $endDate);
        if ($investmentsChange > 0) {
            $activities[] = [
                'description' => 'Purchase of Investments',
                'amount' => -$investmentsChange,
                'type' => 'purchase'
            ];
        } elseif ($investmentsChange < 0) {
            $activities[] = [
                'description' => 'Sale of Investments',
                'amount' => -$investmentsChange,
                'type' => 'sale'
            ];
        }

        return $activities;
    }

    private function getFinancingActivities($startDate, $endDate)
    {
        $activities = [];

        // Long-term Debt
        $debtChange = $this->getAccountChange('2100', $startDate, $endDate);
        if ($debtChange > 0) {
            $activities[] = [
                'description' => 'Proceeds from Long-term Debt',
                'amount' => $debtChange,
                'type' => 'proceeds'
            ];
        } elseif ($debtChange < 0) {
            $activities[] = [
                'description' => 'Repayment of Long-term Debt',
                'amount' => $debtChange, // Negative = cash out
                'type' => 'payment'
            ];
        }

        // Owner's Equity
        $equityChange = $this->getAccountChange('3010', $startDate, $endDate);
        if ($equityChange > 0) {
            $activities[] = [
                'description' => 'Owner Contributions',
                'amount' => $equityChange,
                'type' => 'contribution'
            ];
        } elseif ($equityChange < 0) {
            $activities[] = [
                'description' => 'Owner Draws/Dividends',
                'amount' => $equityChange, // Negative = cash out
                'type' => 'distribution'
            ];
        }

        return $activities;
    }

    private function getNetIncome($startDate, $endDate)
    {
        $db = \Config\Database::connect();

        // Get total revenue
        $revenueBuilder = $db->table('journal_entry_lines jel');
        $revenueBuilder->select('SUM(jel.credit_amount - jel.debit_amount) as total');
        $revenueBuilder->join('journal_entries je', 'je.uuid = jel.uuid_journal_entry_id');
        $revenueBuilder->join('accounts a', 'a.uuid = jel.uuid_account_id');
        $revenueBuilder->where('a.account_type', 'Revenue');
        $revenueBuilder->where('a.uuid_business_id', $this->businessUuid);
        $revenueBuilder->where('je.is_posted', 1);
        $revenueBuilder->where('je.entry_date >=', $startDate);
        $revenueBuilder->where('je.entry_date <=', $endDate);
        $revenue = $revenueBuilder->get()->getRowArray()['total'] ?? 0;

        // Get total expenses
        $expenseBuilder = $db->table('journal_entry_lines jel');
        $expenseBuilder->select('SUM(jel.debit_amount - jel.credit_amount) as total');
        $expenseBuilder->join('journal_entries je', 'je.uuid = jel.uuid_journal_entry_id');
        $expenseBuilder->join('accounts a', 'a.uuid = jel.uuid_account_id');
        $expenseBuilder->where('a.account_type', 'Expense');
        $expenseBuilder->where('a.uuid_business_id', $this->businessUuid);
        $expenseBuilder->where('je.is_posted', 1);
        $expenseBuilder->where('je.entry_date >=', $startDate);
        $expenseBuilder->where('je.entry_date <=', $endDate);
        $expenses = $expenseBuilder->get()->getRowArray()['total'] ?? 0;

        return $revenue - $expenses;
    }

    private function getAccountChange($accountCode, $startDate, $endDate)
    {
        $account = $this->accounts_model->where('uuid_business_id', $this->businessUuid)
            ->where('account_code', $accountCode)
            ->first();

        if (!$account) {
            return 0;
        }

        $beginningBalance = $this->getAccountBalanceAtDate($account['uuid'], $startDate, true);
        $endingBalance = $this->getAccountBalanceAtDate($account['uuid'], $endDate, false);

        return $endingBalance - $beginningBalance;
    }

    private function getAccountActivity($accountCode, $startDate, $endDate)
    {
        $db = \Config\Database::connect();

        $account = $this->accounts_model->where('uuid_business_id', $this->businessUuid)
            ->where('account_code', $accountCode)
            ->first();

        if (!$account) {
            return 0;
        }

        $builder = $db->table('journal_entry_lines jel');
        $builder->select('SUM(jel.debit_amount - jel.credit_amount) as total');
        $builder->join('journal_entries je', 'je.uuid = jel.uuid_journal_entry_id');
        $builder->where('jel.uuid_account_id', $account['uuid']);
        $builder->where('je.is_posted', 1);
        $builder->where('je.entry_date >=', $startDate);
        $builder->where('je.entry_date <=', $endDate);

        return $builder->get()->getRowArray()['total'] ?? 0;
    }

    private function getAccountBalanceAtDate($accountUuid, $date, $isBefore = false)
    {
        $db = \Config\Database::connect();

        $builder = $db->table('journal_entry_lines jel');
        $builder->select('
            SUM(jel.debit_amount) as total_debit,
            SUM(jel.credit_amount) as total_credit
        ');
        $builder->join('journal_entries je', 'je.uuid = jel.uuid_journal_entry_id');
        $builder->where('jel.uuid_account_id', $accountUuid);
        $builder->where('je.is_posted', 1);

        if ($isBefore) {
            $builder->where('je.entry_date <', $date);
        } else {
            $builder->where('je.entry_date <=', $date);
        }

        $result = $builder->get()->getRowArray();

        if ($result) {
            $account = $this->accounts_model->where('uuid', $accountUuid)->first();
            $debit = $result['total_debit'] ?? 0;
            $credit = $result['total_credit'] ?? 0;

            // Calculate based on normal balance
            if ($account['normal_balance'] == 'Debit') {
                return ($account['opening_balance'] ?? 0) + ($debit - $credit);
            } else {
                return ($account['opening_balance'] ?? 0) + ($credit - $debit);
            }
        }

        return 0;
    }

    public function exportPDF($uuid = 0, $view = '')
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-01-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-12-31');

        $data = $this->generateCashFlowStatement($startDate, $endDate);
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;
        $data['company_name'] = 'Your Company Name';

        // Generate PDF using parent method
        return parent::exportPDF(0, 'reports/cash_flow_pdf');
    }
}
