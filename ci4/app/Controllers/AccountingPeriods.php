<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\AccountingPeriods_model;

class AccountingPeriods extends CommonController
{
    protected $periods_model;

    public function __construct()
    {
        parent::__construct();
        $this->periods_model = new AccountingPeriods_model();
    }

    public function index()
    {
        $this->data['page_title'] = "Accounting Periods";
        $this->data['tableName'] = "accounting_periods";
        $this->data['current_period'] = $this->periods_model->getCurrentPeriod($this->businessUuid);

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('accounting_periods/list', $this->data);
    }

    public function edit($uuid = null)
    {
        $this->data['page_title'] = $uuid ? "Edit Accounting Period" : "New Accounting Period";
        $this->data['tableName'] = "accounting_periods";

        if ($uuid) {
            $period = $this->periods_model->where('uuid', $uuid)->first();

            if (!$period) {
                return redirect()->to('/accounting-periods')->with('error', 'Period not found');
            }

            $this->data['period'] = (object) $period;
        } else {
            $this->data['period'] = (object) [];
        }

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('accounting_periods/edit', $this->data);
    }

    public function update()
    {
        $input = $this->request->getPost();

        if (empty($input['uuid'])) {
            $input['uuid'] = $this->generateUUID();
        }

        $input['uuid_business_id'] = $this->businessUuid;
        $input['is_current'] = $this->request->getPost('is_current') ? 1 : 0;
        $input['is_closed'] = $this->request->getPost('is_closed') ? 1 : 0;

        // If setting as current, unset other current periods
        if ($input['is_current']) {
            $this->periods_model
                ->where('uuid_business_id', $this->businessUuid)
                ->set('is_current', 0)
                ->update();
        }

        try {
            if (!empty($input['id'])) {
                $this->periods_model->where('uuid', $input['uuid'])->set($input)->update();
                $message = 'Period updated successfully';
            } else {
                $this->periods_model->insert($input);
                $message = 'Period created successfully';
            }

            return redirect()->to('/accounting-periods')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error saving period: ' . $e->getMessage());
        }
    }

    public function setCurrent($uuid)
    {
        try {
            $this->periods_model->setCurrentPeriod($uuid, $this->businessUuid);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Current period updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function closePeriod($uuid)
    {
        try {
            $period = $this->periods_model->where('uuid', $uuid)->first();

            if ($period['is_closed']) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Period is already closed'
                ]);
            }

            $this->periods_model->closePeriod($uuid, session('uuid'));

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Period closed successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function periodsList()
    {
        $periods = $this->periods_model
            ->where('uuid_business_id', $this->businessUuid)
            ->orderBy('start_date', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'data' => $periods
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
