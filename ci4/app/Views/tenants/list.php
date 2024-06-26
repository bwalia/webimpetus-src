<?php require_once (APPPATH.'Views/common/list-title.php'); ?>
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