<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/knowledge_base/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Article
        </a>
    </div>
</div>

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
