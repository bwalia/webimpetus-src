<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table table-responsive" id="sprintsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Sprint Name', 'Start Date', 'End Date', 'Note'];
    let columnsMachineName = ['id', 'sprint_name', 'start_date', 'end_date', 'note'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "sprints",
            apiPath: "api/v2/sprints",
            selector: "sprintsTable"
        }
    );
</script>