<?php require_once (APPPATH . 'Views/timeslips/list-title.php'); ?>
<style>
    /* Timeslips JIRA-style enhancements */
    .column-wrap {
        width: 1%;
        white-space: nowrap;
    }

    /* Filter section styling */
    .white_card_header {
        background: var(--bg-primary, #ffffff);
        padding: 20px;
        border-bottom: 2px solid var(--gray-100, #f3f4f6);
    }

    .white_card_header .form-control {
        border: 1px solid var(--border-medium, #d1d5db);
        border-radius: var(--radius-md, 8px);
        padding: 8px 12px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .white_card_header .form-control:focus {
        outline: none;
        border-color: var(--primary, #667eea);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    }

    /* Total records badge */
    .page_title_right span#total {
        background: var(--primary, #667eea);
        color: white;
        padding: 4px 12px;
        border-radius: var(--radius-md, 8px);
        font-weight: 700;
        margin-left: 8px;
    }

    /* DataTable wrapper */
    #tableWrapper {
        padding: 20px;
    }

    /* Enhanced table styling */
    .QA_table table {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .QA_table thead th {
        background: var(--gray-50, #f9fafb) !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.5px !important;
        color: var(--gray-600, #4b5563) !important;
        padding: 12px 16px !important;
    }

    .QA_table tbody td {
        padding: 12px 16px !important;
        font-size: 0.875rem !important;
        color: var(--gray-700, #374151) !important;
    }

    .QA_table tbody tr:hover {
        background: var(--gray-50, #f9fafb) !important;
    }
</style>
<div class="white_card_body" id="tableWrapper">
    <div class="QA_table" id="timeslipsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/timeslips/footer.php'); ?>
<script>

    let columnsTitle = ['Id', 'Week No.', 'Task', 'Employee', 'Start Date', 'Start Time'];
    let columnsMachineName = ['id', 'week_no', 'taskName', 'employeeName', 'slip_start_date', 'slip_timer_started'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "timeslips",
            apiPath: "api/v2/timeslips",
            selector: "timeslipsTable"
        }
    );

    $("#list_week").on('change', function () {
        let listMonth = $("#list_monthpicker2").val();
        let listYear = $("#list_yearpicker2").val();
        $("#timeslipsTable").remove("");
        $("#tableWrapper").html('<div class="QA_table" id="timeslipsTable"></div>');
        window.sessionStorage.setItem("listWeek", $(this).val());
        setTimeout(() => {
            initializeGridTable(
                {
                    columnsTitle,
                    columnsMachineName,
                    tableName: "timeslips",
                    apiPath: "api/v2/timeslips",
                    selector: "timeslipsTable",
                    listWeek: $(this).val(),
                    listMonth: listMonth,
                    listYear: listYear
                }
            );
        }, 600);
    });

    $("#list_monthpicker2").on('change', function () {
        let listWeek = $("#list_week").val() || "";
        let listYear = $("#list_yearpicker2").val();
        $("#timeslipsTable").remove("");
        $("#tableWrapper").html('<div class="QA_table" id="timeslipsTable"></div>');
        window.sessionStorage.setItem("listMonth", $(this).val());
        setTimeout(() => {
            initializeGridTable(
                {
                    columnsTitle,
                    columnsMachineName,
                    tableName: "timeslips",
                    apiPath: "api/v2/timeslips",
                    selector: "timeslipsTable",
                    listWeek: listWeek,
                    listMonth: $(this).val(),
                    listYear: listYear
                }
            );
        }, 600);
    });

    $("#list_yearpicker2").on('change', function () {
        let listWeek = $("#list_week").val() || "";
        let listMonth = $("#list_monthpicker2").val();
        $("#timeslipsTable").remove("");
        $("#tableWrapper").html('<div class="QA_table" id="timeslipsTable"></div>');
        window.sessionStorage.setItem("listYear", $(this).val());
        setTimeout(() => {
            initializeGridTable(
                {
                    columnsTitle,
                    columnsMachineName,
                    tableName: "timeslips",
                    apiPath: "api/v2/timeslips",
                    selector: "timeslipsTable",
                    listWeek: listWeek,
                    listMonth: listMonth,
                    listYear: $(this).val()
                }
            );
        }, 600);
    });
    let listMonth = window.sessionStorage.getItem("listMonth");
    let listYear = window.sessionStorage.getItem("listYear");
    console.log({listMonth});
    $('#list_monthpicker2 option[value="'+ listMonth +'"]').attr("selected",true);
    $('#list_yearpicker2 option[value="'+ listYear +'"]').attr("selected",true);
</script>