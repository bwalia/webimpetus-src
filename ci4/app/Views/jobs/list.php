<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/jobs/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Job
        </a>
    </div>
</div>

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