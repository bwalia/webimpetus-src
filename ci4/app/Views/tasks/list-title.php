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
                                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                                <li class="breadcrumb-item active"><a href="/<?php echo $tableName; ?>">
                                        <?php if (isset($menuName)) {
                                            echo ucfirst($menuName);
                                        } else {
                                            echo render_head_text($tableName);
                                        } ?> </a></li>
                            </ol>
                        </div>
                        <div class="page_title_right">
                            <style>
                                .task-view-toggle {
                                    display: inline-flex;
                                    background: #f3f4f6;
                                    border-radius: 8px;
                                    padding: 4px;
                                    margin-right: 12px;
                                }
                                .task-view-toggle-btn {
                                    padding: 8px 16px;
                                    border: none;
                                    background: transparent;
                                    color: #6b7280;
                                    font-weight: 600;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    transition: all 0.2s;
                                    text-decoration: none;
                                    font-size: 0.875rem;
                                }
                                .task-view-toggle-btn.active {
                                    background: white;
                                    color: #667eea;
                                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                                }
                                .task-view-toggle-btn:hover {
                                    color: #667eea;
                                }
                            </style>

                            <div class="task-view-toggle">
                                <a href="/tasks" class="task-view-toggle-btn <?php echo (strpos($_SERVER['REQUEST_URI'], '/board') === false) ? 'active' : ''; ?>">
                                    <i class="fa fa-list"></i> List
                                </a>
                                <a href="/tasks/board" class="task-view-toggle-btn <?php echo (strpos($_SERVER['REQUEST_URI'], '/board') !== false) ? 'active' : ''; ?>">
                                    <i class="fa fa-th"></i> Board
                                </a>
                            </div>

                            <?php if (strpos($_SERVER['REQUEST_URI'], '/board') === false && isset($taskStatusList)) : ?>
                            <div class="form-group mr-3 mt-2" style="display: inline-block;">
                                <select id="task_status" class="form-control required">
                                    <?php foreach ($taskStatusList as $row) : ?>
                                        <option <?php echo (($_GET['status'] ?? "") == strtolower($row["value"]) ?  "selected" : "") ?> value="<?php echo ($row["key"]) ?>"><?php echo (ucfirst($row["value"])) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>

                            <div class="header_more_tool setDropDownBlk">
                                <a href="javascript:void(0)" onClick="window.location.reload();" class="btn btn-primary"><i class="ti-reload"></i> Refresh</a>
                                <?php if (isset($is_add_permission) && $is_add_permission == 0) { ?>

                                <?php } else { ?>
                                    <a href="<?php echo $addLink; ?>" class="btn btn-primary"><i class="ti-plus"></i> Add <?php if (isset($menuName)) {
                                                                                                                                echo ucfirst($menuName);
                                                                                                                            } else {
                                                                                                                                echo render_head_text($rawTblName);
                                                                                                                            } ?></a>
                                <?php } ?>

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