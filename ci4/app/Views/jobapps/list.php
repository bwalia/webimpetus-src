

<?php require_once (APPPATH.'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table jobAppsTable" id="jobAppsTable"></div>
</div>
<?php require_once (APPPATH.'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Title', 'Sub Title', 'Status', 'Published at', 'Created at'];
    let columnsMachineName = ['id', 'title', 'sub_title', 'status', 'publish_date', 'created'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "content_list",
            apiPath: "jobapps/jobAppList",
            selector: "jobAppsTable"
        }
    );
</script>