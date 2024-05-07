<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="sales_invoicesTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
        let columnsTitle = ['Id', 'Invoice Number', 'Supplier', 'Project Code', 'Total Paid', 'Balance Due',  'Status'];
    let columnsMachineName = ['id', 'invoice_number', 'company_name', 'project_code', 'total', 'balance_due', 'status'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "sales_invoices",
            apiPath: "api/v2/sales_invoices",
            selector: "sales_invoicesTable"
        }
    );
</script>