<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<style>
    /* JIRA-style table enhancements */
    .white_card_body {
        padding: 20px;
    }

    /* Enhanced table styling */
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

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-file-invoice"></i> Total Outstanding</div>
            <div class="summary-card-value" id="totalOutstanding">£0.00</div>
            <div class="summary-card-subtitle">payable to suppliers</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-exclamation-circle"></i> Overdue</div>
            <div class="summary-card-value" id="overdueAmount">£0.00</div>
            <div class="summary-card-subtitle"><span id="overdueCount">0</span> invoices</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Paid This Month</div>
            <div class="summary-card-value" id="paidThisMonth">£0.00</div>
            <div class="summary-card-subtitle"><span id="paidThisMonthCount">0</span> invoices</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-clock"></i> Due This Week</div>
            <div class="summary-card-value" id="dueThisWeek">£0.00</div>
            <div class="summary-card-subtitle"><span id="dueThisWeekCount">0</span> invoices</div>
        </div>
    </div>
</div>

<!-- Purchase Invoices Table -->
<div class="white_card_body">
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

    // Load purchase invoice statistics
    function loadPurchaseInvoiceStatistics() {
        $.ajax({
            url: '/purchase_invoices/purchaseInvoicesList',
            method: 'GET',
            success: function(response) {
                if (response && response.data) {
                    const invoices = response.data;

                    // Calculate total outstanding
                    let totalOutstanding = 0;
                    invoices.forEach(inv => {
                        const balanceDue = parseFloat(inv.balance_due) || 0;
                        totalOutstanding += balanceDue;
                    });
                    $('#totalOutstanding').text('£' + totalOutstanding.toFixed(2));

                    // Calculate overdue amount and count
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    let overdueAmount = 0;
                    let overdueCount = 0;

                    invoices.forEach(inv => {
                        if (inv.date) {
                            const dueDate = new Date(inv.date);
                            const balanceDue = parseFloat(inv.balance_due) || 0;
                            if (dueDate < today && balanceDue > 0) {
                                overdueAmount += balanceDue;
                                overdueCount++;
                            }
                        }
                    });
                    $('#overdueAmount').text('£' + overdueAmount.toFixed(2));
                    $('#overdueCount').text(overdueCount);

                    // Paid this month
                    const currentMonth = new Date().getMonth();
                    const currentYear = new Date().getFullYear();
                    let paidThisMonth = 0;
                    let paidThisMonthCount = 0;

                    invoices.forEach(inv => {
                        const balanceDue = parseFloat(inv.balance_due) || 0;
                        const total = parseFloat(inv.total) || 0;

                        if (balanceDue === 0 && total > 0) {
                            // Check if paid this month (you may need to adjust based on your paid_date field)
                            if (inv.paid_date || inv.updated) {
                                const paidDate = new Date(inv.paid_date || inv.updated);
                                if (paidDate.getMonth() === currentMonth && paidDate.getFullYear() === currentYear) {
                                    paidThisMonth += total;
                                    paidThisMonthCount++;
                                }
                            }
                        }
                    });
                    $('#paidThisMonth').text('£' + paidThisMonth.toFixed(2));
                    $('#paidThisMonthCount').text(paidThisMonthCount);

                    // Due this week
                    const oneWeekFromNow = new Date();
                    oneWeekFromNow.setDate(today.getDate() + 7);
                    let dueThisWeek = 0;
                    let dueThisWeekCount = 0;

                    invoices.forEach(inv => {
                        if (inv.date) {
                            const dueDate = new Date(inv.date);
                            const balanceDue = parseFloat(inv.balance_due) || 0;
                            if (dueDate >= today && dueDate <= oneWeekFromNow && balanceDue > 0) {
                                dueThisWeek += balanceDue;
                                dueThisWeekCount++;
                            }
                        }
                    });
                    $('#dueThisWeek').text('£' + dueThisWeek.toFixed(2));
                    $('#dueThisWeekCount').text(dueThisWeekCount);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading purchase invoice statistics:', error);
            }
        });
    }

    // Load statistics on page load
    loadPurchaseInvoiceStatistics();
</script>