<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/categories/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Category
        </a>
    </div>
</div>

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