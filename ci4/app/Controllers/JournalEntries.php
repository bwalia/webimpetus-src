<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\JournalEntries_model;
use App\Models\Accounts_model;

class JournalEntries extends CommonController
{
    protected $journal_model;
    protected $accounts_model;

    public function __construct()
    {
        parent::__construct();
        $this->journal_model = new JournalEntries_model();
        $this->accounts_model = new Accounts_model();
    }

    public function index()
    {
        $this->data['page_title'] = "Journal Entries";
        $this->data['tableName'] = "journal_entries";

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('journal_entries/list', $this->data);
    }

    public function edit($uuid = null)
    {
        $this->data['page_title'] = $uuid ? "Edit Journal Entry" : "New Journal Entry";
        $this->data['tableName'] = "journal_entries";

        if ($uuid) {
            $entry = $this->journal_model->getEntryWithLines($uuid);

            if (!$entry) {
                return redirect()->to('/journal-entries')->with('error', 'Journal entry not found');
            }

            $this->data['entry'] = (object) $entry;
        } else {
            $this->data['entry'] = (object) [
                'entry_number' => $this->journal_model->getNextEntryNumber($this->businessUuid),
                'entry_date' => date('Y-m-d'),
                'entry_type' => 'Manual'
            ];
        }

        // Get active accounts
        $this->data['accounts'] = $this->accounts_model
            ->where('uuid_business_id', $this->businessUuid)
            ->where('is_active', 1)
            ->orderBy('account_code', 'ASC')
            ->findAll();

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('journal_entries/edit', $this->data);
    }

    public function update()
    {
        $input = $this->request->getPost();
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Prepare journal entry data
            if (empty($input['uuid'])) {
                $input['uuid'] = $this->generateUUID();
                $input['created_by'] = session('uuid');
            }

            $input['uuid_business_id'] = $this->businessUuid;
            $input['entry_date'] = $input['entry_date'] ?? date('Y-m-d');

            // Calculate totals from lines
            $lines = json_decode($input['lines_data'] ?? '[]', true);
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($lines as $line) {
                $totalDebit += floatval($line['debit_amount'] ?? 0);
                $totalCredit += floatval($line['credit_amount'] ?? 0);
            }

            $input['total_debit'] = $totalDebit;
            $input['total_credit'] = $totalCredit;
            $input['is_balanced'] = abs($totalDebit - $totalCredit) < 0.01 ? 1 : 0;

            // Save journal entry
            if (!empty($input['id'])) {
                $this->journal_model->where('uuid', $input['uuid'])->set($input)->update();
            } else {
                $this->journal_model->insert($input);
            }

            // Delete existing lines
            $db->table('journal_entry_lines')
                ->where('uuid_journal_entry_id', $input['uuid'])
                ->delete();

            // Insert new lines
            $lineNumber = 1;
            foreach ($lines as $line) {
                if (empty($line['uuid_account_id'])) continue;

                $lineData = [
                    'uuid' => $this->generateUUID(),
                    'uuid_journal_entry_id' => $input['uuid'],
                    'uuid_account_id' => $line['uuid_account_id'],
                    'line_number' => $lineNumber++,
                    'description' => $line['description'] ?? '',
                    'debit_amount' => floatval($line['debit_amount'] ?? 0),
                    'credit_amount' => floatval($line['credit_amount'] ?? 0)
                ];

                $db->table('journal_entry_lines')->insert($lineData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('/journal-entries')->with('success', 'Journal entry saved successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error saving journal entry: ' . $e->getMessage());
        }
    }

    public function post($uuid)
    {
        try {
            $entry = $this->journal_model->where('uuid', $uuid)->first();

            if (!$entry) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Journal entry not found'
                ]);
            }

            if (!$entry['is_balanced']) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Cannot post unbalanced entry. Debits must equal credits.'
                ]);
            }

            $this->journal_model->postEntry($uuid);

            // Update account balances
            $lines = $this->db->table('journal_entry_lines')
                ->where('uuid_journal_entry_id', $uuid)
                ->get()
                ->getResultArray();

            foreach ($lines as $line) {
                $this->accounts_model->updateAccountBalance($line['uuid_account_id']);
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Journal entry posted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error posting entry: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($uuid)
    {
        try {
            $entry = $this->journal_model->where('uuid', $uuid)->first();

            if ($entry['is_posted']) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Cannot delete posted journal entry'
                ]);
            }

            // Delete lines
            $this->db->table('journal_entry_lines')
                ->where('uuid_journal_entry_id', $uuid)
                ->delete();

            // Delete entry
            $this->journal_model->where('uuid', $uuid)->delete();

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Journal entry deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error deleting entry: ' . $e->getMessage()
            ]);
        }
    }

    public function journalEntriesList()
    {
        $db = \Config\Database::connect();

        $entries = $db->table('journal_entries je')
            ->select('je.*, u.name as created_by_name')
            ->join('users u', 'u.uuid = je.created_by', 'left')
            ->where('je.uuid_business_id', $this->businessUuid)
            ->orderBy('je.entry_date', 'DESC')
            ->orderBy('je.entry_number', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'data' => $entries
        ]);
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
