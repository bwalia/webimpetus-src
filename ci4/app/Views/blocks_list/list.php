<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/blocks/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Block
        </a>
    </div>
</div>

<div class="white_card_body ">
    <div class="QA_table" id="blocksTable"></div>
</div>
<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Title', 'Status', 'Code'];
    let columnsMachineName = ['id', 'title', 'status', 'code'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "blocks",
            apiPath: "api/v2/blocks",
            selector: "blocksTable"
        }
    );
</script>