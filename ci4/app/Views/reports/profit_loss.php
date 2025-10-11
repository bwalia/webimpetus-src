<?php require_once(APPPATH . 'Views/common/header.php'); ?>
<?php require_once(APPPATH . 'Views/common/sidebar.php'); ?>

<section class="main_content dashboard_part large_header_bg">
    <?php require_once(APPPATH . 'Views/common/top-header.php'); ?>

    <div class="main_content_iner">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0"><i class="fa fa-chart-line"></i> Profit & Loss Statement</h3>
                                    <p class="mb-0 text-muted">Income Statement - Revenue vs Expenses</p>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <!-- Date Filter -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <form method="get" action="/profit-loss" class="form-inline">
                                        <label class="mr-2">Period:</label>
                                        <input type="date" name="start_date" class="form-control mr-2"
                                               value="<?= $start_date ?>" placeholder="Start Date" />
                                        <span class="mr-2">to</span>
                                        <input type="date" name="end_date" class="form-control mr-2"
                                               value="<?= $end_date ?>" placeholder="End Date" />
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> Generate
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button class="btn btn-success" onclick="exportToPDF()">
                                        <i class="fa fa-file-pdf"></i> Export PDF
                                    </button>
                                    <button class="btn btn-info" onclick="window.print()">
                                        <i class="fa fa-print"></i> Print
                                    </button>
                                    <a href="/accounts" class="btn btn-secondary">
                                        <i class="fa fa-cog"></i> Manage Accounts
                                    </a>
                                </div>
                            </div>

                            <div class="report-container" style="max-width: 900px; margin: 0 auto;">
                                <!-- Report Header -->
                                <div class="text-center mb-4">
                                    <h4 style="margin-bottom: 5px; color: #1e293b; font-weight: 700;">Profit & Loss Statement</h4>
                                    <p style="margin: 0; color: #64748b;">
                                        <?= date('F j, Y', strtotime($start_date)) ?> to <?= date('F j, Y', strtotime($end_date)) ?>
                                    </p>
                                </div>

                                <!-- REVENUE -->
                                <div class="mb-4">
                                    <h5 style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 10px 15px; margin: 0 0 10px 0; border-radius: 4px;">
                                        <i class="fa fa-arrow-up"></i> REVENUE
                                    </h5>
                                    <table class="table table-sm" style="margin-bottom: 0;">
                                        <tbody>
                                            <?php foreach ($report_data['revenue'] as $item): ?>
                                            <tr>
                                                <td style="padding-left: 30px; border: none;">
                                                    <span style="color: #64748b; font-size: 12px;"><?= $item['account_code'] ?></span>
                                                    <?= $item['account_name'] ?>
                                                </td>
                                                <td class="text-right" style="border: none; font-weight: 600;">
                                                    <?= number_format($item['amount'], 2) ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr style="border-top: 2px solid #e2e8f0; background: #f0fdf4;">
                                                <td style="padding-left: 15px; font-weight: 700; color: #1e293b;">
                                                    Total Revenue
                                                </td>
                                                <td class="text-right" style="font-weight: 700; color: #10b981; font-size: 16px;">
                                                    <?= number_format($report_data['total_revenue'], 2) ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- COST OF GOODS SOLD -->
                                <?php if (!empty($report_data['cogs'])): ?>
                                <div class="mb-4">
                                    <h5 style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 10px 15px; margin: 0 0 10px 0; border-radius: 4px;">
                                        <i class="fa fa-box"></i> COST OF GOODS SOLD
                                    </h5>
                                    <table class="table table-sm" style="margin-bottom: 0;">
                                        <tbody>
                                            <?php foreach ($report_data['cogs'] as $item): ?>
                                            <tr>
                                                <td style="padding-left: 30px; border: none;">
                                                    <span style="color: #64748b; font-size: 12px;"><?= $item['account_code'] ?></span>
                                                    <?= $item['account_name'] ?>
                                                </td>
                                                <td class="text-right" style="border: none; font-weight: 600;">
                                                    <?= number_format($item['amount'], 2) ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr style="border-top: 2px solid #e2e8f0; background: #fef3c7;">
                                                <td style="padding-left: 15px; font-weight: 700; color: #1e293b;">
                                                    Total COGS
                                                </td>
                                                <td class="text-right" style="font-weight: 700; color: #d97706;">
                                                    <?= number_format($report_data['total_cogs'], 2) ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- GROSS PROFIT -->
                                <div class="mb-4" style="background: #dbeafe; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6;">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h5 class="mb-0" style="color: #1e40af; font-weight: 700;">
                                                <i class="fa fa-chart-line"></i> GROSS PROFIT
                                            </h5>
                                            <small style="color: #64748b;">Revenue - COGS</small>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <h3 class="mb-0" style="color: #1e40af; font-weight: 700;">
                                                <?= number_format($report_data['gross_profit'], 2) ?>
                                            </h3>
                                            <small style="color: #64748b;">
                                                Margin: <?= number_format($report_data['gross_profit_margin'], 1) ?>%
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- OPERATING EXPENSES -->
                                <div class="mb-4">
                                    <h5 style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 10px 15px; margin: 0 0 10px 0; border-radius: 4px;">
                                        <i class="fa fa-arrow-down"></i> OPERATING EXPENSES
                                    </h5>
                                    <table class="table table-sm" style="margin-bottom: 0;">
                                        <tbody>
                                            <?php foreach ($report_data['operating_expenses'] as $item): ?>
                                            <tr>
                                                <td style="padding-left: 30px; border: none;">
                                                    <span style="color: #64748b; font-size: 12px;"><?= $item['account_code'] ?></span>
                                                    <?= $item['account_name'] ?>
                                                </td>
                                                <td class="text-right" style="border: none; font-weight: 600;">
                                                    <?= number_format($item['amount'], 2) ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr style="border-top: 2px solid #e2e8f0; background: #fee2e2;">
                                                <td style="padding-left: 15px; font-weight: 700; color: #1e293b;">
                                                    Total Operating Expenses
                                                </td>
                                                <td class="text-right" style="font-weight: 700; color: #dc2626;">
                                                    <?= number_format($report_data['total_operating_expenses'], 2) ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- NET INCOME -->
                                <div class="mb-4" style="background: <?= $report_data['net_income'] >= 0 ? '#d1fae5' : '#fee2e2' ?>; padding: 20px; border-radius: 8px; border: 3px solid <?= $report_data['net_income'] >= 0 ? '#10b981' : '#dc2626' ?>;">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h4 class="mb-0" style="color: #1e293b; font-weight: 700;">
                                                <i class="fa fa-<?= $report_data['net_income'] >= 0 ? 'check-circle' : 'exclamation-circle' ?>"></i>
                                                NET <?= $report_data['net_income'] >= 0 ? 'INCOME' : 'LOSS' ?>
                                            </h4>
                                            <small style="color: #64748b;">Revenue - Total Expenses</small>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <h2 class="mb-0" style="color: <?= $report_data['net_income'] >= 0 ? '#10b981' : '#dc2626' ?>; font-weight: 700; font-size: 32px;">
                                                <?= number_format($report_data['net_income'], 2) ?>
                                            </h2>
                                            <small style="color: #64748b; font-weight: 600;">
                                                Margin: <?= number_format($report_data['net_profit_margin'], 1) ?>%
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Summary Cards -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; height: 100%;">
                                            <div class="card-body">
                                                <h6 style="opacity: 0.8; font-size: 11px; margin-bottom: 8px;">TOTAL REVENUE</h6>
                                                <h3 style="margin: 0; font-weight: 700;"><?= number_format($report_data['total_revenue'], 2) ?></h3>
                                                <small style="opacity: 0.8;">Income earned during period</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; height: 100%;">
                                            <div class="card-body">
                                                <h6 style="opacity: 0.8; font-size: 11px; margin-bottom: 8px;">TOTAL EXPENSES</h6>
                                                <h3 style="margin: 0; font-weight: 700;"><?= number_format($report_data['total_cogs'] + $report_data['total_operating_expenses'], 2) ?></h3>
                                                <small style="opacity: 0.8;">Costs incurred during period</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="text-center text-muted" style="font-size: 12px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                                    Generated on <?= date('F j, Y \a\t g:i A') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
function exportToPDF() {
    Swal.fire({
        icon: 'info',
        title: 'PDF Export',
        text: 'PDF export feature will be implemented soon. For now, please use the Print button.',
        confirmButtonColor: '#667eea'
    });
}
</script>

<style>
@media print {
    .white_card_header, .row.mb-4, .main_header, .sidebar, button { display: none !important; }
    .white_card { box-shadow: none; border: none; }
}
</style>
