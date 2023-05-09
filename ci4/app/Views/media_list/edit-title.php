<?php require_once (APPPATH.'Views/common/header.php'); ?>
<!-- main content part here -->

<?php require_once (APPPATH.'Views/common/sidebar.php'); ?>
<section class="main_content dashboard_part large_header_bg">
<?php require_once (APPPATH.'Views/common/top-header.php'); ?>
<div class="main_content_iner overly_inner ">
<div class="container-fluid p-0 ">
    <!-- page title  -->
    <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                <div class="page_title_left d-flex align-items-center">
                    <h3 class="f_s_25 f_w_700 dark_text mr_30" ><?php echo render_head_text($tableName);?> </h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                        <li class="breadcrumb-item active"><a href="/<?php echo $tableName; ?>"><?php echo render_head_text($tableName);?> </a></li>
                    </ol>
                </div>
                <div class="page_title_right">
                    <a href="/<?php echo $tableName; ?>" class="btn btn-primary"><i class="<?php echo @$activeIcon; ?>"></i> <?php echo render_head_text($tableName);?>  </a>
                </div>
                
            </div>
        </div>
    </div>
    <div class="row ">

        <div class="col-lg-12">
            <div class="white_card card_height_100 mb_30">
                       