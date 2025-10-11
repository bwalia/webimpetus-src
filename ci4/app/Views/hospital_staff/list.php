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

    .status-active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-on-leave {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .status-suspended {
        background-color: #e5e7eb;
        color: #374151;
    }

    .department-badge {
        background-color: #e0e7ff;
        color: #3730a3;
        padding: 3px 10px;
        border-radius: 10px;
        font-size: 0.7rem;
    }

    .training-status {
        font-size: 0.75rem;
    }

    .training-uptodate {
        color: #065f46;
    }

    .training-overdue {
        color: #991b1b;
    }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <a href="/hospital_staff/dashboard" class="btn btn-info mr-2">
                <i class="fa fa-tachometer-alt"></i> Dashboard
            </a>
        </div>
        <div>
            <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
                <i class="fa fa-refresh"></i> Refresh
            </button>
            <a href="/hospital_staff/edit" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add New Staff
            </a>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-users"></i> Total Staff</div>
            <div class="summary-card-value"><?= $total_staff ?></div>
            <div class="summary-card-subtitle">all staff members</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Active</div>
            <div class="summary-card-value"><?= $active_staff ?></div>
            <div class="summary-card-subtitle">currently working</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-plane"></i> On Leave</div>
            <div class="summary-card-value"><?= $on_leave ?></div>
            <div class="summary-card-subtitle">staff members</div>
        </div>

        <div class="summary-card red">
            <div class="summary-card-title"><i class="fa fa-exclamation-triangle"></i> Expiring Soon</div>
            <div class="summary-card-value"><?= count($expiring_soon) ?></div>
            <div class="summary-card-subtitle">registrations</div>
        </div>
    </div>
</div>

<div class="white_card_body">
    <div class="QA_table" id="hospitalStaffTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Staff #', 'Name', 'Department', 'Job Title', 'Registration', 'Employment', 'Training', 'Status'];
    let columnsMachineName = ['id', 'staff_number', 'name', 'department', 'job_title', 'professional_registration', 'employment_type', 'mandatory_training_status', 'status'];

    // Custom column renderers
    const columnRenderers = {
        staff_number: function(data, type, row) {
            return '<strong style="color: #667eea;">' + data + '</strong>';
        },
        name: function(data, type, row) {
            const userName = row.user_name || row.contact_name || row.employee_name || '-';
            return '<div>' +
                '<strong>' + userName + '</strong>' +
                (row.specialization ? '<br><small style="color: #6b7280;">' + row.specialization + '</small>' : '') +
                '</div>';
        },
        department: function(data, type, row) {
            if (!data) return '-';
            return '<span class="department-badge">' + data + '</span>';
        },
        job_title: function(data, type, row) {
            if (!data) return '-';
            let icon = '';
            const title = data.toLowerCase();
            if (title.includes('doctor') || title.includes('consultant')) {
                icon = '<i class="fa fa-user-md"></i> ';
            } else if (title.includes('nurse')) {
                icon = '<i class="fa fa-user-nurse"></i> ';
            } else if (title.includes('admin') || title.includes('clerk')) {
                icon = '<i class="fa fa-user-tie"></i> ';
            }
            return icon + data;
        },
        professional_registration: function(data, type, row) {
            if (!data) return '-';
            let expiry = row.registration_expiry;
            let html = '<div>' + data;
            if (expiry) {
                const expiryDate = new Date(expiry);
                const today = new Date();
                const daysUntilExpiry = Math.floor((expiryDate - today) / (1000 * 60 * 60 * 24));

                if (daysUntilExpiry < 0) {
                    html += '<br><small style="color: #dc2626;"><i class="fa fa-exclamation-circle"></i> Expired</small>';
                } else if (daysUntilExpiry < 90) {
                    html += '<br><small style="color: #f59e0b;"><i class="fa fa-clock"></i> ' + daysUntilExpiry + ' days</small>';
                }
            }
            html += '</div>';
            return html;
        },
        employment_type: function(data, type, row) {
            return data || '-';
        },
        mandatory_training_status: function(data, type, row) {
            if (!data) return '-';
            let statusClass = 'training-uptodate';
            let icon = '<i class="fa fa-check-circle"></i>';

            if (data.toLowerCase() === 'overdue') {
                statusClass = 'training-overdue';
                icon = '<i class="fa fa-exclamation-circle"></i>';
            }

            return '<span class="training-status ' + statusClass + '">' + icon + ' ' + data + '</span>';
        },
        status: function(data, type, row) {
            let badgeClass = 'status-active';
            let statusText = data || 'Active';

            if (statusText.toLowerCase() === 'on leave') {
                badgeClass = 'status-on-leave';
            } else if (statusText.toLowerCase() === 'inactive') {
                badgeClass = 'status-inactive';
            } else if (statusText.toLowerCase() === 'suspended') {
                badgeClass = 'status-suspended';
            }

            return '<span class="status-badge ' + badgeClass + '">' + statusText + '</span>';
        }
    };

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "hospital_staff",
        apiPath: "hospital_staff/staffList",
        selector: "hospitalStaffTable",
        columnRenderers: columnRenderers
    });
</script>
