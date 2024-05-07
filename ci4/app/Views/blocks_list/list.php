<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
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