<?php require_once(APPPATH . 'Views/common/header.php'); ?>
<!-- main content part here -->
<?php require_once(APPPATH . 'Views/common/sidebar.php'); ?>
<style>
    /* Modern Scrum Board Styling */
    .scrum-container {
        background: #ffffff;
        min-height: 100vh;
        padding: 20px 0;
    }

    /* Sidebar responsiveness */
    .main_content.scrum-container {
        transition: padding-left 0.3s ease !important;
    }

    .scrum-board {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        padding: 20px;
    }

    .scrum-column {
        background: #f8f9fa;
        border-radius: 12px;
        min-height: 600px;
        margin: 0 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .scrum-column:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .scrum-column-header {
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

    .scrum-column-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: all 0.5s;
    }

    .scrum-column-header:hover::before {
        left: 100%;
    }

    /* Column-specific colors - Darker, more saturated */
    .scrum-column[data-category="backlog"] .scrum-column-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    }

    .scrum-column[data-category="sprint-ready"] .scrum-column-header {
        background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
    }

    .scrum-column[data-category="in-sprint"] .scrum-column-header {
        background: linear-gradient(135deg, #f59e0b 0%, #eab308 100%);
    }

    .scrum-column[data-category="completed"] .scrum-column-header {
        background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
    }

    .scrum-column-content {
        padding: 8px 10px;
        min-height: 520px;
    }

    .scrum-task {
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

    .scrum-task:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        border-color: #667eea;
    }

    .scrum-task:active {
        cursor: grabbing;
        transform: rotate(3deg) scale(1.02);
    }

    .scrum-task::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .scrum-task-content {
        padding: 12px;
    }

    .scrum-task-id {
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

    .scrum-task-title {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        line-height: 1.3;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .scrum-task-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #f1f3f4;
    }

    .scrum-priority {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .scrum-priority.high {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
    }

    .scrum-priority.medium {
        background: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%);
        box-shadow: 0 2px 8px rgba(254, 202, 87, 0.3);
    }

    .scrum-priority.low {
        background: linear-gradient(135deg, #48dbfb 0%, #0abde3 100%);
        box-shadow: 0 2px 8px rgba(72, 219, 251, 0.3);
    }

    .scrum-sprint {
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
    .scrum-sprint-selector {
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

    .scrum-sprint-selector:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        background: #ffffff;
        border-color: #5a67d8;
    }

    /* Page header improvements */
    .scrum-page-header {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }

    .scrum-page-title {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    /* Responsive design */
    @media (max-width: 991px) {
        .scrum-board {
            padding: 15px;
        }

        .scrum-column {
            margin-bottom: 20px;
        }

        .scrum-sprint-selector {
            width: 100%;
            margin-top: 10px;
        }
    }

    /* Task count badge */
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

    /* Story points badge */
    .story-points {
        position: absolute;
        top: 8px;
        right: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 12px;
    }

    /* Sprint info card */
    .sprint-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .sprint-info h4 {
        color: white;
        margin: 0 0 5px 0;
        font-size: 18px;
        font-weight: 700;
    }

    .sprint-info p {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
    }
</style>

<section class="main_content dashboard_part large_header_bg scrum-container" style="background: #ffffff !important;">
    <?php require_once(APPPATH . 'Views/common/top-header.php'); ?>
    <div class="main_content_iner overly_inner" style="background: transparent !important; position: relative; z-index: 5;">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="scrum-page-header">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-wrap">
                                <h3 class="scrum-page-title mr-3 mb-2 mb-md-0">📋 Scrum Board</h3>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="/" style="position: relative; z-index: 25;">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        <a href="/scrum_board" style="position: relative; z-index: 25;">
                                            Scrum Board
                                        </a>
                                    </li>
                                </ol>
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <div class="mr-2">
                                    <?php
                                    $blank_item = array("id" => "", "sprint_name" => "🎯 Choose Sprint");
                                    array_unshift($sprints_list, $blank_item);
                                    ?>
                                    <select id="scrum_sprint" class="scrum-sprint-selector">
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

            <?php if (!empty($selected_sprint)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="sprint-info">
                        <h4>🚀 <?php echo htmlspecialchars($selected_sprint['sprint_name']); ?></h4>
                        <p>Sprint Goal: Plan, develop, and deliver high-value features</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-12">
                    <?php if (session()->has('message')) { ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> <?php echo session('message'); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php } ?>

                    <div class="scrum-board">
                        <?php require_once(APPPATH . 'Views/scrum_board/board-content.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    var base_url = '<?php echo base_url('/scrum_board') ?>';
    $(document).ready(function () {
        $("#scrum_sprint").on("change", function (e) {
            var redirect_to = base_url;
            if ($(this).val() != "") {
                redirect_to = base_url + "?sprint=" + $(this).val();
            }
            window.location.replace(redirect_to);
        });
    });

    function gotoTask(task_id) {
        window.location.href = '/tasks/edit/'+task_id
    }
    
    // Ensure scrum container responds to sidebar toggle
    $(document).ready(function() {
        $(".open_miniSide").on("click", function() {
            setTimeout(function() {
                if ($(".sidebar").hasClass("mini_sidebar")) {
                    $(".scrum-container").addClass("full_main_content");
                } else {
                    $(".scrum-container").removeClass("full_main_content");
                }
            }, 50);
        });
        
        if ($(".sidebar").hasClass("mini_sidebar")) {
            $(".scrum-container").addClass("full_main_content");
        }
    });
</script>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
