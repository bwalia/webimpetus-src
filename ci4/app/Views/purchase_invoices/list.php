<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="purchaseInvoicesTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script>
        let columnsTitle = ['Id', 'Invoice Number', 'Due Date', 'Supplier', 'Project Code', 'Total Paid', 'Balance Outstanding', 'Status'];
    let columnsMachineName = ['id', 'invoice_number', 'date', 'company_name', 'project_code', 'total', 'balance_due', 'status'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "purchase_invoices",
            apiPath: "purchase_invoices/purchaseInvoicesList",
            selector: "purchaseInvoicesTable"
        }
    );
</script>