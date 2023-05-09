<?php include('common/header.php'); ?>
<!-- main content part here -->


<?php include('common/sidebar.php'); ?>

<section class="main_content dashboard_part large_header_bg full_main_content">
    <?php include('common/top-header.php'); ?>
    <div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">
            <!-- page title  -->
            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                        <div class="page_title_left d-flex align-items-center">
                            <h3 class="f_s_25 f_w_700 dark_text mr_30">Dashboard</h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Analytic</li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                            <!-- <div class="page_date_button d-flex align-items-center">
                                <img src="/img/icon/calender_icon.svg" alt="">
                                August 1, 2020 - August 31, 2020
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 ">
                    <div class="white_card mb_30 user_crm_wrapper dsbrdIconsRow">
                        <div class="row" id="dashboard_row">
                            <?php
                            $count = 0;
                            foreach ($tableList as $table => $eachInfo) {
                                if (in_array(@$eachInfo['menu']['name'], $user_permissions)) {
                                    $count++;
                            ?>
                                    <div class="col-xxl-2 col-xl-3 col-lg-6 col-md-4">
                                        <div class="dashboard-card">
                                            <a href="<?php echo @$eachInfo['url']; ?>" target="_blank">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="col">
                                                        <div class="dashContent">
                                                            <h4 class="dashCount"><?= @$eachInfo['total'] ?></h4>
                                                            <p class="dashTitle"><?= @$eachInfo['menu']['name'] ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="dashBrdIcon">
                                                            <i class="<?= @$eachInfo['menu']['icon'] ?>"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                            <?php }
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="view_more_list" style="display:none">
                <div class="col-xl-12 ">
                    <div class="white_card mb_30 user_crm_wrapper dsbrdIconsRow">
                        <div class="row">
                            <?php /* foreach ($allList as $table => $eachInfo) {
                                if (in_array(@$eachInfo['menu']['name'], $user_permissions)) { ?>
                                    <div class="col-xxl-2 col-xl-3 col-lg-6 col-md-4">
                                        <div class="dashboard-card">
                                            <a href="<?php echo @$eachInfo['url']; ?>">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="col">
                                                        <div class="dashContent">
                                                            <h4 class="dashCount"><?= @$eachInfo['total'] ?></h4>
                                                            <p class="dashTitle"><?= @$eachInfo['menu']['name'] ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="dashBrdIcon">
                                                            <i class="<?= @$eachInfo['menu']['icon'] ?>"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                            <?php }
                            }*/ ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($count > 8) { ?><div class="row">
                    <div class="col-12">
                        <div class="create_report_btn text-center mb_30">
                            <a href="javascript:void(0);" class="btn_1 radius_btn  text-center view_more" style="padding:15px 20px;">View More</a>
                        </div>
                    </div>
                <?php } ?>
                </div>
                <div class="row">
                    <div class="col-xl-4">
                        <div class="white_card c mb_30">
                            <div class="white_card_header">
                                <div class="row align-items-center">
                                    <div class="col-lg-4">
                                        <div class="main-title">
                                            <h3 class="m-0">Users</h3>
                                        </div>
                                    </div>
                                    <!-- <div class="col-lg-8 d-flex justify-content-end">
                                    <select class="select2 col-md-5" >
                                        <option value="1">Show by All</option>
                                        <option value="1">Show by A</option>
                                        <option value="1">Show by B</option>
                                    </select>
                                </div> -->
                                </div>

                            </div>
                            <?php foreach ($recent_users as $users) { ?>
                                <div class="white_card_body ">
                                    <div class="single_user_pil d-flex align-items-center justify-content-between">
                                        <div class="user_pils_thumb d-flex align-items-center">
                                            <div class="thumb_34 mr_15 mt-0"><img class="img-fluid radius_50" src="<?=(!empty(@$users->profile_img))?'data:image/jpeg;base64,'.@$users->profile_img:'/assets/img/1.jpg'?>" alt=""></div>
                                            <span class="f_s_14 f_w_400 text_color_red"><?php echo $users->name ?></span>
                                        </div>
                                        <div class="user_info">
                                            <?php echo $users->email; ?>
                                        </div>
                                        <div class="action_btns d-flex">
                                            <a href="/users/edit/<?php echo $users->uuid; ?>" class="action_btn mr_10"> <i class="far fa-edit"></i> </a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="white_card  mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h3 class="m-0">Sales of the last week</h3>
                                    </div>
                                    <div class="header_more_tool dropInr">
                                        <div class="dropdown">
                                            <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                                <i class="ti-more-alt"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#"> <i class="ti-eye"></i> Action</a>
                                                <a class="dropdown-item" href="#"> <i class="ti-trash"></i> Delete</a>
                                                <a class="dropdown-item" href="#"> <i class="fas fa-edit"></i> Edit</a>
                                                <a class="dropdown-item" href="#"> <i class="ti-printer"></i> Print</a>
                                                <a class="dropdown-item" href="#"> <i class="fa fa-download"></i> Download</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body">
                                <div id="chart-currently"></div>
                                <div class="monthly_plan_wraper">
                                    <div class="single_plan d-flex align-items-center justify-content-between">
                                        <div class="plan_left d-flex align-items-center">
                                            <div class="thumb">
                                                <img src="img/icon2/7.svg" alt="">
                                            </div>
                                            <div>
                                                <h5>Most Sales</h5>
                                                <span>Authors with the best sales</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="single_plan d-flex align-items-center justify-content-between">
                                        <div class="plan_left d-flex align-items-center">
                                            <div class="thumb">
                                                <img src="img/icon2/6.svg" alt="">
                                            </div>
                                            <div>
                                                <h5>Total sales lead</h5>
                                                <span>40% increased on week-to-week reports</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="single_plan d-flex align-items-center justify-content-between">
                                        <div class="plan_left d-flex align-items-center">
                                            <div class="thumb">
                                                <img src="img/icon2/5.svg" alt="">
                                            </div>
                                            <div>
                                                <h5>Average Bestseller</h5>
                                                <span>Pitstop Email Marketing</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="white_card  mb_30 overflow_hidden">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h3 class="m-0">Sales Details</h3>
                                    </div>
                                    <div class="header_more_tool dropInr">
                                        <div class="dropdown">
                                            <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                                <i class="ti-more-alt"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#"> <i class="ti-eye"></i> Action</a>
                                                <a class="dropdown-item" href="#"> <i class="ti-trash"></i> Delete</a>
                                                <a class="dropdown-item" href="#"> <i class="fas fa-edit"></i> Edit</a>
                                                <a class="dropdown-item" href="#"> <i class="ti-printer"></i> Print</a>
                                                <a class="dropdown-item" href="#"> <i class="fa fa-download"></i> Download</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body pb-0">
                                <div class="Sales_Details_plan">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="single_plan d-flex align-items-center justify-content-between">
                                                <div class="plan_left d-flex align-items-center">
                                                    <div class="thumb">
                                                        <img src="img/icon2/3.svg" alt="">
                                                    </div>
                                                    <div>
                                                        <h5>$2,034</h5>
                                                        <span>Author Sales</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="single_plan d-flex align-items-center justify-content-between">
                                                <div class="plan_left d-flex align-items-center">
                                                    <div class="thumb">
                                                        <img src="img/icon2/2.svg" alt="">
                                                    </div>
                                                    <div>
                                                        <h5>$5.8M</h5>
                                                        <span>All Time Sales</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="chart_wrap overflow_hidden">
                                <div id="chart4"></div>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-4">
                        <div class="white_card  mb_20 ">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h3 class="m-0">Employees</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body QA_section">
                                <div class="QA_table ">
                                    <!-- table-responsive -->
                                    <table class="table lms_table_active2 p-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_employees as $employee) { ?>
                                                <tr>
                                                    <td scope="col" class="text_color_red"><?= $employee->first_name . ' ' . $employee->title; ?></td>
                                                    <td scope="col" class="color_text_6"><?= $employee->email; ?>
                                                        <a href="/employees/editrow/<?php echo $employee->uuid; ?>" class="action_btn mr_10"> <i class="far fa-edit"></i> </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="white_card  mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h3 class="m-0">Recent activity</h3>
                                    </div>
                                    <div class="header_more_tool dropInr">
                                        <div class="dropdown">
                                            <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                                <i class="ti-more-alt"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#"> <i class="ti-eye"></i> Action</a>
                                                <a class="dropdown-item" href="#"> <i class="ti-trash"></i> Delete</a>
                                                <a class="dropdown-item" href="#"> <i class="fas fa-edit"></i> Edit</a>
                                                <a class="dropdown-item" href="#"> <i class="ti-printer"></i> Print</a>
                                                <a class="dropdown-item" href="#"> <i class="fa fa-download"></i> Download</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body">
                                <div class="Activity_timeline">
                                    <ul>
                                        <li>
                                            <div class="activity_bell"></div>
                                            <div class="timeLine_inner d-flex align-items-center">
                                                <div class="activity_wrap">
                                                    <h6>5 min ago</h6>
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="activity_bell "></div>
                                            <div class="timeLine_inner d-flex align-items-center">
                                                <div class="activity_wrap">
                                                    <h6>5 min ago</h6>
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="activity_bell bell_lite"></div>
                                            <div class="timeLine_inner d-flex align-items-center">
                                                <div class="activity_wrap">
                                                    <h6>5 min ago</h6>
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="activity_bell bell_lite"></div>
                                            <div class="timeLine_inner d-flex align-items-center">
                                                <div class="activity_wrap">
                                                    <h6>5 min ago</h6>
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="white_card mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h3 class="m-0">Member request
                                            to mail.</h3>
                                    </div>
                                    <div class="header_more_tool dropInr">
                                        <div class="dropdown">
                                            <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                                <i class="ti-more-alt"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#"> <i class="ti-eye"></i> Action</a>
                                                <a class="dropdown-item" href="#"> <i class="ti-trash"></i> Delete</a>
                                                <a class="dropdown-item" href="#"> <i class="fas fa-edit"></i> Edit</a>
                                                <a class="dropdown-item" href="#"> <i class="ti-printer"></i> Print</a>
                                                <a class="dropdown-item" href="#"> <i class="fa fa-download"></i> Download</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body">
                                <div class="thumb mb_30">
                                    <img src="/img/table.svg" alt="" class="img-fluid">
                                </div>
                                <div class="common_form">
                                    <form action="#">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="common_input mb_15">
                                                    <input type="text" placeholder="First Name">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="common_input mb_15">
                                                    <input type="text" placeholder="Last Name">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="common_input mb_15">
                                                    <input type="text" placeholder="Email">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <select class="select2 col-lg-12">
                                                    <option value="1">Role</option>
                                                    <option value="1">Member</option>
                                                    <option value="1">Editor</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <div class="create_report_btn mt_30">
                                                    <a href="#" class="btn_1 radius_btn d-block text-center">Send the invitation link</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
        </div>
    </div>

    <?php include('common/footer.php'); ?>
</section>
<!-- main content part end -->

<?php require_once('common/scripts.php'); ?>

<script>
    $(".view_more").click(function() {
        // $("#view_more_list").show();
        $(".view_more").hide();
        var list = document.getElementById("dashboard_row");
        for (var i = 1; i < list.children.length; i++) {
            list.children[i].style.display = "block";
        }
    })
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
<script>
    if ($("#addcat").length > 0) {
        $("#addcat").validate({
            rules: {
                name: {
                    required: true,
                },
                uuid: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                uuid: {
                    required: "Please select userid",
                },

            },
        })
    }
</script>

<style>
    .white_card .white_card_body {
        padding: 10px !important;
    }

    .row .col-xxl-2:nth-of-type(1n+9) {
        display: none;
    }
</style>