<?php require_once(APPPATH . 'Views/common/header.php'); ?>
<!-- main content part here -->
<?php require_once(APPPATH . 'Views/common/sidebar.php'); ?>
<style>
    /* Modern Kanban Board Styling */
    .kanban-container {
        background: #ffffff;
        min-height: 100vh;
        padding: 20px 0;
        /* Removed fixed padding to allow responsive sidebar behavior */
    }

    /* Sidebar responsiveness - ensure padding adjusts when sidebar toggles */
    .main_content.kanban-container {
        transition: padding-left 0.3s ease !important;
    }

    .kanban-board {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        padding: 25px;
        margin: 0 15px;
        border: 1px solid #e9ecef;
    }

    .kanban-column {
        background: #ffffff;
        border-radius: 12px;
        min-height: 600px;
        margin: 0 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .kanban-column:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .kanban-column-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 15px;
        border-radius: 12px 12px 0 0;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .kanban-column-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: all 0.5s;
    }

    .kanban-column-header:hover::before {
        left: 100%;
    }

    /* Column-specific colors - Darker, more saturated to match brand */
    .kanban-column[data-category="todo"] .kanban-column-header {
        background: linear-gradient(135deg, #d946ef 0%, #e11d48 100%);
    }

    .kanban-column[data-category="in-progress"] .kanban-column-header {
        background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
    }

    .kanban-column[data-category="review"] .kanban-column-header {
        background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
    }

    .kanban-column[data-category="done"] .kanban-column-header {
        background: linear-gradient(135deg, #f59e0b 0%, #eab308 100%);
    }

    .kanban-column-content {
        padding: 8px 10px;
        min-height: 520px;
    }

    .kanban-task {
        background: white;
        border-radius: 8px;
        margin-bottom: 6px;
        cursor: grab;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .kanban-task:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        border-color: #667eea;
    }

    .kanban-task:active {
        cursor: grabbing;
        transform: rotate(3deg) scale(1.02);
    }

    .kanban-task::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .kanban-task-content {
        padding: 12px;
    }

    .kanban-task-id {
        font-size: 10px;
        font-weight: 800;
        color: #667eea;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        background: rgba(102, 126, 234, 0.15);
        padding: 3px 6px;
        border-radius: 12px;
        display: inline-block;
    }

    .kanban-task-title {
        font-size: 16px;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.4;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .kanban-task-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #f1f3f4;
    }

    .kanban-priority {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .kanban-priority.high {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
    }

    .kanban-priority.medium {
        background: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%);
        box-shadow: 0 2px 8px rgba(254, 202, 87, 0.3);
    }

    .kanban-priority.low {
        background: linear-gradient(135deg, #48dbfb 0%, #0abde3 100%);
        box-shadow: 0 2px 8px rgba(72, 219, 251, 0.3);
    }

    .kanban-project {
        font-size: 11px;
        color: #6c757d;
        font-weight: 500;
        background: #f8f9fa;
        padding: 3px 6px;
        border-radius: 10px;
    }

    .dropzone {
        min-height: 30px;
        border: 2px dashed transparent;
        border-radius: 8px;
        margin: 4px 0;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        color: #adb5bd;
    }

    .dropzone.droppable {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        font-weight: 600;
    }

    .dropzone.droppable::before {
        content: '+ Drop task here';
    }

    /* Sprint selector styling */
    .kanban-sprint-selector {
        background: #ffffff;
        border: 2px solid #667eea;
        border-radius: 8px;
        padding: 8px 15px;
        font-weight: 500;
        font-size: 16px;
        color: #495057;
        transition: all 0.3s ease;
        min-width: 180px;
    }

    .kanban-sprint-selector:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        background: #ffffff;
        border-color: #5a67d8;
    }

    /* Page header improvements */
    .kanban-page-header {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px 25px;
        margin-bottom: 25px;
        border: 1px solid #dee2e6;
        position: relative;
        z-index: 10;
    }

    .kanban-page-title {
        color: #2c3e50;
        font-weight: 700;
        font-size: 24px;
        margin-bottom: 0;
    }

    /* Ensure breadcrumb links are clickable */
    .kanban-page-header .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .kanban-page-header .breadcrumb-item a {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
        position: relative;
        z-index: 20;
        padding: 5px 8px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .kanban-page-header .breadcrumb-item a:hover {
        background: rgba(102, 126, 234, 0.1);
        color: #5a67d8;
        text-decoration: none;
    }

    .kanban-page-header .breadcrumb-item.active a {
        color: #6c757d;
    }

    /* Fix header layout for sidebar expansion */
    .kanban-page-header .d-flex {
        position: relative;
        z-index: 15;
    }

    .kanban-page-header .breadcrumb {
        flex-wrap: wrap;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .kanban-column {
            margin: 0 5px 20px 5px;
        }
        
        .kanban-board {
            margin: 0 10px;
            padding: 15px;
        }
        
        .kanban-task:hover {
            transform: none;
        }

        .kanban-page-header {
            padding: 15px 20px;
        }

        .kanban-page-title {
            font-size: 20px;
        }

        .kanban-sprint-selector {
            min-width: 150px;
            font-size: 14px;
        }
    }

    @media (max-width: 576px) {
        .kanban-page-header .d-flex:first-child {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .kanban-page-header .breadcrumb {
            margin-top: 8px;
        }
    }

    /* Animation for task count */
    .task-count {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        margin-left: 6px;
    }
</style>

<section class="main_content dashboard_part large_header_bg kanban-container" style="background: #ffffff !important;">
    <?php require_once(APPPATH . 'Views/common/top-header.php'); ?>
    <div class="main_content_iner overly_inner" style="background: transparent !important; position: relative; z-index: 5;">
        <div class="container-fluid p-0">
            <!-- page title  -->

            <div class="row">
                <div class="col-12">
                    <div class="kanban-page-header">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-wrap">
                                <h3 class="kanban-page-title mr-3 mb-2 mb-md-0">🎯 Kanban Board</h3>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="/" style="position: relative; z-index: 25;">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        <a href="/<?php echo $tableName; ?>" style="position: relative; z-index: 25;">
                                            <?php echo ucfirst($tableName); ?>
                                        </a>
                                    </li>
                                </ol>
                            </div>

                            <div class="d-flex align-items-center mt-2 mt-md-0">
                                <div class="form-group mb-0">
                                    <?php
                                    $blank_item = array("id" => "", "sprint_name" => "🎯 Choose Sprint");
                                    array_unshift($sprints_list, $blank_item);
                                    ?>
                                    <select id="kanban_sprint" class="kanban-sprint-selector">
                                        <?php foreach ($sprints_list as $row) : ?>
                                            <option <?php echo (($_GET['sprint'] ?? "") == $row["id"] ?  "selected" : "") ?> value="<?php echo ($row["id"]) ?>"><?php echo ($row["sprint_name"]) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
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

                    <div class="kanban-board">