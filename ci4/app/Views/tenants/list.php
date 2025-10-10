<?php require_once (APPPATH.'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/tenants/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Tenant
        </a>
    </div>
</div>

<div class="white_card_body ">
    <div class="QA_table tenantTabls" id="tenantTabls"></div>
</div>
<?php require_once (APPPATH.'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Services', 'Name', 'Address', 'Contact Name', 'Contact Email', 'Note'];
    let columnsMachineName = ['id', 'service_name', 'name', 'address', 'contact_name', 'contact_email', 'notes'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "tenants",
            apiPath: "tenants/tenantsList",
            selector: "tenantTabls"
        }
    );
</script>