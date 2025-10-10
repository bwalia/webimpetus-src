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
                                    <h3 class="m-0"><i class="fa fa-balance-scale"></i> Balance Sheet</h3>
                                    <p class="mb-0 text-muted">Assets = Liabilities + Equity</p>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <!-- Date Filter -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <form method="get" action="/balance-sheet" class="form-inline">
                                        <label class="mr-2">As of Date:</label>
                                        <input type="date" name="as_of_date" class="form-control mr-2"
                                               value="<?= $as_of_date ?>" />
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> Generate
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-8 text-right">
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
                                <!-- Company Header -->
                                <div class="text-center mb-4">
                                    <h4 style="margin-bottom: 5px; color: #1e293b; font-weight: 700;">Balance Sheet</h4>
                                    <p style="margin: 0; color: #64748b;">As of <?= date('F j, Y', strtotime($as_of_date)) ?></p>
                                </div>

                                <!-- ASSETS -->
                                <div class="mb-4">
                                    <h5 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 15px; margin: 0 0 10px 0; border-radius: 4px;">
                                        <i class="fa fa-plus-circle"></i> ASSETS
                                    </h5>
                                    <table class="table table-sm" style="margin-bottom: 0;">
                                        <tbody>
                                            <?php foreach ($report_data['assets'] as $asset): ?>
                                                <?php if (abs($asset['balance']) > 0.01): ?>
                                                <tr>
                                                    <td style="padding-left: 30px; border: none;">
                                                        <span style="color: #64748b; font-size: 12px;"><?= $asset['account_code'] ?></span>
                                                        <?= $asset['account_name'] ?>
                                                    </td>
                                                    <td class="text-right" style="border: none; font-weight: 600;">
                                                        <?= number_format($asset['balance'], 2) ?>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <tr style="border-top: 2px solid #e2e8f0;">
                                                <td style="padding-left: 15px; font-weight: 700; color: #1e293b;">
                                                    Total Assets
                                                </td>
                                                <td class="text-right" style="font-weight: 700; color: #1e293b; font-size: 16px;">
                                                    <?= number_format($report_data['total_assets'], 2) ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- LIABILITIES -->
                                <div class="mb-4">
                                    <h5 style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 10px 15px; margin: 0 0 10px 0; border-radius: 4px;">
                                        <i class="fa fa-minus-circle"></i> LIABILITIES
                                    </h5>
                                    <table class="table table-sm" style="margin-bottom: 0;">
                                        <tbody>
                                            <?php foreach ($report_data['liabilities'] as $liability): ?>
                                                <?php if (abs($liability['balance']) > 0.01): ?>
                                                <tr>
                                                    <td style="padding-left: 30px; border: none;">
                                                        <span style="color: #64748b; font-size: 12px;"><?= $liability['account_code'] ?></span>
                                                        <?= $liability['account_name'] ?>
                                                    </td>
                                                    <td class="text-right" style="border: none; font-weight: 600;">
                                                        <?= number_format($liability['balance'], 2) ?>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <tr style="border-top: 2px solid #e2e8f0;">
                                                <td style="padding-left: 15px; font-weight: 700; color: #1e293b;">
                                                    Total Liabilities
                                                </td>
                                                <td class="text-right" style="font-weight: 700; color: #1e293b;">
                                                    <?= number_format($report_data['total_liabilities'], 2) ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- EQUITY -->
                                <div class="mb-4">
                                    <h5 style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 10px 15px; margin: 0 0 10px 0; border-radius: 4px;">
                                        <i class="fa fa-equals"></i> EQUITY
                                    </h5>
                                    <table class="table table-sm" style="margin-bottom: 0;">
                                        <tbody>
                                            <?php foreach ($report_data['equity'] as $equity): ?>
                                                <?php if (abs($equity['balance']) > 0.01): ?>
                                                <tr>
                                                    <td style="padding-left: 30px; border: none;">
                                                        <span style="color: #64748b; font-size: 12px;"><?= $equity['account_code'] ?></span>
                                                        <?= $equity['account_name'] ?>
                                                    </td>
                                                    <td class="text-right" style="border: none; font-weight: 600;">
                                                        <?= number_format($equity['balance'], 2) ?>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php if (abs($report_data['net_income']) > 0.01): ?>
                                            <tr>
                                                <td style="padding-left: 30px; border: none; font-style: italic; color: #10b981;">
                                                    <span style="color: #64748b; font-size: 12px;">----</span>
                                                    Net Income (Current Period)
                                                </td>
                                                <td class="text-right" style="border: none; font-weight: 600; color: #10b981;">
                                                    <?= number_format($report_data['net_income'], 2) ?>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr style="border-top: 2px solid #e2e8f0;">
                                                <td style="padding-left: 15px; font-weight: 700; color: #1e293b;">
                                                    Total Equity
                                                </td>
                                                <td class="text-right" style="font-weight: 700; color: #1e293b;">
                                                    <?= number_format($report_data['total_equity'], 2) ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- TOTALS -->
                                <div class="mb-4" style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 2px solid #e2e8f0;">
                                    <table class="table table-sm mb-0" style="margin: 0;">
                                        <tbody>
                                            <tr>
                                                <td style="border: none; font-weight: 700; font-size: 16px; color: #1e293b;">
                                                    Total Liabilities & Equity
                                                </td>
                                                <td class="text-right" style="border: none; font-weight: 700; font-size: 18px; color: #1e293b;">
                                                    <?= number_format($report_data['total_liabilities_equity'], 2) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border: none; padding-top: 10px;">
                                                    <?php
                                                    $difference = abs($report_data['total_assets'] - $report_data['total_liabilities_equity']);
                                                    $isBalanced = $difference < 0.01;
                                                    ?>
                                                    <?php if ($isBalanced): ?>
                                                        <div class="alert alert-success mb-0" style="padding: 8px 12px;">
                                                            <i class="fa fa-check-circle"></i> <strong>Balanced</strong> - Assets equal Liabilities + Equity
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="alert alert-danger mb-0" style="padding: 8px 12px;">
                                                            <i class="fa fa-exclamation-triangle"></i> <strong>Out of Balance</strong> - Difference: <?= number_format($difference, 2) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
