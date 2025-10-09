<?php require_once (APPPATH.'Views/common/list-title.php');
$status = ["Estimate", "Quote","Ordered","Acknowledged","Authorised","Delivered","Completed","Proforma Invoice"];
?>
<style>



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

    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        text-transform: capitalize;
    }

    .status-estimate {
        background-color: #e5e7eb;
        color: #374151;
    }

    .status-quote {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .status-ordered {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-completed {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-delivered {
        background-color: #d1fae5;
        color: #065f46;
    }

    .amount-cell {
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }

    .order-link {
        color: #667eea;
        font-weight: 600;
        text-decoration: none;
    }

    .order-link:hover {
        text-decoration: underline;
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

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-clipboard-list"></i> Total Orders</div>
            <div class="summary-card-value" id="totalOrders">0</div>
            <div class="summary-card-subtitle">all work orders</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-hourglass-half"></i> In Progress</div>
            <div class="summary-card-value" id="inProgressCount">0</div>
            <div class="summary-card-subtitle">active orders</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Completed</div>
            <div class="summary-card-value" id="completedCount">0</div>
            <div class="summary-card-subtitle">this month</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-file-invoice-dollar"></i> Total Value</div>
            <div class="summary-card-value" id="totalValue">£0</div>
            <div class="summary-card-subtitle">all orders</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="/work_orders/edit" class="quick-action-btn success">
        <i class="fa fa-plus"></i> New Work Order
    </a>
    <button class="quick-action-btn primary" onclick="window.location.reload()">
        <i class="fa fa-sync"></i> Refresh
    </button>
</div>

<div class="white_card_body">
    <div class="QA_table workOrderTable" id="workOrderTable"></div>
</div>

<?php require_once (APPPATH.'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Order #', 'Customer', 'Date', 'Project Code', 'Total', 'Balance Due', 'Status'];
    let columnsMachineName = ['id', 'order_number', 'company_name', 'date', 'project_code', 'total', 'balance_due', 'status'];

    // Custom column renderers
    const columnRenderers = {
        order_number: function(data, type, row) {
            const orderNum = row.custom_order_number || data;
            return '<a href="/work_orders/edit/' + row.uuid + '" class="order-link">#' + orderNum + '</a>';
        },
        date: function(data, type, row) {
            if (!data) return '-';
            const date = new Date(parseInt(data) * 1000);
            const options = { day: '2-digit', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('en-GB', options);
        },
        total: function(data, type, row) {
            const amount = parseFloat(data || 0);
            return '<span class="amount-cell">£' + amount.toFixed(2) + '</span>';
        },
        balance_due: function(data, type, row) {
            const amount = parseFloat(data || 0);
            return '<span class="amount-cell">£' + amount.toFixed(2) + '</span>';
        },
        status: function(data, type, row) {
            let badgeClass = 'status-estimate';
            const status = (data || 'Estimate').toLowerCase();

            if (status.includes('completed') || status.includes('delivered')) {
                badgeClass = 'status-completed';
            } else if (status.includes('quote')) {
                badgeClass = 'status-quote';
            } else if (status.includes('ordered') || status.includes('authorised')) {
                badgeClass = 'status-ordered';
            }

            return '<span class="status-badge ' + badgeClass + '">' + (data || 'Estimate') + '</span>';
        }
    };

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "work_orders",
        apiPath: "api/v2/work_orders",
        selector: "workOrderTable",
        columnRenderers: columnRenderers
    });

    // Update summary cards
    function updateWorkOrderSummaryCards() {
        const businessUuid = '<?php echo session('uuid_business'); ?>';
        const token = '<?php echo session('token') ?? ''; ?>';

        fetch('/api/v2/work_orders?uuid_business_id=' + businessUuid, {
            headers: {
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result && result.data) {
                calculateWorkOrderMetrics(result.data);
            }
        })
        .catch(error => {
            console.error('Error fetching work order summary:', error);
        });
    }

    function calculateWorkOrderMetrics(orders) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const weekStart = new Date(today);
        weekStart.setDate(today.getDate() - today.getDay());

        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);

        let totalCount = orders.length;
        let inProgressCount = 0;
        let completedCount = 0;
        let quotesCount = 0;
        let thisWeekCount = 0;
        let totalValue = 0;

        orders.forEach(function(order) {
            const status = (order.status || '').toLowerCase();
            const total = parseFloat(order.total || 0);
            const orderDate = order.date ? new Date(parseInt(order.date) * 1000) : null;

            // Total value
            totalValue += total;

            // In progress (ordered, acknowledged, authorised, delivered)
            if (status.includes('ordered') || status.includes('acknowledged') ||
                status.includes('authorised') || status.includes('delivered')) {
                inProgressCount++;
            }

            // Completed this month
            if (status.includes('completed') && orderDate && orderDate >= monthStart) {
                completedCount++;
            }

            // Quotes
            if (status.includes('quote') || status.includes('estimate')) {
                quotesCount++;
            }

            // This week
            if (orderDate && orderDate >= weekStart) {
                thisWeekCount++;
            }
        });

        // Update cards
        $('#totalOrders').text(totalCount);
        $('#inProgressCount').text(inProgressCount);
        $('#completedCount').text(completedCount);
        $('#totalValue').text('£' + totalValue.toFixed(0));
        $('#quotesCount').text(quotesCount);
        $('#thisWeekCount').text(thisWeekCount);

        console.log('Work order metrics updated:', {
            total: totalCount,
            inProgress: inProgressCount,
            completed: completedCount,
            totalValue: totalValue,
            quotes: quotesCount,
            thisWeek: thisWeekCount
        });
    }

    $(document).ready(function() {
        setTimeout(function() {
            updateWorkOrderSummaryCards();
        }, 1000);
    });
</script>
