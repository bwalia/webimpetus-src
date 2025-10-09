<?php require_once(APPPATH . 'Views/common/list-title.php');
$status = ["Estimate", "Quote", "Ordered", "Acknowledged", "Authorised", "Delivered", "Completed", "Proforma Invoice"];
?>

<style>
    /* JIRA-style table enhancements */
    .white_card_body {
        padding: 20px;
    }

    .QA_table table,
    .dataTable {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
    }

    .QA_table thead th,
    .dataTable thead th {
        background: var(--gray-50, #f9fafb) !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.5px !important;
        color: var(--gray-600, #4b5563) !important;
        padding: 12px 16px !important;
        border-bottom: 2px solid var(--gray-200, #e5e7eb) !important;
    }

    .QA_table tbody td,
    .dataTable tbody td {
        padding: 12px 16px !important;
        font-size: 0.875rem !important;
        color: var(--gray-700, #374151) !important;
        border-bottom: 1px solid var(--gray-100, #f3f4f6) !important;
    }

    .QA_table tbody tr:hover,
    .dataTable tbody tr:hover {
        background: var(--gray-50, #f9fafb) !important;
        transition: background 0.2s ease;
    }

    .QA_table tbody tr:last-child td,
    .dataTable tbody tr:last-child td {
        border-bottom: none !important;
    }
</style>

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