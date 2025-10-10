<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
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
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/employees/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Employee
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-users"></i> Total Employees</div>
            <div class="summary-card-value" id="totalEmployees">0</div>
            <div class="summary-card-subtitle">in the system</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-user-check"></i> Web Access</div>
            <div class="summary-card-value" id="webAccessCount">0</div>
            <div class="summary-card-subtitle">employees with access</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-calendar-plus"></i> This Month</div>
            <div class="summary-card-value" id="newThisMonth">0</div>
            <div class="summary-card-subtitle">new employees</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-user-clock"></i> This Week</div>
            <div class="summary-card-value" id="newThisWeek">0</div>
            <div class="summary-card-subtitle">new employees</div>
        </div>
    </div>
</div>

<div class="white_card_body">
    <div class="QA_table" id="employeesTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'First Name', 'Surname', 'Email', 'Mobile', 'Web Access'];
    let columnsMachineName = ['id', 'first_name', 'surname', 'email', 'mobile', 'allow_web_access'];

    // Custom column renderer for better display
    const columnRenderers = {
        allow_web_access: function(data, type, row) {
            if (data == 1 || data === true || data === 'true') {
                return '<span class="status-badge status-active"><i class="fa fa-check"></i> Yes</span>';
            } else {
                return '<span class="status-badge status-inactive"><i class="fa fa-times"></i> No</span>';
            }
        },
        email: function(data, type, row) {
            return '<a href="mailto:' + data + '" style="color: #667eea;">' + data + '</a>';
        },
        mobile: function(data, type, row) {
            if (data) {
                return '<a href="tel:' + data + '" style="color: #667eea;">' + data + '</a>';
            }
            return data || '-';
        }
    };

    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "employees",
            apiPath: "employees/employeesList",
            selector: "employeesTable",
            columnRenderers: columnRenderers
        }
    );

    // Update summary cards with employee metrics
    function updateEmployeeSummaryCards() {
        const businessUuid = '<?php echo session('uuid_business'); ?>';

        // Fetch employees data and calculate summaries
        fetch('/employees/employeesList?limit=10000&offset=0&uuid_business_id=' + businessUuid)
            .then(response => response.json())
            .then(result => {
                if (result && result.data) {
                    calculateEmployeeSummaries(result.data);
                }
            })
            .catch(error => {
                console.error('Error fetching employee summary data:', error);
                // Set default values on error
                $('#totalEmployees').text('0');
                $('#webAccessCount').text('0');
                $('#newThisMonth').text('0');
                $('#newThisWeek').text('0');
            });
    }

    function calculateEmployeeSummaries(employees) {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const weekStart = new Date(today);
        weekStart.setDate(today.getDate() - today.getDay()); // Sunday of current week
        const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);

        let totalCount = employees.length;
        let webAccessCount = 0;
        let newThisWeek = 0;
        let newThisMonth = 0;

        employees.forEach(function(employee) {
            // Count web access
            if (employee.allow_web_access == 1 || employee.allow_web_access === true) {
                webAccessCount++;
            }

            // Count new employees (this would require created_at field)
            // Since we have created_at in the database, we can add it to the API response
            if (employee.created_at) {
                const createdDate = new Date(employee.created_at);

                if (createdDate >= weekStart) {
                    newThisWeek++;
                }

                if (createdDate >= monthStart) {
                    newThisMonth++;
                }
            }
        });

        // Update summary cards
        $('#totalEmployees').text(totalCount);
        $('#webAccessCount').text(webAccessCount);
        $('#newThisMonth').text(newThisMonth);
        $('#newThisWeek').text(newThisWeek);

        console.log('Employee summary updated:', {
            total: totalCount,
            webAccess: webAccessCount,
            newThisWeek: newThisWeek,
            newThisMonth: newThisMonth
        });
    }

    // Update summary cards on page load
    $(document).ready(function() {
        // Wait for DataTable to initialize
        setTimeout(function() {
            updateEmployeeSummaryCards();
        }, 1000);
    });
</script>
