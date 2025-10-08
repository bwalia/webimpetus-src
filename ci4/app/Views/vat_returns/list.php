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
                            <h3 class="f_s_25 f_w_700 dark_text mr_30">UK VAT Returns</h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                <li class="breadcrumb-item active"><a href="/vat_returns">VAT Returns</a></li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                            <div class="header_more_tool setDropDownBlk">
                                <a href="javascript:void(0)" onClick="window.location.href='/vat_returns';" class="btn btn-primary">
                                    <i class="ti-reload"></i> Refresh
                                </a>
                                <a href="/vat_returns/generate" class="btn btn-success">
                                    <i class="ti-plus"></i> Generate VAT Return
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <?php if(session()->has('message')){ ?>
                        <div class="alert <?= session()->getFlashdata('alert-class') ?>">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php } ?>

                    <div class="white_card card_height_100 mb_20">
                        <div class="white_card_body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Quarter</th>
                                            <th>Year</th>
                                            <th>Period</th>
                                            <th>UK VAT</th>
                                            <th>Non-UK VAT</th>
                                            <th>Total VAT Due</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($vat_returns)): ?>
                                            <?php foreach ($vat_returns as $return): ?>
                                                <tr>
                                                    <td>Q<?= $return['quarter'] ?></td>
                                                    <td><?= $return['year'] ?></td>
                                                    <td>
                                                        <?= date('d/m/Y', strtotime($return['period_start'])) ?> -
                                                        <?= date('d/m/Y', strtotime($return['period_end'])) ?>
                                                    </td>
                                                    <td>£<?= number_format($return['uk_vat_total'], 2) ?></td>
                                                    <td>£<?= number_format($return['non_uk_vat_total'], 2) ?></td>
                                                    <td><strong>£<?= number_format($return['total_vat_due'], 2) ?></strong></td>
                                                    <td>
                                                        <?php if ($return['status'] === 'submitted'): ?>
                                                            <span class="badge badge-success">Submitted</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-warning">Draft</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="/vat_returns/view/<?= $return['uuid'] ?>" class="btn btn-sm btn-info" title="View">
                                                                <i class="ti-eye"></i>
                                                            </a>
                                                            <a href="/vat_returns/export/<?= $return['uuid'] ?>" class="btn btn-sm btn-primary" title="Export CSV">
                                                                <i class="ti-download"></i>
                                                            </a>
                                                            <?php if ($return['status'] !== 'submitted'): ?>
                                                                <a href="/vat_returns/submit/<?= $return['uuid'] ?>"
                                                                   class="btn btn-sm btn-success"
                                                                   title="Submit"
                                                                   onclick="return confirm('Are you sure you want to submit this VAT return?');">
                                                                    <i class="ti-check"></i>
                                                                </a>
                                                                <a href="/vat_returns/delete/<?= $return['uuid'] ?>"
                                                                   class="btn btn-sm btn-danger"
                                                                   title="Delete"
                                                                   onclick="return confirm('Are you sure you want to delete this VAT return?');">
                                                                    <i class="ti-trash"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center">
                                                    <p class="mt-3 mb-3">No VAT returns found. <a href="/vat_returns/generate">Generate your first VAT return</a></p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
