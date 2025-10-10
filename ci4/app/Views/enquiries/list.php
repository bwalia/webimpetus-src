<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/enquiries/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Enquiry
        </a>
    </div>
</div>

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