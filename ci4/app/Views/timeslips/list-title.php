<?php require_once(APPPATH . 'Views/common/header.php'); ?>
<!-- main content part here -->
<?php require_once(APPPATH . 'Views/common/sidebar.php'); ?>
<section class="main_content dashboard_part large_header_bg full_main_content">
    <?php require_once(APPPATH . 'Views/common/top-header.php'); ?>
    <div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">
            <!-- page title  -->

            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                        <div class="page_title_left d-flex align-items-center">
                            <h3 class="f_s_25 f_w_700 dark_text mr_30">
                                <?php if (isset($menuName)) {
                                    $addLink = "/$tableName/edit/0?cat=strategies";
                                    echo ucfirst($menuName);
                                } else {
                                    $addLink = "/$tableName/edit";
                                    echo render_head_text($tableName);
                                } ?>
                            </h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="/dashboard">
                                        <?php echo lang('Common.home'); ?>
                                    </a></li>
                                <li class="breadcrumb-item active"><a href="/<?php echo $tableName; ?>">
                                        <?php if (isset($menuName)) {
                                            echo ucfirst($menuName);
                                        } else {
                                            echo render_head_text($tableName);
                                        } ?> </a></li>
                            </ol>
                        </div>
                        <div class="page_title_right">

                            <div class="header_more_tool setDropDownBlk">
                                <a href="/timeslips?reset=1" class="btn btn-primary"><i class="ti-reload"></i>
                                    <?php echo lang('Common.refresh'); ?>
                                </a>
                                <?php if (isset($is_add_permission) && $is_add_permission == 0) { ?>

                                <?php } else { ?>
                                    <a href="<?php echo $addLink; ?>" class="btn btn-primary"><i class="ti-plus"></i>
                                        <?php echo lang('Common.add'); ?>
                                        <?php if (isset($menuName)) {
                                            echo ucfirst($menuName);
                                        } else {
                                            echo render_head_text($rawTblName);
                                        } ?>
                                    </a>
                                <?php } ?>
                                <button data-toggle="modal" data-target="#exampleModal"
                                    href="<?php echo base_url("timeslips/downloadPdf"); ?>" class="btn btn-primary"><i
                                        class="ti-export"></i>
                                    <?php echo lang('Common.export_pdf'); ?>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row ">
                <div class="col-lg-12">
                    <!-- // Display Response -->
                    <?php if (session()->has('message')) { ?>

                        <div class="alert <?= session()->getFlashdata('alert-class') ?>">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php } ?>

                    <div class="white_card card_height_100 mb_20 ">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div method="GET">
                                    <div class="row">
                                        <div class="form-group mr-3">
                                            <input type="text" class="form-control"
                                                placeholder="Filter by task name, employee" name="filter" id="filter"
                                                aria-controls="example" value="<?= @$_GET['filter'] ?>">
                                        </div>
                                        <div class="form-group mr-3">
                                            <select class="form-control" id="list_week" name="list_week"
                                                onchange="window.searchTimeslips()">
                                                <option value="none">--
                                                    <?php echo lang('Common.select_week'); ?>--
                                                </option>
                                                <?php foreach ($weeks as $row): ?>
                                                    <option <?= (($list_week ?? "") == $row["week_no"] ? "selected" : "") ?>
                                                        value="<?php echo ($row["week_no"]) ?>"><?= $row["week_no"] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group mr-3">
                                            <select class="form-control" id="list_monthpicker2" name="list_monthpicker"
                                                onchange="window.searchTimeslips()">
                                                <option value="none">--
                                                    <?php echo lang('Common.select_month'); ?>--
                                                </option>
                                                <?php for ($iM = 1; $iM <= 12; $iM++) { ?>
                                                    <option <?= (($list_monthpicker ?? "") == $iM ? "selected" : "") ?>
                                                        value="<?php echo ($iM) ?>"><?php echo date('F', mktime(0, 0, 0, $iM, 10)); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group mr-3">
                                            <select class="form-control" id="list_yearpicker2" name="list_yearpicker"
                                                onchange="window.searchTimeslips()">
                                                <option value="none">--
                                                    <?php echo lang('Common.select_year'); ?>--
                                                </option>

                                                <?php for ($iM = 0; $iM <= 4; $iM++) { ?>
                                                    <option <?= (($list_yearpicker ?? "") == date("Y", strtotime("-" . $iM . " year")) ? "selected" : "") ?>
                                                        value="<?php echo date("Y", strtotime("-" . $iM . " year")) ?>"><?php echo date("Y", strtotime("-" . $iM . " year")); ?></option>
                                                <?php } ?>


                                            </select>
                                        </div>
                                        <!-- <div class="form-group">
                                            <button type="button" onclick="window.searchTimeslips()" class="btn btn-outline-secondary"><i class="ti-search"></i> <?php echo lang('Common.search'); ?></button>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>