<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="templatesTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Code', 'Subject'];
    let columnsMachineName = ['id', 'code', 'subject'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "templates",
            apiPath: "templates/templateList",
            selector: "templatesTable"
        }
    );
</script>