<?php include('common/header.php'); ?>
<!-- main content part here -->

<?php include('common/sidebar.php'); ?>

<style>
    /* JIRA-Style Dashboard Styling */
    .dashboard_part {
        background: #f4f5f7 !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .main_content_iner {
        background: transparent !important;
    }

    /* Page Header - JIRA Style */
    .page_title_box {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }

    .page_title_box h3 {
        font-size: 24px;
        font-weight: 600;
        color: #172b4d;
        margin: 0;
    }

    .breadcrumb {
        background: transparent;
        margin: 0;
        padding: 0;
    }

    .breadcrumb-item {
        font-size: 14px;
        color: #5e6c84;
    }

    .breadcrumb-item a {
        color: #0052cc;
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: #0065ff;
        text-decoration: underline;
    }

    /* Dashboard Cards - JIRA Style */
    .dashboard-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.2s ease;
        border: 1px solid #dfe1e6;
        box-shadow: 0 1px 1px rgba(9, 30, 66, 0.08);
        height: 100%;
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(9, 30, 66, 0.15);
        border-color: #0052cc;
    }

    .dashboard-card a {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .dashCount {
        font-size: 28px;
        font-weight: 700;
        color: #172b4d;
        margin: 0 0 4px 0;
        line-height: 1;
    }

    .dashTitle {
        font-size: 14px;
        font-weight: 600;
        color: #5e6c84;
        margin: 0;
        text-transform: none;
    }

    .dashBrdIcon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    /* White Cards - JIRA Style */
    .white_card {
        background: white;
        border-radius: 8px;
        border: 1px solid #dfe1e6;
        box-shadow: 0 1px 1px rgba(9, 30, 66, 0.08);
        transition: box-shadow 0.2s ease;
    }

    .white_card:hover {
        box-shadow: 0 4px 8px rgba(9, 30, 66, 0.15);
    }

    .white_card_header {
        padding: 16px 20px;
        border-bottom: 1px solid #f4f5f7;
    }

    .white_card_header h3 {
        font-size: 16px;
        font-weight: 600;
        color: #172b4d;
        margin: 0;
    }

    .white_card_body {
        padding: 16px 20px;
    }

    /* User List - JIRA Style */
    .single_user_pil {
        padding: 12px 0;
        border-bottom: 1px solid #f4f5f7;
        transition: background 0.2s ease;
    }

    .single_user_pil:last-child {
        border-bottom: none;
    }

    .single_user_pil:hover {
        background: #f4f5f7;
        border-radius: 4px;
        margin: 0 -8px;
        padding-left: 8px;
        padding-right: 8px;
    }

    .single_user_pil span {
        font-size: 14px;
        font-weight: 500;
        color: #172b4d;
    }

    .user_info {
        font-size: 13px;
        color: #5e6c84;
    }

    .action_btn {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f4f5f7;
        color: #5e6c84;
        transition: all 0.2s ease;
    }

    .action_btn:hover {
        background: #0052cc;
        color: white;
    }

    /* Buttons - JIRA Style */
    .btn_1 {
        background: #0052cc;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn_1:hover {
        background: #0065ff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 82, 204, 0.3);
    }

    /* Tables - JIRA Style */
    .QA_table table,
    .dataTable {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .QA_table thead th,
    .dataTable thead th {
        background: #f4f5f7;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        color: #5e6c84;
        padding: 12px 16px;
        border-bottom: 2px solid #dfe1e6;
    }

    .QA_table tbody td,
    .dataTable tbody td {
        padding: 12px 16px;
        font-size: 14px;
        color: #172b4d;
        border-bottom: 1px solid #f4f5f7;
    }

    .QA_table tbody tr:hover,
    .dataTable tbody tr:hover {
        background: #f4f5f7;
        transition: background 0.2s ease;
    }

    /* Responsive Grid */
    @media (max-width: 1400px) {
        .col-xxl-2 {
            flex: 0 0 20%;
            max-width: 20%;
        }
    }

    @media (max-width: 991px) {
        .dashboard-card {
            margin-bottom: 12px;
        }
    }
</style>

<section class="main_content dashboard_part large_header_bg full_main_content">
    <?php include('common/top-header.php'); ?>
    <div class="main_content_iner overly_inner ">
        <div class="container-fluid p-0 ">
            <!-- page title  -->
            <div class="row">
                <div class="col-12">
                    <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                        <div class="page_title_left d-flex align-items-center">
                            <h3 class="f_s_25 f_w_700 dark_text mr_30">📊 Dashboard</h3>
                            <ol class="breadcrumb page_bradcam mb-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
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
                                                        <h5>$<?= number_format($sales_totals['current_month'], 2) ?></h5>
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
                                                        <h5>$<?= number_format($sales_totals['all_time'], 2) ?></h5>
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

                    <div class="col-xl-4">
                        <div class="white_card mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h3 class="m-0">Incidents Per Customer</h3>
                                    </div>
                                    <div class="header_more_tool dropInr">
                                        <div class="dropdown">
                                            <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                                <i class="ti-more-alt"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="/incidents"> <i class="ti-eye"></i> View All</a>
                                                <a class="dropdown-item" href="/incidents/edit"> <i class="fas fa-plus"></i> New Incident</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body">
                                <div id="incidents_customer_chart"></div>
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
    // Pass sales data from PHP to JavaScript
    window.salesChartData = {
        months: <?= json_encode($sales_chart_data['months']) ?>,
        data: <?= json_encode($sales_chart_data['data']) ?>
    };

    // Pass weekly sales progress data
    window.weeklySalesData = {
        percentage: <?= $weekly_sales['percentage'] ?>,
        currentWeek: <?= $weekly_sales['current_week'] ?>,
        lastWeek: <?= $weekly_sales['last_week'] ?>
    };

    // Pass incidents per customer data
    window.incidentsCustomerData = {
        customers: <?= json_encode($incidents_per_customer['customers']) ?>,
        counts: <?= json_encode($incidents_per_customer['counts']) ?>
    };
</script>

<script>
    // Create incidents per customer chart
    if (document.querySelector("#incidents_customer_chart")) {
        var incidentsData = window.incidentsCustomerData || {
            customers: [],
            counts: []
        };

        // Limit to top 5 customers for better visibility
        var topCustomers = incidentsData.customers.slice(0, 5);
        var topCounts = incidentsData.counts.slice(0, 5);

        // Generate different colors for each bar
        var chartColors = ['#ff004e', '#9767FD', '#f1b44c', '#50a5f1', '#34c38f'];

        var incidentsChartOptions = {
            series: [{
                name: 'Incidents',
                data: topCounts
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '60%',
                    endingShape: 'rounded',
                    distributed: true,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '11px',
                    fontWeight: 'bold',
                    colors: ["#304758"]
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: topCustomers,
                labels: {
                    rotate: -45,
                    rotateAlways: false,
                    style: {
                        fontSize: '10px'
                    },
                    trim: true
                }
            },
            yaxis: {
                title: {
                    text: 'Incidents',
                    style: {
                        fontSize: '11px'
                    }
                },
                labels: {
                    style: {
                        fontSize: '10px'
                    }
                }
            },
            colors: chartColors,
            legend: {
                show: false
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " incidents"
                    }
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                padding: {
                    bottom: 10
                }
            }
        };

        var incidentsChart = new ApexCharts(document.querySelector("#incidents_customer_chart"), incidentsChartOptions);
        incidentsChart.render();
    }
</script>

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