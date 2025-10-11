<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<style>
    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        text-transform: capitalize;
    }

    .status-draft {
        background-color: #e5e7eb;
        color: #374151;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-cleared {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-cancelled {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .amount-cell {
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }

    .posted-badge {
        background-color: #dbeafe;
        color: #1e40af;
        padding: 2px 8px;
        border-radius: 8px;
        font-size: 0.7rem;
    }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/receipts/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Receipt
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-receipt"></i> Total Receipts</div>
            <div class="summary-card-value" id="totalReceipts">£0.00</div>
            <div class="summary-card-subtitle">this month</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-clock"></i> Pending</div>
            <div class="summary-card-value" id="pendingReceipts">£0.00</div>
            <div class="summary-card-subtitle"><span id="pendingCount">0</span> receipts</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Cleared</div>
            <div class="summary-card-value" id="clearedReceipts">£0.00</div>
            <div class="summary-card-subtitle"><span id="clearedCount">0</span> receipts</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-calendar"></i> This Year</div>
            <div class="summary-card-value" id="yearReceipts">£0.00</div>
            <div class="summary-card-subtitle">total received</div>
        </div>
    </div>
</div>

<div class="white_card_body">
    <div class="QA_table" id="receiptsTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Receipt #', 'Date', 'Payer', 'Type', 'Amount', 'Method', 'Bank Account', 'Status', 'Posted'];
    let columnsMachineName = ['id', 'receipt_number', 'receipt_date', 'payer_name', 'receipt_type', 'amount', 'payment_method', 'bank_account_name', 'status', 'is_posted'];

    // Custom column renderers
    const columnRenderers = {
        receipt_number: function(data, type, row) {
            return '<strong style="color: #667eea;">' + data + '</strong>';
        },
        receipt_date: function(data, type, row) {
            if (!data) return '-';
            const date = new Date(data);
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };
            return date.toLocaleDateString('en-GB', options);
        },
        amount: function(data, type, row) {
            const amount = parseFloat(data || 0);
            const currency = row.currency || 'GBP';
            return '<span class="amount-cell">' + getCurrencySymbol(currency) + amount.toFixed(2) + '</span>';
        },
        status: function(data, type, row) {
            let badgeClass = 'status-draft';
            let statusText = data || 'Draft';

            if (statusText.toLowerCase() === 'cleared') {
                badgeClass = 'status-cleared';
            } else if (statusText.toLowerCase() === 'pending') {
                badgeClass = 'status-pending';
            } else if (statusText.toLowerCase() === 'cancelled') {
                badgeClass = 'status-cancelled';
            }

            return '<span class="status-badge ' + badgeClass + '">' + statusText + '</span>';
        },
        is_posted: function(data, type, row) {
            if (data == 1) {
                return '<span class="posted-badge"><i class="fa fa-check"></i> Posted</span>';
            }
            return '<span style="color: #9ca3af;">Not Posted</span>';
        },
        bank_account_name: function(data, type, row) {
            return data || '-';
        }
    };

    function getCurrencySymbol(currency) {
        const symbols = {
            'GBP': '£',
            'USD': '$',
            'EUR': '€',
            'INR': '₹'
        };
        return symbols[currency] || currency + ' ';
    }

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "receipts",
        apiPath: "api/v2/receipts",
        selector: "receiptsTable",
        columnRenderers: columnRenderers
    });

    // Update summary cards
    function updateReceiptsSummaryCards() {
        const businessUuid = '<?php echo session('uuid_business'); ?>';
        const token = '<?php echo session('token') ?? ''; ?>';

        fetch('/api/v2/receipts?uuid_business_id=' + businessUuid, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result && result.data) {
                    calculateReceiptsMetrics(result.data);
                }
            })
            .catch(error => {
                console.error('Error fetching receipts summary data:', error);
            });
    }

    function calculateReceiptsMetrics(receipts) {
        const today = new Date();
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
        const yearStart = new Date(today.getFullYear(), 0, 1);

        let totalReceipts = 0;
        let pendingReceipts = 0;
        let pendingCount = 0;
        let clearedReceipts = 0;
        let clearedCount = 0;
        let yearReceipts = 0;

        receipts.forEach(function(receipt) {
            const amount = parseFloat(receipt.amount || 0);
            const status = (receipt.status || '').toLowerCase();
            const receiptDate = receipt.receipt_date ? new Date(receipt.receipt_date) : null;

            // This month
            if (receiptDate && receiptDate >= monthStart) {
                totalReceipts += amount;
            }

            // This year
            if (receiptDate && receiptDate >= yearStart) {
                yearReceipts += amount;
            }

            // Pending
            if (status === 'pending') {
                pendingReceipts += amount;
                pendingCount++;
            }

            // Cleared
            if (status === 'cleared') {
                clearedReceipts += amount;
                clearedCount++;
            }
        });

        $('#totalReceipts').text('£' + totalReceipts.toFixed(2));
        $('#pendingReceipts').text('£' + pendingReceipts.toFixed(2));
        $('#pendingCount').text(pendingCount);
        $('#clearedReceipts').text('£' + clearedReceipts.toFixed(2));
        $('#clearedCount').text(clearedCount);
        $('#yearReceipts').text('£' + yearReceipts.toFixed(2));
    }

    // Update summary cards on page load
    $(document).ready(function() {
        setTimeout(function() {
            updateReceiptsSummaryCards();
        }, 1000);
    });
</script>
