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
                                    <h3 class="m-0"><i class="fa fa-balance-scale"></i> Trial Balance</h3>
                                    <p class="mb-0 text-muted">Verify that total debits equal total credits</p>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <!-- Date Filter -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <form method="get" action="/trial-balance" class="form-inline">
                                        <label class="mr-2">As of Date:</label>
                                        <input type="date" name="as_of_date" class="form-control mr-2"
                                               value="<?= $as_of_date ?>" />
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> Generate
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-8 text-right">
                                    <button class="btn btn-success" onclick="exportToExcel()">
                                        <i class="fa fa-file-excel"></i> Export Excel
                                    </button>
                                    <button class="btn btn-info" onclick="window.print()">
                                        <i class="fa fa-print"></i> Print
                                    </button>
                                    <a href="/accounts" class="btn btn-secondary">
                                        <i class="fa fa-cog"></i> Manage Accounts
                                    </a>
                                </div>
                            </div>

                            <div class="report-container" style="max-width: 1000px; margin: 0 auto;">
                                <!-- Report Header -->
                                <div class="text-center mb-4">
                                    <h4 style="margin-bottom: 5px; color: #1e293b; font-weight: 700;">Trial Balance</h4>
                                    <p style="margin: 0; color: #64748b;">As of <?= date('F j, Y', strtotime($as_of_date)) ?></p>
                                </div>

                                <!-- Balance Status -->
                                <?php if ($report_data['is_balanced']): ?>
                                    <div class="alert alert-success mb-4">
                                        <i class="fa fa-check-circle"></i> <strong>Balanced</strong> - Total Debits equal Total Credits
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-danger mb-4">
                                        <i class="fa fa-exclamation-triangle"></i> <strong>Out of Balance</strong> -
                                        Difference: <?= number_format(abs($report_data['total_debit'] - $report_data['total_credit']), 2) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Trial Balance Table -->
                                <table class="table table-hover" style="background: white;">
                                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                        <tr>
                                            <th style="border: none;">Account Code</th>
                                            <th style="border: none;">Account Name</th>
                                            <th class="text-right" style="border: none;">Debit</th>
                                            <th class="text-right" style="border: none;">Credit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($report_data['accounts'] as $account): ?>
                                        <tr>
                                            <td style="font-family: monospace; color: #667eea; font-weight: 600;">
                                                <?= $account['account_code'] ?>
                                            </td>
                                            <td style="color: #334155; font-weight: 500;">
                                                <?= $account['account_name'] ?>
                                                <br>
                                                <small style="color: #94a3b8; font-size: 11px;">
                                                    <?= $account['account_type'] ?>
                                                </small>
                                            </td>
                                            <td class="text-right" style="font-weight: 600; color: #0ea5e9;">
                                                <?php if ($account['debit'] > 0): ?>
                                                    <?= number_format($account['debit'], 2) ?>
                                                <?php else: ?>
                                                    <span style="color: #cbd5e1;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right" style="font-weight: 600; color: #8b5cf6;">
                                                <?php if ($account['credit'] > 0): ?>
                                                    <?= number_format($account['credit'], 2) ?>
                                                <?php else: ?>
                                                    <span style="color: #cbd5e1;">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot style="border-top: 3px solid #334155; background: #f8fafc;">
                                        <tr>
                                            <td colspan="2" style="font-weight: 700; font-size: 16px; color: #1e293b; padding: 15px;">
                                                TOTAL
                                            </td>
                                            <td class="text-right" style="font-weight: 700; font-size: 18px; color: #0ea5e9; padding: 15px;">
                                                <?= number_format($report_data['total_debit'], 2) ?>
                                            </td>
                                            <td class="text-right" style="font-weight: 700; font-size: 18px; color: #8b5cf6; padding: 15px;">
                                                <?= number_format($report_data['total_credit'], 2) ?>
                                            </td>
                                        </tr>
                                        <tr style="background: white;">
                                            <td colspan="2" style="font-weight: 600; color: #64748b; padding: 10px;">
                                                DIFFERENCE
                                            </td>
                                            <td colspan="2" class="text-right" style="font-weight: 700; padding: 10px; color: <?= $report_data['is_balanced'] ? '#10b981' : '#dc2626' ?>;">
                                                <?= number_format(abs($report_data['total_debit'] - $report_data['total_credit']), 2) ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <!-- Summary Stats -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                                            <div class="card-body text-center">
                                                <h6 style="opacity: 0.8; font-size: 12px; margin-bottom: 10px;">TOTAL ACCOUNTS</h6>
                                                <h2 style="margin: 0; font-weight: 700;"><?= count($report_data['accounts']) ?></h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card" style="background: linear-gradient(135deg, <?= $report_data['is_balanced'] ? '#10b981, #059669' : '#dc2626, #991b1b' ?>); color: white; border: none;">
                                            <div class="card-body text-center">
                                                <h6 style="opacity: 0.8; font-size: 12px; margin-bottom: 10px;">STATUS</h6>
                                                <h2 style="margin: 0; font-weight: 700;">
                                                    <?= $report_data['is_balanced'] ? 'BALANCED' : 'OUT OF BALANCE' ?>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="text-center text-muted" style="font-size: 12px; padding-top: 30px; border-top: 1px solid #e2e8f0; margin-top: 30px;">
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
function exportToExcel() {
    Swal.fire({
        icon: 'info',
        title: 'Excel Export',
        text: 'Excel export feature will be implemented soon.',
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
