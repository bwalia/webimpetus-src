<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;
use App\Models\Core\Common_model;
use App\Models\Vat_return_model;
use stdClass;

class Vat_returns extends CommonController
{
    private $vat_model;

    function __construct()
    {
        parent::__construct();

        $this->vat_model = new Vat_return_model();
        $this->model = new Common_model();
    }

    /**
     * List all VAT returns
     */
    public function index()
    {
        $data[$this->table] = $this->vat_model->getRows();
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;

        echo view($this->table . "/list", $data);
    }

    /**
     * Generate new VAT return form
     */
    public function generate()
    {
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['current_year'] = date('Y');
        $data['current_quarter'] = ceil(date('n') / 3);

        echo view($this->table . "/generate", $data);
    }

    /**
     * Preview VAT return calculations before saving
     */
    public function preview()
    {
        $year = $this->request->getPost('year') ?: $this->request->getGet('year');
        $quarter = $this->request->getPost('quarter') ?: $this->request->getGet('quarter');

        if (!$year || !$quarter) {
            session()->setFlashdata('message', 'Year and Quarter are required!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to($this->table . "/generate");
        }

        $businessUuid = session('uuid_business');

        // Check if return already exists
        $existing = $this->vat_model->returnExists($year, $quarter, $businessUuid);

        // Generate calculations
        $vatData = $this->vat_model->generateQuarterlyReturn($year, $quarter, $businessUuid);

        if (!$vatData) {
            session()->setFlashdata('message', 'Invalid quarter selected!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to($this->table . "/generate");
        }

        // Get invoice breakdowns
        $dates = $this->vat_model->getQuarterDates($year, $quarter);
        $vatData['uk_invoices'] = $this->vat_model->getInvoiceBreakdown($dates['start'], $dates['end'], $businessUuid, true);
        $vatData['non_uk_invoices'] = $this->vat_model->getInvoiceBreakdown($dates['start'], $dates['end'], $businessUuid, false);

        $data['vat_data'] = $vatData;
        $data['existing'] = $existing;
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;

        echo view($this->table . "/preview", $data);
    }

    /**
     * Save VAT return
     */
    public function save()
    {
        $year = $this->request->getPost('year');
        $quarter = $this->request->getPost('quarter');
        $businessUuid = session('uuid_business');

        if (!$year || !$quarter) {
            session()->setFlashdata('message', 'Year and Quarter are required!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to($this->table . "/generate");
        }

        // Check if return already exists
        $existing = $this->vat_model->returnExists($year, $quarter, $businessUuid);

        if ($existing) {
            session()->setFlashdata('message', 'VAT return for Q' . $quarter . ' ' . $year . ' already exists!');
            session()->setFlashdata('alert-class', 'alert-warning');
            return redirect()->to($this->table . "/view/" . $existing['uuid']);
        }

        // Generate calculations
        $vatData = $this->vat_model->generateQuarterlyReturn($year, $quarter, $businessUuid);

        if (!$vatData) {
            session()->setFlashdata('message', 'Invalid quarter selected!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to($this->table . "/generate");
        }

        // Prepare data for insertion
        $uuid = UUID::v5(UUID::v4(), 'vat_returns');
        $insertData = [
            'uuid' => $uuid,
            'uuid_business_id' => $businessUuid,
            'quarter' => $quarter,
            'year' => $year,
            'period_start' => $vatData['period_start'],
            'period_end' => $vatData['period_end'],
            'uk_vat_total' => $vatData['uk_vat_total'],
            'uk_sales_total' => $vatData['uk_sales_total'],
            'non_uk_vat_total' => $vatData['non_uk_vat_total'],
            'non_uk_sales_total' => $vatData['non_uk_sales_total'],
            'total_vat_due' => $vatData['total_vat_due'],
            'status' => 'draft'
        ];

        $this->vat_model->insert($insertData);

        session()->setFlashdata('message', 'VAT return created successfully!');
        session()->setFlashdata('alert-class', 'alert-success');
        return redirect()->to($this->table . "/view/" . $uuid);
    }

    /**
     * View VAT return details
     */
    public function view($uuid = '')
    {
        if (empty($uuid)) {
            return redirect()->to($this->table);
        }

        $vatReturn = $this->vat_model->getByUUID($uuid);

        if (!$vatReturn) {
            session()->setFlashdata('message', 'VAT return not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to($this->table);
        }

        // Get invoice breakdowns
        $businessUuid = session('uuid_business');
        $vatReturn['uk_invoices'] = $this->vat_model->getInvoiceBreakdown(
            $vatReturn['period_start'],
            $vatReturn['period_end'],
            $businessUuid,
            true
        );
        $vatReturn['non_uk_invoices'] = $this->vat_model->getInvoiceBreakdown(
            $vatReturn['period_start'],
            $vatReturn['period_end'],
            $businessUuid,
            false
        );

        $data['vat_return'] = $vatReturn;
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;

        echo view($this->table . "/view", $data);
    }

    /**
     * Submit VAT return
     */
    public function submit($uuid = '')
    {
        if (empty($uuid)) {
            return redirect()->to($this->table);
        }

        $vatReturn = $this->vat_model->getByUUID($uuid);

        if (!$vatReturn) {
            session()->setFlashdata('message', 'VAT return not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to($this->table);
        }

        if ($vatReturn['status'] === 'submitted') {
            session()->setFlashdata('message', 'This VAT return has already been submitted!');
            session()->setFlashdata('alert-class', 'alert-warning');
            return redirect()->to($this->table . "/view/" . $uuid);
        }

        // Update status to submitted
        $this->vat_model->update($vatReturn['id'], [
            'status' => 'submitted',
            'submitted_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('message', 'VAT return submitted successfully!');
        session()->setFlashdata('alert-class', 'alert-success');
        return redirect()->to($this->table . "/view/" . $uuid);
    }

    /**
     * Delete VAT return
     */
    public function delete($uuid = '')
    {
        if (empty($uuid)) {
            return redirect()->to($this->table);
        }

        $vatReturn = $this->vat_model->getByUUID($uuid);

        if (!$vatReturn) {
            session()->setFlashdata('message', 'VAT return not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to($this->table);
        }

        if ($vatReturn['status'] === 'submitted') {
            session()->setFlashdata('message', 'Cannot delete submitted VAT return!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to($this->table);
        }

        $this->vat_model->delete($vatReturn['id']);

        session()->setFlashdata('message', 'VAT return deleted successfully!');
        session()->setFlashdata('alert-class', 'alert-success');
        return redirect()->to($this->table);
    }

    /**
     * Export VAT return to CSV
     */
    public function export($uuid = '')
    {
        if (empty($uuid)) {
            return redirect()->to($this->table);
        }

        $vatReturn = $this->vat_model->getByUUID($uuid);

        if (!$vatReturn) {
            session()->setFlashdata('message', 'VAT return not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to($this->table);
        }

        // Get invoice breakdowns
        $businessUuid = session('uuid_business');
        $ukInvoices = $this->vat_model->getInvoiceBreakdown(
            $vatReturn['period_start'],
            $vatReturn['period_end'],
            $businessUuid,
            true
        );
        $nonUkInvoices = $this->vat_model->getInvoiceBreakdown(
            $vatReturn['period_start'],
            $vatReturn['period_end'],
            $businessUuid,
            false
        );

        // Set CSV headers
        $filename = 'VAT_Return_Q' . $vatReturn['quarter'] . '_' . $vatReturn['year'] . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Write summary
        fputcsv($output, ['UK VAT Return Summary']);
        fputcsv($output, ['Quarter', 'Q' . $vatReturn['quarter'] . ' ' . $vatReturn['year']]);
        fputcsv($output, ['Period', date('d/m/Y', strtotime($vatReturn['period_start'])) . ' - ' . date('d/m/Y', strtotime($vatReturn['period_end']))]);
        fputcsv($output, ['Status', ucfirst($vatReturn['status'])]);
        fputcsv($output, []);
        fputcsv($output, ['Category', 'Sales Total', 'VAT Total']);
        fputcsv($output, ['UK Sales', '£' . number_format($vatReturn['uk_sales_total'], 2), '£' . number_format($vatReturn['uk_vat_total'], 2)]);
        fputcsv($output, ['Non-UK Sales', '£' . number_format($vatReturn['non_uk_sales_total'], 2), '£' . number_format($vatReturn['non_uk_vat_total'], 2)]);
        fputcsv($output, ['Total VAT Due', '', '£' . number_format($vatReturn['total_vat_due'], 2)]);
        fputcsv($output, []);
        fputcsv($output, []);

        // Write UK invoices
        fputcsv($output, ['UK Invoices']);
        fputcsv($output, ['Invoice Number', 'Date', 'Customer', 'Net Amount', 'VAT', 'Total', 'VAT Rate']);
        foreach ($ukInvoices as $invoice) {
            fputcsv($output, [
                $invoice['custom_invoice_number'] ?: $invoice['invoice_number'],
                date('d/m/Y', strtotime($invoice['created_at'])),
                $invoice['company_name'],
                '£' . number_format($invoice['total'], 2),
                '£' . number_format($invoice['total_tax'], 2),
                '£' . number_format($invoice['total_due_with_tax'], 2),
                $invoice['invoice_tax_rate'] . '%'
            ]);
        }

        fputcsv($output, []);
        fputcsv($output, []);

        // Write non-UK invoices
        fputcsv($output, ['Non-UK Invoices']);
        fputcsv($output, ['Invoice Number', 'Date', 'Customer', 'Country', 'Net Amount', 'VAT', 'Total']);
        foreach ($nonUkInvoices as $invoice) {
            fputcsv($output, [
                $invoice['custom_invoice_number'] ?: $invoice['invoice_number'],
                date('d/m/Y', strtotime($invoice['created_at'])),
                $invoice['company_name'],
                $invoice['country'],
                '£' . number_format($invoice['total'], 2),
                '£' . number_format($invoice['total_tax'], 2),
                '£' . number_format($invoice['total_due_with_tax'], 2)
            ]);
        }

        fclose($output);
        exit;
    }
}
