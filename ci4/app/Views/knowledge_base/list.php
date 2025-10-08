<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="knowledgeBaseTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Article #', 'Title', 'Category', 'Status', 'Views', 'Helpful', 'Created'];
    let columnsMachineName = ['id', 'article_number', 'title', 'category', 'status', 'view_count', 'helpful_count', 'created_at'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "knowledge_base",
            apiPath: "knowledge_base/knowledgeBaseList",
            selector: "knowledgeBaseTable"
        }
    );
</script>
