<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Accounts_model;
use App\Models\AccountingPeriods_model;

class ProfitLoss extends CommonController
{
    protected $accounts_model;
    protected $periods_model;

    public function __construct()
    {
        parent::__construct();
        $this->accounts_model = new Accounts_model();
        $this->periods_model = new AccountingPeriods_model();
    }

    public function index()
    {
        $this->data['page_title'] = "Profit & Loss Statement";

        $currentPeriod = $this->periods_model->getCurrentPeriod($this->businessUuid);
        $startDate = $this->request->getGet('start_date') ?? ($currentPeriod['start_date'] ?? date('Y-01-01'));
        $endDate = $this->request->getGet('end_date') ?? ($currentPeriod['end_date'] ?? date('Y-m-d'));

        $this->data['start_date'] = $startDate;
        $this->data['end_date'] = $endDate;
        $this->data['report_data'] = $this->generateProfitLoss($startDate, $endDate);
        $this->data['periods'] = $this->periods_model
            ->where('uuid_business_id', $this->businessUuid)
            ->orderBy('start_date', 'DESC')
            ->findAll();

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('reports/profit_loss', $this->data);
    }

    private function generateProfitLoss($startDate, $endDate)
    {
        $db = \Config\Database::connect();

        $query = "
            SELECT
                a.account_code,
                a.account_name,
                a.account_type,
                a.account_subtype,
                COALESCE(SUM(
                    CASE
                        WHEN a.account_type = 'Revenue' THEN jel.credit_amount - jel.debit_amount
                        WHEN a.account_type = 'Expense' THEN jel.debit_amount - jel.credit_amount
                        ELSE 0
                    END
                ), 0) as amount
            FROM accounts a
            LEFT JOIN journal_entry_lines jel ON jel.uuid_account_id = a.uuid
            LEFT JOIN journal_entries je ON je.uuid = jel.uuid_journal_entry_id
                AND je.is_posted = 1
                AND je.entry_date BETWEEN ? AND ?
            WHERE a.uuid_business_id = ?
                AND a.is_active = 1
                AND a.account_type IN ('Revenue', 'Expense')
            GROUP BY a.id, a.account_code, a.account_name, a.account_type, a.account_subtype
            HAVING ABS(amount) > 0.01
            ORDER BY a.account_code ASC
        ";

        $accounts = $db->query($query, [$startDate, $endDate, $this->businessUuid])->getResultArray();

        $revenue = [];
        $expenses = [];

        foreach ($accounts as $account) {
            if ($account['account_type'] === 'Revenue') {
                $revenue[] = $account;
            } else {
                $expenses[] = $account;
            }
        }

        $totalRevenue = array_sum(array_column($revenue, 'amount'));
        $totalExpenses = array_sum(array_column($expenses, 'amount'));
        $netIncome = $totalRevenue - $totalExpenses;

        // Calculate gross profit (Revenue - COGS)
        $cogs = array_filter($expenses, function($acc) {
            return $acc['account_subtype'] === 'Cost of Sales';
        });
        $totalCOGS = array_sum(array_column($cogs, 'amount'));
        $grossProfit = $totalRevenue - $totalCOGS;

        // Operating expenses (exclude COGS)
        $operatingExpenses = array_filter($expenses, function($acc) {
            return $acc['account_subtype'] !== 'Cost of Sales';
        });
        $totalOperatingExpenses = array_sum(array_column($operatingExpenses, 'amount'));

        return [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'operating_expenses' => $operatingExpenses,
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCOGS,
            'gross_profit' => $grossProfit,
            'total_operating_expenses' => $totalOperatingExpenses,
            'net_income' => $netIncome,
            'gross_profit_margin' => $totalRevenue > 0 ? ($grossProfit / $totalRevenue * 100) : 0,
            'net_profit_margin' => $totalRevenue > 0 ? ($netIncome / $totalRevenue * 100) : 0
        ];
    }
}
