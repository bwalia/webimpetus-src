<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/products/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Product
        </a>
    </div>
</div>

<div class="white_card_body ">
    <div class="QA_table" id="productsTable"></div>
</div>
<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Name', 'Code', 'SKU', 'Availavle Stock', 'Unit Price'];
    let columnsMachineName = ['id', 'name', 'code', 'sku', 'stock_available', 'unit_price'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "products",
            apiPath: "products/productsList",
            selector: "productsTable"
        }
    );
</script>