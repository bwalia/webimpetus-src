<?php require_once (APPPATH . 'Views/timeslips/list-title.php'); ?>
<style>
    .column-wrap {
        width: 1%;
        white-space: nowrap;
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