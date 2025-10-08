<?php require_once(APPPATH . 'Views/common/header.php'); ?>
<?php require_once (APPPATH.'Views/common/sidebar.php'); ?>
<section class="main_content dashboard_part large_header_bg full_main_content">
    <?php require_once (APPPATH.'Views/common/top-header.php'); ?>
    <div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">
            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                        <div class="page_title_left d-flex align-items-center">
                            <h3 class="f_s_25 f_w_700 dark_text mr_30">VAT Return Preview - Q<?= $vat_data['quarter'] ?> <?= $vat_data['year'] ?></h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="/vat_returns">VAT Returns</a></li>
                                <li class="breadcrumb-item active">Preview</li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                            <div class="header_more_tool setDropDownBlk">
                                <a href="/vat_returns/generate" class="btn btn-secondary">
                                    <i class="ti-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($existing): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="ti-alert"></i> <strong>Warning:</strong> A VAT return for this quarter already exists.
                            <a href="/vat_returns/view/<?= $existing['uuid'] ?>" class="btn btn-sm btn-warning ml-2">View Existing Return</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="white_card card_height_100 mb_20">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">VAT Summary</h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Period:</strong> <?= date('d/m/Y', strtotime($vat_data['period_start'])) ?> - <?= date('d/m/Y', strtotime($vat_data['period_end'])) ?></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title">UK Sales</h5>
                                            <p class="mb-1"><strong>Sales Total:</strong> £<?= number_format($vat_data['uk_sales_total'], 2) ?></p>
                                            <p class="mb-1"><strong>VAT Total:</strong> £<?= number_format($vat_data['uk_vat_total'], 2) ?></p>
                                            <p class="mb-0"><small><?= $vat_data['uk_invoice_count'] ?> invoice(s)</small></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title">Non-UK Sales</h5>
                                            <p class="mb-1"><strong>Sales Total:</strong> £<?= number_format($vat_data['non_uk_sales_total'], 2) ?></p>
                                            <p class="mb-1"><strong>VAT Total:</strong> £<?= number_format($vat_data['non_uk_vat_total'], 2) ?></p>
                                            <p class="mb-0"><small><?= $vat_data['non_uk_invoice_count'] ?> invoice(s)</small></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h5 class="card-title text-white">Total VAT Due</h5>
                                            <h2 class="text-white">£<?= number_format($vat_data['total_vat_due'], 2) ?></h2>
                                            <p class="mb-0"><small><?= $vat_data['uk_invoice_count'] + $vat_data['non_uk_invoice_count'] ?> total invoice(s)</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (!$existing): ?>
                                <div class="row mt-4">
                                    <div class="col-12 text-right">
                                        <form action="/vat_returns/save" method="POST" style="display: inline;">
                                            <input type="hidden" name="year" value="<?= $vat_data['year'] ?>">
                                            <input type="hidden" name="quarter" value="<?= $vat_data['quarter'] ?>">
                                            <button type="submit" class="btn btn-success">
                                                <i class="ti-save"></i> Save VAT Return
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- UK Invoices -->
            <?php if (!empty($vat_data['uk_invoices'])): ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="white_card card_height_100 mb_20">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">UK Invoices (<?= count($vat_data['uk_invoices']) ?>)</h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Net Amount</th>
                                            <th>VAT</th>
                                            <th>Total</th>
                                            <th>VAT Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($vat_data['uk_invoices'] as $invoice): ?>
                                            <tr>
                                                <td><?= $invoice['custom_invoice_number'] ?: $invoice['invoice_number'] ?></td>
                                                <td><?= date('d/m/Y', strtotime($invoice['created_at'])) ?></td>
                                                <td><?= $invoice['company_name'] ?></td>
                                                <td>£<?= number_format($invoice['total'], 2) ?></td>
                                                <td>£<?= number_format($invoice['total_tax'], 2) ?></td>
                                                <td>£<?= number_format($invoice['total_due_with_tax'], 2) ?></td>
                                                <td><?= $invoice['invoice_tax_rate'] ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Non-UK Invoices -->
            <?php if (!empty($vat_data['non_uk_invoices'])): ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="white_card card_height_100 mb_20">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Non-UK Invoices (<?= count($vat_data['non_uk_invoices']) ?>)</h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Country</th>
                                            <th>Net Amount</th>
                                            <th>VAT</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($vat_data['non_uk_invoices'] as $invoice): ?>
                                            <tr>
                                                <td><?= $invoice['custom_invoice_number'] ?: $invoice['invoice_number'] ?></td>
                                                <td><?= date('d/m/Y', strtotime($invoice['created_at'])) ?></td>
                                                <td><?= $invoice['company_name'] ?></td>
                                                <td><?= $invoice['country'] ?></td>
                                                <td>£<?= number_format($invoice['total'], 2) ?></td>
                                                <td>£<?= number_format($invoice['total_tax'], 2) ?></td>
                                                <td>£<?= number_format($invoice['total_due_with_tax'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
