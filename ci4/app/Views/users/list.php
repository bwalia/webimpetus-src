<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

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

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/users/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New User
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-users"></i> Total Users</div>
            <div class="summary-card-value" id="totalUsers">0</div>
            <div class="summary-card-subtitle">All registered users</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Active Users</div>
            <div class="summary-card-value" id="activeUsers">0</div>
            <div class="summary-card-subtitle">Currently active</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-user-shield"></i> Administrators</div>
            <div class="summary-card-value" id="adminUsers">0</div>
            <div class="summary-card-subtitle">Admin role users</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-clock"></i> Recent Users</div>
            <div class="summary-card-value" id="recentUsers">0</div>
            <div class="summary-card-subtitle">Added this month</div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="white_card_body">
    <div class="QA_table" id="usersTable"></div>
</div>

<?php require_once (APPPATH . 'Views/users/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Name', 'Email', 'Address', 'Status'];
    let columnsMachineName = ['id', 'name', 'email', 'address', 'status'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "users",
            apiPath: "api/v2/users",
            selector: "usersTable"
        }
    );

    // Load summary statistics
    function loadUserStatistics() {
        $.ajax({
            url: '/api/v2/users',
            method: 'GET',
            success: function(response) {
                if (response && response.data) {
                    const users = response.data;

                    // Total users
                    $('#totalUsers').text(users.length);

                    // Active users (status = 1)
                    const activeUsers = users.filter(user => user.status == 1 || user.status == '1');
                    $('#activeUsers').text(activeUsers.length);

                    // Admin users (you may need to adjust this based on your role field)
                    // This is a placeholder - adjust based on your actual role field
                    const adminUsers = users.filter(user => {
                        return user.role_name === 'Administrator' ||
                               user.role === 'admin' ||
                               user.is_admin == 1;
                    });
                    $('#adminUsers').text(adminUsers.length);

                    // Recent users (created this month)
                    const currentMonth = new Date().getMonth();
                    const currentYear = new Date().getFullYear();
                    const recentUsers = users.filter(user => {
                        if (user.created || user.created_at) {
                            const createdDate = new Date(user.created || user.created_at);
                            return createdDate.getMonth() === currentMonth &&
                                   createdDate.getFullYear() === currentYear;
                        }
                        return false;
                    });
                    $('#recentUsers').text(recentUsers.length);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading user statistics:', error);
                // Set default values on error
                $('#totalUsers').text('0');
                $('#activeUsers').text('0');
                $('#adminUsers').text('0');
                $('#recentUsers').text('0');
            }
        });
    }

    // Load statistics on page load
    loadUserStatistics();

    $('.table-listing-items  tr  td').on('click', function (e) {
        var dataClickable = $(this).parent().attr('data-link');
        if ($(this).is(':last-child') || $(this).is(':nth-last-child(2)')) {

        } else {
            if (dataClickable && dataClickable.length > 0) {

                window.location = dataClickable;
            }
        }

    });
</script>