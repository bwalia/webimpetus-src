<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="jobsTable"></div>
</div>
<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Title', 'Sub title', 'Status', 'Published at', 'Created at'];
    let columnsMachineName = ['id', 'title', 'sub_title', 'status', 'publish_date', 'created'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "jobs",
            apiPath: "jobs/jobsList",
            selector: "jobsTable"
        }
    );
</script>