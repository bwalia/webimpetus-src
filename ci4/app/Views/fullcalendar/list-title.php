<?php require_once (APPPATH.'Views/common/header.php'); ?>
<!-- main content part here -->
<?php require_once (APPPATH.'Views/common/sidebar.php'); ?>
<style>
.fc-toolbar-chunk > .btn-group {
    height: 38px;
}
</style>
<link href='<?php echo base_url('assets/vendors/fullcalendar-5.10.2/lib/main.min.css') ?>' rel='stylesheet' />
<link href='<?php echo base_url('assets/css/calendar.css') ?>' rel='stylesheet' />
<script src='<?php echo base_url('assets/vendors/fullcalendar-5.10.2/lib/main.min.js') ?>'></script>

<section class="main_content dashboard_part large_header_bg">
    <?php require_once (APPPATH.'Views/common/top-header.php'); ?>
      <div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">
            <!-- page title  -->

            <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                <div class="page_title_left d-flex align-items-center">
                    <h3 class="f_s_25 f_w_700 dark_text mr_30" ><?php echo ucfirst($tableName); ?> </h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                        <li class="breadcrumb-item active"><a href="/<?php echo $tableName; ?>"><?php echo ucfirst($tableName); ?> </a></li>
                    </ol>
                </div>
                <div class="page_title_right">
                    
                    <div class="header_more_tool setDropDownBlk">
                   
                    </div>

                </div>
        </div>
    </div>
</div>

<div class="row ">
  <div class="col-lg-12">
        <!-- // Display Response -->
    <?php  if(session()->has('message')){ ?>

        <div class="alert <?= session()->getFlashdata('alert-class') ?>">
        <?= session()->getFlashdata('message') ?>
        </div>
    <?php } ?>

    <div class="white_card card_height_100 mb_20 " style="padding: 26px 26px 36px 26px;">
