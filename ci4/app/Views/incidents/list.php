<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
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
