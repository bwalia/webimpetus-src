<?php require_once (APPPATH.'Views/common/list-title.php'); 
$status = ["Estimate", "Quote","Ordered","Acknowledged","Authorised","Delivered","Completed","Proforma Invoice"];
?>
    <div class="white_card_body">
        <div class="QA_table workOrderTable" id="workOrderTable"></div>
    </div>

<?php require_once (APPPATH.'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Order Number', 'Due Date', 'Project Code', 'Total Paid', 'Balance Outstanding', 'Paid Date', 'Status'];
    let columnsMachineName = ['id', 'order_numner', 'date', 'project_code', 'total', 'balance_due', 'paid_date', 'status'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "work_orders",
            apiPath: "api/v2/work_orders",
            selector: "workOrderTable"
        }
    );
</script>