<?php require_once (APPPATH . 'Views/tasks/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="tasksTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Task Title', 'Project', 'Start Date', 'End Date', 'Est. Hours', 'Rate', 'Active', 'Status'];
    let columnsMachineName = ['id', 'name', 'project_name', 'start_date', 'end_date', 'estimated_hour', 'rate', 'active', 'status'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "tasks",
            apiPath: "tasks/tasksList",
            selector: "tasksTable"
        }
    );
    var base_url = '<?php echo base_url('/tasks') ?>';
    $(document).ready(function () {
        $("#task_status").on("change", function (e) {
            var redirect_to = base_url;
            if ($(this).val() != "") {
                redirect_to = base_url + "?status=" + $(this).val();
            }
            window.location.replace(redirect_to);
        });
    });
</script>