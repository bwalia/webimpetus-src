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
                            <h3 class="f_s_25 f_w_700 dark_text mr_30">Generate VAT Return</h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                <li class="breadcrumb-item"><a href="/vat_returns">VAT Returns</a></li>
                                <li class="breadcrumb-item active">Generate</li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                            <div class="header_more_tool setDropDownBlk">
                                <a href="/vat_returns" class="btn btn-secondary">
                                    <i class="ti-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <?php if(session()->has('message')){ ?>
                        <div class="alert <?= session()->getFlashdata('alert-class') ?>">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php } ?>

                    <div class="white_card card_height_100 mb_20">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Select Quarter and Year</h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <form action="/vat_returns/preview" method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="year">Year <span class="text-danger">*</span></label>
                                            <select name="year" id="year" class="form-control" required>
                                                <?php
                                                $currentYear = date('Y');
                                                for ($i = $currentYear; $i >= $currentYear - 10; $i--):
                                                ?>
                                                    <option value="<?= $i ?>" <?= $i == $current_year ? 'selected' : '' ?>>
                                                        <?= $i ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="quarter">Quarter <span class="text-danger">*</span></label>
                                            <select name="quarter" id="quarter" class="form-control" required>
                                                <option value="1" <?= $current_quarter == 1 ? 'selected' : '' ?>>Q1 (Jan - Mar)</option>
                                                <option value="2" <?= $current_quarter == 2 ? 'selected' : '' ?>>Q2 (Apr - Jun)</option>
                                                <option value="3" <?= $current_quarter == 3 ? 'selected' : '' ?>>Q3 (Jul - Sep)</option>
                                                <option value="4" <?= $current_quarter == 4 ? 'selected' : '' ?>>Q4 (Oct - Dec)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="ti-info-alt"></i>
                                    <strong>Note:</strong> This will calculate VAT from all sales invoices in the selected quarter.
                                    <ul class="mt-2 mb-0">
                                        <li>UK customers: VAT will be accounted for in UK VAT total</li>
                                        <li>Non-UK customers: VAT will be tracked separately in Non-UK VAT total</li>
                                    </ul>
                                </div>

                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti-eye"></i> Preview VAT Calculations
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
