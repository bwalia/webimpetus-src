<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="projectsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Project', 'Budget', 'Rate', 'Currency', 'Start Date', 'Active/Completed'];
    let columnsMachineName = ['id', 'name', 'budget', 'rate', 'currency', 'start_date', 'active'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "projects",
            apiPath: "projects/projectsList",
            selector: "projectsTable"
        }
    );
</script>