<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Accounts_model;
use App\Models\AccountingPeriods_model;

class TrialBalance extends CommonController
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
        $this->data['page_title'] = "Trial Balance";

        $currentPeriod = $this->periods_model->getCurrentPeriod($this->businessUuid);
        $asOfDate = $this->request->getGet('as_of_date') ?? ($currentPeriod['end_date'] ?? date('Y-m-d'));

        $this->data['as_of_date'] = $asOfDate;
        $this->data['report_data'] = $this->generateTrialBalance($asOfDate);
        $this->data['periods'] = $this->periods_model
            ->where('uuid_business_id', $this->businessUuid)
            ->orderBy('start_date', 'DESC')
            ->findAll();

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('reports/trial_balance', $this->data);
    }

    private function generateTrialBalance($asOfDate)
    {
        $db = \Config\Database::connect();

        $query = "
            SELECT
                a.account_code,
                a.account_name,
                a.account_type,
                a.normal_balance,
                a.opening_balance,
                COALESCE(SUM(jel.debit_amount), 0) as total_debit,
                COALESCE(SUM(jel.credit_amount), 0) as total_credit,
                (a.opening_balance + COALESCE(SUM(jel.debit_amount - jel.credit_amount), 0)) as debit_balance,
                (a.opening_balance + COALESCE(SUM(jel.credit_amount - jel.debit_amount), 0)) as credit_balance
            FROM accounts a
            LEFT JOIN journal_entry_lines jel ON jel.uuid_account_id = a.uuid
            LEFT JOIN journal_entries je ON je.uuid = jel.uuid_journal_entry_id
                AND je.is_posted = 1
                AND je.entry_date <= ?
            WHERE a.uuid_business_id = ?
                AND a.is_active = 1
            GROUP BY a.id, a.account_code, a.account_name, a.account_type, a.normal_balance, a.opening_balance
            HAVING ABS(debit_balance) > 0.01 OR ABS(credit_balance) > 0.01
            ORDER BY a.account_code ASC
        ";

        $accounts = $db->query($query, [$asOfDate, $this->businessUuid])->getResultArray();

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as &$account) {
            if ($account['normal_balance'] === 'Debit') {
                $balance = $account['debit_balance'];
                $account['debit'] = $balance > 0 ? $balance : 0;
                $account['credit'] = 0;
                $totalDebit += $account['debit'];
            } else {
                $balance = $account['credit_balance'];
                $account['credit'] = $balance > 0 ? $balance : 0;
                $account['debit'] = 0;
                $totalCredit += $account['credit'];
            }
        }

        return [
            'accounts' => $accounts,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'is_balanced' => abs($totalDebit - $totalCredit) < 0.01
        ];
    }
}
