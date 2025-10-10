<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
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
    }

    .status-active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .customer-link {
        color: #667eea;
        font-weight: 600;
        text-decoration: none;
    }

    .customer-link:hover {
        text-decoration: underline;
    }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/customers/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Customer
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="summary-card-title"><i class="fa fa-building"></i> Total Customers</div>
        <div class="summary-card-value" id="totalCustomers">0</div>
        <div class="summary-card-subtitle">in database</div>
    </div>

    <div class="summary-card green">
        <div class="summary-card-title"><i class="fa fa-check-circle"></i> Active Customers</div>
        <div class="summary-card-value" id="activeCustomers">0</div>
        <div class="summary-card-subtitle">status active</div>
    </div>

    <div class="summary-card orange">
        <div class="summary-card-title"><i class="fa fa-calendar-plus"></i> New This Month</div>
        <div class="summary-card-value" id="newThisMonth">0</div>
        <div class="summary-card-subtitle">customers added</div>
    </div>

    <div class="summary-card blue">
        <div class="summary-card-title"><i class="fa fa-truck"></i> Suppliers</div>
        <div class="summary-card-value" id="suppliersCount">0</div>
        <div class="summary-card-subtitle">marked as supplier</div>
    </div>
</div>

<div class="white_card_body">
    <div class="QA_table" id="customersTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/scripts.php'); ?>

<script>
    let columnsTitle = ['Id', 'Customer Name', 'Account Number', 'Status', 'Email', 'Phone', 'City', 'Supplier'];
    let columnsMachineName = ['id', 'company_name', 'acc_no', 'status', 'email', 'phone', 'city', 'supplier'];

    // Custom column renderers
    const columnRenderers = {
        company_name: function(data, type, row) {
            return '<a href="/customers/edit/' + row.uuid + '" class="customer-link">' + data + '</a>';
        },
        status: function(data, type, row) {
            if (data == 1 || data === true) {
                return '<span class="status-badge status-active"><i class="fa fa-check"></i> Active</span>';
            } else {
                return '<span class="status-badge status-inactive"><i class="fa fa-times"></i> Inactive</span>';
            }
        },
        email: function(data, type, row) {
            if (data) {
                return '<a href="mailto:' + data + '" style="color: #667eea;">' + data + '</a>';
            }
            return '-';
        },
        phone: function(data, type, row) {
            if (data) {
                return '<a href="tel:' + data + '" style="color: #667eea;">' + data + '</a>';
            }
            return '-';
        },
        supplier: function(data, type, row) {
            if (data == 1 || data === true) {
                return '<span class="status-badge status-active"><i class="fa fa-truck"></i> Yes</span>';
            } else {
                return '<span style="color: #9ca3af;">No</span>';
            }
        }
    };

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "customers",
        apiPath: "customers/customersList",
        selector: "customersTable",
        columnRenderers: columnRenderers
    });

    // Update summary cards
    function updateCustomerSummaryCards() {
        const businessUuid = '<?php echo session('uuid_business'); ?>';

        fetch('/customers/customersList?limit=10000&offset=0&uuid_business_id=' + businessUuid)
            .then(response => response.json())
            .then(result => {
                if (result && result.data) {
                    calculateCustomerMetrics(result.data);
                }
            })
            .catch(error => {
                console.error('Error fetching customer summary:', error);
            });
    }

    function calculateCustomerMetrics(customers) {
        const today = new Date();
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);

        let totalCount = customers.length;
        let activeCount = 0;
        let newThisMonth = 0;
        let suppliersCount = 0;

        customers.forEach(function(customer) {
            // Count active customers
            if (customer.status == 1 || customer.status === true) {
                activeCount++;
            }

            // Count suppliers
            if (customer.supplier == 1 || customer.supplier === true) {
                suppliersCount++;
            }

            // Count new this month
            if (customer.created_at) {
                const createdDate = new Date(customer.created_at);
                if (createdDate >= monthStart) {
                    newThisMonth++;
                }
            }
        });

        // Update cards
        $('#totalCustomers').text(totalCount);
        $('#activeCustomers').text(activeCount);
        $('#newThisMonth').text(newThisMonth);
        $('#suppliersCount').text(suppliersCount);

        console.log('Customer metrics updated:', {
            total: totalCount,
            active: activeCount,
            newThisMonth: newThisMonth,
            suppliers: suppliersCount
        });
    }

    $(document).ready(function() {
        setTimeout(function() {
            updateCustomerSummaryCards();
        }, 1000);
    });
</script>
