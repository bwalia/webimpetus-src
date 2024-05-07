<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<div class="white_card_body ">
    <div class="QA_table" id="webpagesTable"></div>
</div>
<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Title', 'Sub Title', 'Status', 'Code'];
    let columnsMachineName = ['id', 'title', 'sub_title', 'status', 'code'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "webpages",
            apiPath: "api/v2/webpages",
            selector: "webpagesTable"
        }
    );
</script>