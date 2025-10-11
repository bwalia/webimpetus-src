<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/incidents/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Incident
        </a>
    </div>
</div>

<div class="white_card_body ">
    <div class="QA_table" id="incidentsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Incident #', 'Title', 'Priority', 'Status', 'Category', 'Created'];
    let columnsMachineName = ['id', 'incident_number', 'title', 'priority', 'status', 'category', 'created_at'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "incidents",
            apiPath: "incidents/incidentsList",
            selector: "incidentsTable"
        }
    );
</script>
