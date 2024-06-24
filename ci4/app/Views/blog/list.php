<?php require_once (APPPATH.'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table blogsTable" id="blogsTable"></div>
</div>
<?php require_once (APPPATH.'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Title', 'Sub Title', 'Status', 'Published at', 'Creted at'];
    let columnsMachineName = ['id', 'title', 'sub_title', 'status', 'publish_date', 'created'];
    initializeGridTable(
        {
            columnsTitle, 
            columnsMachineName, 
            tableName: "blog", 
            apiPath: "blog/blogsList", 
            selector: "blogsTable"
        }
    );
</script>