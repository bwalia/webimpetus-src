<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
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