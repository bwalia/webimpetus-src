<?php require_once(APPPATH . 'Views/common/header.php'); ?>
<!-- main content part here -->
<?php require_once(APPPATH . 'Views/common/sidebar.php'); ?>
<style>
    .card.draggable {
        margin-bottom: 1rem;
        cursor: grab;
    }

    .droppable {
        /* background-color: var(--success); */
        background: rgba(80, 212, 238, 0.8) !important;
        min-height: 120px;
        margin-bottom: 1rem;
    }

    .card-body-custom {
        padding: 0.75rem !important;
    }

    .card-body-custom p {
        font-size: 16px !important;
        line-height: 20px !important;
    }

    .card-title a.lead {
        font-size: revert;
    }

    .card-title a.lead:hover {
        text-decoration: underline;
    }

    .bg-light {
        /* background: rgba(80, 212, 238, 0.2) !important; */
        background: rgba(233, 20, 90, 0.33) !important;
    }

    .btn-group-sm>.btn,
    .btn-sm {
        font-size: 0.7rem;
    }
</style>

<section class="main_content dashboard_part large_header_bg">
    <?php require_once(APPPATH . 'Views/common/top-header.php'); ?>
    <div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">
            <!-- page title  -->

            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                        <div class="page_title_left d-flex align-items-center">
                            <h3 class="f_s_25 f_w_700 dark_text mr_30"><!--php echo ucfirst($tableName); -->Kanban Board</h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active"><a href="/<?php echo $tableName; ?>"><?php echo ucfirst($tableName); ?> </a></li>
                            </ol>
                        </div>

                        <div class="page_title_right d-flex align-items-center">
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item">
                                    <div class="form-group required col-md-12">
                                        <?php
                                        $blank_item = array("id" => "", "sprint_name" => "--Choose Sprint--");
                                        array_unshift($sprints_list, $blank_item);

                                        //$backlog_item = array("id" => "backlog", "sprint_name" => "Backlog");
                                        //array_push($sprints_list,$backlog_item);


                                        ?>
                                        <!-- <label for="inputModule">Choose Sprint</label> -->
                                        <?php echo (($_GET['sprint'] ?? "") == "" ?  "" : "Selected") ?>
                                        <select id="kanban_sprint" class="form-control required">
                                            <!-- <option value="" <?php echo (($_GET['sprint'] ?? "") == "" ?  "" : "selected") ?>>--Choose Sprint--</option> -->
                                            <?php foreach ($sprints_list as $row) : ?>
                                                <option <?php echo (($_GET['sprint'] ?? "") == $row["id"] ?  "selected" : "") ?> value="<?php echo ($row["id"]) ?>"><?php echo ($row["sprint_name"]) ?></option>
                                            <?php endforeach; ?>
                                           

                                        </select>
                                    </div>
                            </ol>
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

                    <div class="social_media_card card_height_100 mb_20 " style="padding: 26px 26px 36px 26px; background:#FFF !important;">