<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<!-- start section for body -->
<div class="white_card_body ">
    <div class="QA_table" id="categoriesTable"></div>
</div>
<!-- end section for body -->
<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Name', 'Sort Order', 'Note'];
    let columnsMachineName = ['id', 'name', 'sort_order', 'note'];
    initializeGridTable(
        {
            columnsTitle, 
            columnsMachineName, 
            tableName: "categories", 
            apiPath: "categories/categoriesList", 
            selector: "categoriesTable"
        }
    );
</script>