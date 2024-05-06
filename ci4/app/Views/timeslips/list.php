<?php require_once (APPPATH . 'Views/timeslips/list-title.php'); ?>
<style>
    .column-wrap {
        width: 1%;
        white-space: nowrap;
    }
</style>
<div class="white_card_body ">
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
        console.log({vvv: $(this).val()});
        $("#timeslipsTable").html("");
        setTimeout(() => {
            initializeGridTable(
                {
                    columnsTitle,
                    columnsMachineName,
                    tableName: "timeslips",
                    apiPath: "api/v2/timeslips",
                    selector: "timeslipsTable",
                    listWeek: $(this).val()
    
                }
            );
        }, 1000);
    })
</script>