<?php require_once(APPPATH . 'Views/common/header.php'); ?>
<!-- main content part here -->

<?php require_once(APPPATH . 'Views/common/sidebar.php'); ?>
<?php $session = \Config\Services::session(); ?>
<section class="main_content dashboard_part large_header_bg">
    <?php require_once(APPPATH . 'Views/common/top-header.php'); ?>
    <div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">
            <!-- page title  -->
            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                        <div class="page_title_left d-flex align-items-center">
                            <h3 class="f_s_25 f_w_700 dark_text mr_30">
                                <?php
                                if (isset($menuName)) {
                                    echo ucfirst($menuName);
                                } else {
                                    echo render_head_text($tableName);
                                } ?>
                            </h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                <li class="breadcrumb-item active"><a
                                        href="/<?php echo strtolower($tableName); ?>"><?php if (isset($menuName)) {
                                               echo ucfirst($menuName);
                                           } else {
                                               echo render_head_text($tableName);
                                           } ?> </a></li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                            <?php if ($tableName == "tasks") { ?>
                                <a target="" href="<?= "/" . $tableName . "/clone/" . @$task->uuid; ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-copy mr-1"></i>
                                    <?php echo lang('Common.clone'); ?>
                                </a>
                            <?php } ?>
                            <?php if ($tableName == "sales_invoices") { ?>
                                <a target="" href="<?= "/" . $tableName . "/clone/" . @$sales_invoice->uuid; ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-copy mr-1"></i>
                                    <?php echo lang('Common.clone'); ?>
                                </a>
                            <?php } ?>
                            <?php if ($tableName == "sales_invoices") { ?>
                                <a target="_blank"
                                    href="<?= "/" . $tableName . "/exportPDF/" . @$sales_invoice->uuid . "/view?" . rand(0, 999999); ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-eye mr-1"></i>
                                    <?php echo lang('Common.view'); ?>
                                </a>
                                <a href="<?= "/" . $tableName . "/exportPDF/" . @$sales_invoice->uuid . "?" . rand(0, 999999); ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-print mr-1"></i>
                                    <?php echo lang('Common.print_pdf'); ?>
                                </a>
                            <?php } ?>
                            <?php if ($tableName == "purchase_invoices") { ?>
                                <a target="" href="<?= "/" . $tableName . "/clone/" . @$purchase_invoice->uuid; ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-copy mr-1"></i>
                                    <?php echo lang('Common.clone'); ?>
                                </a>
                            <?php } ?>
                            <?php if ($tableName == "purchase_invoices") { ?>
                                <a target="_blank"
                                    href="<?= "/" . $tableName . "/exportPDF/" . @$purchase_invoice->uuid . "/view?" . rand(0, 999999); ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-eye mr-1"></i>
                                    <?php echo lang('Common.view'); ?>
                                </a>
                                <a href="<?= "/" . $tableName . "/exportPDF/" . @$purchase_invoice->uuid . "?" . rand(0, 999999); ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-print mr-1"></i>
                                    <?php echo lang('Common.print_pdf'); ?>
                                </a>
                            <?php } ?>

                            <?php if ($tableName == "purchase_orders") { ?>
                                <a target="" href="<?= "/" . $tableName . "/clone/" . @$purchase_order->uuid; ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-copy mr-1"></i>
                                    <?php echo lang('Common.clone'); ?>
                                </a>
                            <?php } ?>

                            <?php if ($tableName == "purchase_orders") { ?>
                                <a target="_blank"
                                    href="<?= "/" . $tableName . "/exportPDF/" . @$purchase_order->uuid . "/view?" . rand(0, 999999); ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-eye mr-1"></i>
                                    <?php echo lang('Common.view'); ?>
                                </a>
                                <a href="<?= "/" . $tableName . "/exportPDF/" . @$purchase_order->uuid . "?" . rand(0, 999999); ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-print mr-1"></i>
                                    <?php echo lang('Common.print_pdf'); ?>
                                </a>
                            <?php } ?>

                            <?php if ($tableName == "work_orders") { ?>
                                <a target="" href="<?= "/" . $tableName . "/clone/" . @$work_order->uuid; ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-copy mr-1"></i>
                                    <?php echo lang('Common.clone'); ?>
                                </a>
                            <?php } ?>

                            <?php if ($tableName == "work_orders") { ?>
                                <a target="_blank"
                                    href="<?= "/" . $tableName . "/exportPDF/" . @$work_order->uuid . "/view?" . rand(0, 999999); ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-eye mr-1"></i>
                                    <?php echo lang('Common.view'); ?>
                                </a>
                                <a href="<?= "/" . $tableName . "/exportPDF/" . @$work_order->uuid . "?" . rand(0, 999999); ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-print mr-1"></i>
                                    <?php echo lang('Common.print_pdf'); ?>
                                </a>
                            <?php } ?>

                            <?php if ($tableName == "timeslips" && !empty($timeslips['uuid'])) { ?>
                                <a target="" href="<?= "/" . $tableName . "/clone/" . $timeslips['uuid']; ?>"
                                    class="btn btn-secondary mr-2"><i class="fa fa-copy mr-1"></i>
                                    <?php echo lang('Common.clone'); ?>
                                </a>
                            <?php } ?>

                            <?php if ($tableName == "timeslips" && !empty($timeslips['uuid'])) { ?>
                                <a href="/<?php echo strtolower($tableName) . (!empty($session->get('list_week')) ? '?list_week=' . $session->get('list_week') . '&' : '?') . (!empty($session->get('list_monthpicker')) ? 'list_month=' . $session->get('list_monthpicker') : '') . (!empty($session->get('list_yearpicker')) ? '&list_year=' . $session->get('list_yearpicker') : ''); ?>"
                                    class="btn btn-primary"><i class="<?php echo @$activeIcon; ?>"></i>
                                    <?php
                                    if (isset($menuName)) {
                                        echo ucfirst($menuName);
                                    } else {
                                        echo render_head_text($tableName);
                                    } ?>
                                </a>
                            <?php } else { ?>
                                <a href="/<?php echo strtolower($tableName) . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''); ?>"
                                    class="btn btn-primary"><i class="<?php echo @$activeIcon; ?>"></i>
                                    <?php
                                    if (isset($menuName)) {
                                        echo ucfirst($menuName);
                                    } else {
                                        echo render_head_text($tableName);
                                    } ?>
                                </a>
                            <?php } ?>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row ">

                <div class="col-lg-12">
                    <?php if (session()->has('message')) { ?>

                        <div class="alert <?= session()->getFlashdata('alert-class') ?>">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php } ?>
                    <div class="white_card card_height_100 mb_30">



                        <!-- BEGIN LOGIN -->
                        <div id="ajax_loader" style="position: fixed;
    top: 0px;
    background: rgba(0, 0, 0, 0.25);
    height: 100%;
    width: 100%;
    z-index: 99999;display: none;">
                            <img style="position: absolute;
    top: 50%;
    left: 50%;
    margin-left: -16px;
    margin-top: -16px;
    z-index: 9999999;" alt="" src="/assets/img/ajax-loader.gif" ?>>
                        </div>