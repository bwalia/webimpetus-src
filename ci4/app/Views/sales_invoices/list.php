<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<style>
    .column-wrap {
        width: 1%;
        white-space: nowrap;
    }



    .quick-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .quick-action-btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        text-decoration: none;
        display: inline-block;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .quick-action-btn.primary {
        background-color: #667eea;
        color: white;
    }

    .quick-action-btn.success {
        background-color: #10b981;
        color: white;
    }

    .quick-action-btn.warning {
        background-color: #f59e0b;
        color: white;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        text-transform: capitalize;
    }

    .status-paid {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-invoiced,
    .status-sent {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .status-overdue {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .status-partial {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-draft {
        background-color: #e5e7eb;
        color: #374151;
    }

    .aging-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 5px;
    }

    .aging-current {
        background-color: #10b981;
    }

    .aging-30 {
        background-color: #f59e0b;
    }

    .aging-60 {
        background-color: #ef4444;
    }

    .aging-90 {
        background-color: #7c3aed;
    }

    .amount-cell {
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }

    .overdue-amount {
        color: #dc2626;
    }

    .paid-amount {
        color: #059669;
    }

    /* JIRA-style table enhancements */
    .white_card_header {
        background: var(--bg-primary, #ffffff);
        padding: 20px;
        border-bottom: 2px solid var(--gray-100, #f3f4f6);
    }

    .white_card_header .form-control {
        border: 1px solid var(--border-medium, #d1d5db);
        border-radius: var(--radius-md, 8px);
        padding: 8px 12px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .white_card_header .form-control:focus {
        outline: none;
        border-color: var(--primary, #667eea);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    }

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

<!-- Summary Cards for Credit Controller -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/sales_invoices/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Invoice
        </a>
    </div>
</div>

<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-file-invoice-dollar"></i> Total Outstanding</div>
            <div class="summary-card-value" id="totalOutstanding">£0.00</div>
            <div class="summary-card-subtitle">balance due</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-exclamation-triangle"></i> Overdue</div>
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

<div class="white_card_body">
    <div class="QA_table" id="sales_invoicesTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Invoice #', 'Customer', 'Invoice Date', 'Due Date', 'Total', 'Paid', 'Balance Due', 'Status', 'Aging'];
    let columnsMachineName = ['id', 'invoice_number', 'company_name', 'date', 'due_date', 'total', 'total_paid', 'balance_due', 'status', 'aging'];

    // Custom column renderers for credit controller view
    const columnRenderers = {
        invoice_number: function(data, type, row) {
            const customNumber = row.custom_invoice_number || data;
            return '<strong style="color: #667eea;">' + customNumber + '</strong>';
        },
        date: function(data, type, row) {
            if (!data) return '-';
            const date = new Date(parseInt(data) * 1000);
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };
            return date.toLocaleDateString('en-GB', options);
        },
        due_date: function(data, type, row) {
            if (!data) return '-';
            const dueDate = new Date(parseInt(data) * 1000);
            const today = new Date();
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };

            let dateStr = dueDate.toLocaleDateString('en-GB', options);

            // Highlight if overdue
            if (row.status !== 'Paid' && dueDate < today) {
                dateStr = '<span style="color: #dc2626; font-weight: 600;">' + dateStr + '</span>';
            }

            return dateStr;
        },
        total: function(data, type, row) {
            const amount = parseFloat(data || 0);
            return '<span class="amount-cell">£' + amount.toFixed(2) + '</span>';
        },
        total_paid: function(data, type, row) {
            const amount = parseFloat(data || 0);
            return '<span class="amount-cell paid-amount">£' + amount.toFixed(2) + '</span>';
        },
        balance_due: function(data, type, row) {
            const amount = parseFloat(data || 0);
            let className = 'amount-cell';

            // Highlight overdue amounts
            if (amount > 0 && row.status !== 'Paid') {
                const dueDate = new Date(parseInt(row.due_date) * 1000);
                const today = new Date();
                if (dueDate < today) {
                    className += ' overdue-amount';
                }
            }

            return '<span class="' + className + '">£' + amount.toFixed(2) + '</span>';
        },
        status: function(data, type, row) {
            let badgeClass = 'status-invoiced';
            let statusText = data || 'Draft';

            if (statusText.toLowerCase() === 'paid') {
                badgeClass = 'status-paid';
            } else if (statusText.toLowerCase() === 'draft') {
                badgeClass = 'status-draft';
            } else if (statusText.toLowerCase() === 'partial') {
                badgeClass = 'status-partial';
            } else {
                // Check if overdue
                const dueDate = new Date(parseInt(row.due_date) * 1000);
                const today = new Date();
                if (dueDate < today && statusText.toLowerCase() !== 'paid') {
                    badgeClass = 'status-overdue';
                    statusText = 'Overdue';
                }
            }

            return '<span class="status-badge ' + badgeClass + '">' + statusText + '</span>';
        },
        aging: function(data, type, row) {
            if (row.status && row.status.toLowerCase() === 'paid') {
                return '<span class="aging-indicator aging-current"></span>Paid';
            }

            if (!row.due_date) return '-';

            const dueDate = new Date(parseInt(row.due_date) * 1000);
            const today = new Date();
            const daysOverdue = Math.floor((today - dueDate) / (1000 * 60 * 60 * 24));

            if (daysOverdue < 0) {
                return '<span class="aging-indicator aging-current"></span>Current';
            } else if (daysOverdue < 30) {
                return '<span class="aging-indicator aging-30"></span>0-30 days';
            } else if (daysOverdue < 60) {
                return '<span class="aging-indicator aging-30"></span>30-60 days';
            } else if (daysOverdue < 90) {
                return '<span class="aging-indicator aging-60"></span>60-90 days';
            } else {
                return '<span class="aging-indicator aging-90"></span>90+ days';
            }
        }
    };

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "sales_invoices",
        apiPath: "api/v2/sales_invoices",
        selector: "sales_invoicesTable",
        columnRenderers: columnRenderers
    });

    // Update summary cards with credit controller metrics
    function updateSalesInvoiceSummaryCards() {
        const businessUuid = '<?php echo session('uuid_business'); ?>';
        const token = '<?php echo session('token') ?? ''; ?>';

        // Fetch invoices data and calculate summaries
        fetch('/api/v2/sales_invoices?uuid_business_id=' + businessUuid, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result && result.data) {
                    calculateCreditControllerMetrics(result.data);
                }
            })
            .catch(error => {
                console.error('Error fetching sales invoice summary data:', error);
            });
    }

    function calculateCreditControllerMetrics(invoices) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const weekEnd = new Date(today);
        weekEnd.setDate(today.getDate() + 7);

        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);

        let totalOutstanding = 0;
        let overdueAmount = 0;
        let overdueCount = 0;
        let dueThisWeek = 0;
        let dueThisWeekCount = 0;
        let paidThisMonth = 0;
        let paidThisMonthCount = 0;
        let aged90Plus = 0;
        let aged90PlusCount = 0;

        let totalDaysToPay = 0;
        let paidInvoicesCount = 0;

        invoices.forEach(function(invoice) {
            const balanceDue = parseFloat(invoice.balance_due || 0);
            const total = parseFloat(invoice.total || 0);
            const totalPaid = parseFloat(invoice.total_paid || 0);
            const status = (invoice.status || '').toLowerCase();
            const dueDate = invoice.due_date ? new Date(parseInt(invoice.due_date) * 1000) : null;
            const paidDate = invoice.paid_date ? new Date(parseInt(invoice.paid_date) * 1000) : null;
            const invoiceDate = invoice.date ? new Date(parseInt(invoice.date) * 1000) : null;

            // Total outstanding (all unpaid balances)
            if (status !== 'paid' && balanceDue > 0) {
                totalOutstanding += balanceDue;
            }

            // Overdue invoices
            if (dueDate && dueDate < today && status !== 'paid' && balanceDue > 0) {
                overdueAmount += balanceDue;
                overdueCount++;

                // 90+ days aging
                const daysOverdue = Math.floor((today - dueDate) / (1000 * 60 * 60 * 24));
                if (daysOverdue >= 90) {
                    aged90Plus += balanceDue;
                    aged90PlusCount++;
                }
            }

            // Due this week
            if (dueDate && dueDate >= today && dueDate <= weekEnd && status !== 'paid' && balanceDue > 0) {
                dueThisWeek += balanceDue;
                dueThisWeekCount++;
            }

            // Paid this month
            if (status === 'paid' && paidDate && paidDate >= monthStart) {
                paidThisMonth += total;
                paidThisMonthCount++;
            }

            // Calculate days to pay for paid invoices
            if (status === 'paid' && paidDate && invoiceDate) {
                const daysToPay = Math.floor((paidDate - invoiceDate) / (1000 * 60 * 60 * 24));
                if (daysToPay >= 0) {
                    totalDaysToPay += daysToPay;
                    paidInvoicesCount++;
                }
            }
        });

        // Calculate average days to pay
        const avgDaysToPay = paidInvoicesCount > 0 ? Math.round(totalDaysToPay / paidInvoicesCount) : 0;

        // Update summary cards
        $('#totalOutstanding').text('£' + totalOutstanding.toFixed(2));
        $('#overdueAmount').text('£' + overdueAmount.toFixed(2));
        $('#overdueCount').text(overdueCount);
        $('#dueThisWeek').text('£' + dueThisWeek.toFixed(2));
        $('#dueThisWeekCount').text(dueThisWeekCount);
        $('#paidThisMonth').text('£' + paidThisMonth.toFixed(2));
        $('#paidThisMonthCount').text(paidThisMonthCount);
        $('#avgDaysToPay').text(avgDaysToPay);
        $('#aged90Plus').text('£' + aged90Plus.toFixed(2));
        $('#aged90PlusCount').text(aged90PlusCount);

        console.log('Credit Controller metrics updated:', {
            totalOutstanding: totalOutstanding,
            overdueAmount: overdueAmount,
            overdueCount: overdueCount,
            dueThisWeek: dueThisWeek,
            paidThisMonth: paidThisMonth,
            avgDaysToPay: avgDaysToPay,
            aged90Plus: aged90Plus
        });
    }

    // Filter to show overdue invoices only
    function showOverdueOnly() {
        // This would require DataTables API integration
        alert('Filter functionality - would filter table to show only overdue invoices');
    }

    // Update summary cards on page load
    $(document).ready(function() {
        setTimeout(function() {
            updateSalesInvoiceSummaryCards();
        }, 1000);
    });
</script>
