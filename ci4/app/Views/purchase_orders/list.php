<?php require_once(APPPATH . 'Views/common/list-title.php');
$status = ["Estimate", "Quote", "Ordered", "Acknowledged", "Authorised", "Delivered", "Completed", "Proforma Invoice"];
?>
<div class="white_card_body ">
    <div class="QA_table purchaseOrderTable" id="purchaseOrderTable"></div>
</div>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Order Number', 'Due Date', 'Client', 'Project Code', 'Total Paid', 'Balance Outstanding', 'Paid Date', 'Status'];
    let columnsMachineName = ['id', 'order_number', 'date', 'company_name', 'project_code', 'total', 'balance_due', 'paid_date', 'status'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "purchase_orders",
            apiPath: "api/v2/purchase_orders",
            selector: "purchaseOrderTable"
        }
    );
</script>