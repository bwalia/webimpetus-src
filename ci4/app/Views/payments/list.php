<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<!-- Include JIRA-Style CSS -->
<link rel="stylesheet" href="/css/jira-style-custom.css">

<!-- Page Header -->
<div class="white_card mb-3">
    <div class="white_card_header">
        <div class="d-flex justify-content-between align-items-center">
            <h3><i class="fa fa-money-bill-wave"></i> Payments Management</h3>
            <div class="btn-group">
                <button type="button" onclick="window.location.reload();" class="btn btn-sm btn-secondary mr-2">
                    <i class="fa fa-refresh"></i> Refresh
                </button>
                <a href="/payments/edit" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus"></i> Add New Payment
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="summary-cards mb-4">
    <div class="summary-card blue">
        <div class="summary-card-title">
            <i class="fa fa-money-bill-wave"></i>
            Total Payments
        </div>
        <div class="summary-card-value" id="totalPayments">£0.00</div>
        <div class="summary-card-subtitle">this month</div>
    </div>

    <div class="summary-card orange">
        <div class="summary-card-title">
            <i class="fa fa-clock"></i>
            Pending
        </div>
        <div class="summary-card-value" id="pendingPayments">£0.00</div>
        <div class="summary-card-subtitle"><span id="pendingCount">0</span> payments</div>
    </div>

    <div class="summary-card green">
        <div class="summary-card-title">
            <i class="fa fa-check-circle"></i>
            Completed
        </div>
        <div class="summary-card-value" id="completedPayments">£0.00</div>
        <div class="summary-card-subtitle"><span id="completedCount">0</span> payments</div>
    </div>

    <div class="summary-card purple">
        <div class="summary-card-title">
            <i class="fa fa-calendar"></i>
            This Year
        </div>
        <div class="summary-card-value" id="yearPayments">£0.00</div>
        <div class="summary-card-subtitle">total paid</div>
    </div>
</div>

<div class="white_card_body">
    <div class="QA_table" id="paymentsTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Payment #', 'Date', 'Payee', 'Type', 'Amount', 'Method', 'Bank Account', 'Status', 'Posted'];
    let columnsMachineName = ['id', 'payment_number', 'payment_date', 'payee_name', 'payment_type', 'amount', 'payment_method', 'bank_account_name', 'status', 'is_posted'];

    // Custom column renderers using JIRA design system
    const columnRenderers = {
        payment_number: function(data, type, row) {
            return '<strong style="color: var(--jira-blue-primary); font-weight: 600;">' + data + '</strong>';
        },
        payment_date: function(data, type, row) {
            if (!data) return '<span style="color: var(--jira-text-subtle);">-</span>';
            const date = new Date(data);
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };
            return '<span style="color: var(--jira-text-primary);">' + date.toLocaleDateString('en-GB', options) + '</span>';
        },
        amount: function(data, type, row) {
            const amount = parseFloat(data || 0);
            const currency = row.currency || 'GBP';
            return '<span style="font-weight: 600; font-family: monospace; color: var(--jira-text-primary);">' +
                   getCurrencySymbol(currency) + amount.toFixed(2) + '</span>';
        },
        status: function(data, type, row) {
            let badgeClass = 'badge badge-secondary';
            let statusText = data || 'Draft';

            if (statusText.toLowerCase() === 'completed') {
                badgeClass = 'badge badge-success';
            } else if (statusText.toLowerCase() === 'pending') {
                badgeClass = 'badge badge-warning';
            } else if (statusText.toLowerCase() === 'cancelled') {
                badgeClass = 'badge badge-danger';
            }

            return '<span class="' + badgeClass + '">' + statusText + '</span>';
        },
        is_posted: function(data, type, row) {
            if (data == 1) {
                return '<span class="badge badge-info"><i class="fa fa-check"></i> Posted</span>';
            }
            return '<span class="badge badge-secondary">Not Posted</span>';
        },
        bank_account_name: function(data, type, row) {
            return data ? '<span style="color: var(--jira-text-primary);">' + data + '</span>' :
                   '<span style="color: var(--jira-text-subtle);">-</span>';
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
        tableName: "payments",
        apiPath: "api/v2/payments",
        selector: "paymentsTable",
        columnRenderers: columnRenderers
    });

    // Update summary cards
    function updatePaymentsSummaryCards() {
        const businessUuid = '<?php echo session('uuid_business'); ?>';
        const token = '<?php echo session('token') ?? ''; ?>';

        fetch('/api/v2/payments?uuid_business_id=' + businessUuid, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result && result.data) {
                    calculatePaymentsMetrics(result.data);
                }
            })
            .catch(error => {
                console.error('Error fetching payments summary data:', error);
            });
    }

    function calculatePaymentsMetrics(payments) {
        const today = new Date();
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
        const yearStart = new Date(today.getFullYear(), 0, 1);

        let totalPayments = 0;
        let pendingPayments = 0;
        let pendingCount = 0;
        let completedPayments = 0;
        let completedCount = 0;
        let yearPayments = 0;

        payments.forEach(function(payment) {
            const amount = parseFloat(payment.amount || 0);
            const status = (payment.status || '').toLowerCase();
            const paymentDate = payment.payment_date ? new Date(payment.payment_date) : null;

            // This month
            if (paymentDate && paymentDate >= monthStart) {
                totalPayments += amount;
            }

            // This year
            if (paymentDate && paymentDate >= yearStart) {
                yearPayments += amount;
            }

            // Pending
            if (status === 'pending') {
                pendingPayments += amount;
                pendingCount++;
            }

            // Completed
            if (status === 'completed') {
                completedPayments += amount;
                completedCount++;
            }
        });

        $('#totalPayments').text('£' + totalPayments.toFixed(2));
        $('#pendingPayments').text('£' + pendingPayments.toFixed(2));
        $('#pendingCount').text(pendingCount);
        $('#completedPayments').text('£' + completedPayments.toFixed(2));
        $('#completedCount').text(completedCount);
        $('#yearPayments').text('£' + yearPayments.toFixed(2));
    }

    // Update summary cards on page load
    $(document).ready(function() {
        setTimeout(function() {
            updatePaymentsSummaryCards();
        }, 1000);
    });
</script>
