<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="enquiriesTable"></div>
</div>
<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Name', 'Email', 'Phone', 'Created at'];
    let columnsMachineName = ['id', 'name', 'email', 'phone', 'created'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "enquiries",
            apiPath: "enquiries/enquiriesList",
            selector: "enquiriesTable"
        }
    );
</script>