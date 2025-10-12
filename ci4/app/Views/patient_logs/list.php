<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<!-- Include JIRA-Style CSS -->
<link rel="stylesheet" href="/css/jira-style-custom.css">

<!-- Page Header -->
<div class="white_card mb-3">
    <div class="white_card_header">
        <div class="d-flex justify-content-between align-items-center">
            <h3><i class="fa fa-file-medical"></i> Patient Logs Management</h3>
            <div class="btn-group">
                <a href="/patient_logs/flagged" class="btn btn-sm btn-danger mr-2">
                    <i class="fa fa-flag"></i> Flagged Logs
                </a>
                <a href="/patient_logs/scheduled" class="btn btn-sm btn-info mr-2">
                    <i class="fa fa-calendar"></i> Scheduled
                </a>
                <a href="/patient_logs/quickLog" class="btn btn-sm btn-success mr-2">
                    <i class="fa fa-bolt"></i> Quick Log
                </a>
                <button type="button" onclick="window.location.reload();" class="btn btn-sm btn-secondary mr-2">
                    <i class="fa fa-refresh"></i> Refresh
                </button>
                <a href="/patient_logs/edit" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus"></i> Add New Log
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="summary-cards mb-4">
    <div class="summary-card blue">
        <div class="summary-card-title">
            <i class="fa fa-file-medical"></i>
            Total Logs
        </div>
        <div class="summary-card-value"><?= $total_logs ?></div>
        <div class="summary-card-subtitle">all time</div>
    </div>

    <div class="summary-card red">
        <div class="summary-card-title">
            <i class="fa fa-flag"></i>
            Flagged Logs
        </div>
        <div class="summary-card-value"><?= $flagged_logs ?></div>
        <div class="summary-card-subtitle">need attention</div>
    </div>

    <div class="summary-card green">
        <div class="summary-card-title">
            <i class="fa fa-calendar-day"></i>
            Today's Logs
        </div>
        <div class="summary-card-value"><?= $today_logs ?></div>
        <div class="summary-card-subtitle">logs recorded</div>
    </div>

    <div class="summary-card purple">
        <div class="summary-card-title">
            <i class="fa fa-chart-pie"></i>
            Categories
        </div>
        <div class="summary-card-value"><?= count($log_categories) ?></div>
        <div class="summary-card-subtitle">active types</div>
    </div>
</div>

<!-- Category Breakdown -->
<?php if (!empty($log_categories)): ?>
<div class="white_card mb-4">
    <div class="white_card_header">
        <h5><i class="fa fa-chart-bar"></i> Logs by Category</h5>
    </div>
    <div class="white_card_body">
        <div class="row">
            <?php foreach ($log_categories as $category): ?>
                <div class="col-md-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-card-label"><?= esc($category['log_category']) ?></div>
                        <div class="stat-card-value"><?= number_format($category['count']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="white_card_body">
    <div class="QA_table" id="patientLogsTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Log #', 'Patient', 'Category', 'Type', 'Staff', 'Date/Time', 'Priority', 'Status', 'Flagged'];
    let columnsMachineName = ['id', 'log_number', 'patient_name', 'log_category', 'log_type', 'staff_name', 'performed_datetime', 'priority', 'status', 'is_flagged'];

    // Custom column renderers using JIRA design system
    const columnRenderers = {
        log_number: function(data, type, row) {
            return '<a href="/patient_logs/timeline/' + row.patient_contact_id + '" style="color: var(--jira-blue-primary); font-weight: 600;">' + data + '</a>';
        },
        patient_name: function(data, type, row) {
            if (!data) return '<span style="color: var(--jira-text-subtle);">-</span>';
            return '<strong style="color: var(--jira-text-primary);">' + data + '</strong>' +
                (row.patient_phone ? '<br><small style="color: var(--jira-text-secondary);">' + row.patient_phone + '</small>' : '');
        },
        log_category: function(data, type, row) {
            if (!data) return '<span style="color: var(--jira-text-subtle);">-</span>';

            // Map categories to JIRA badge colors
            const cat = data.toLowerCase().replace(/\s+/g, '');
            let badgeClass = 'badge badge-secondary';

            if (cat.includes('medication')) {
                badgeClass = 'badge badge-warning';
            } else if (cat.includes('vital')) {
                badgeClass = 'badge badge-info';
            } else if (cat.includes('treatment') || cat.includes('procedure')) {
                badgeClass = 'badge badge-purple';
            } else if (cat.includes('lab')) {
                badgeClass = 'badge badge-pink';
            }

            return '<span class="' + badgeClass + '">' + data + '</span>';
        },
        log_type: function(data, type, row) {
            return data ? '<span style="color: var(--jira-text-primary);">' + data + '</span>' : '<span style="color: var(--jira-text-subtle);">-</span>';
        },
        staff_name: function(data, type, row) {
            if (!data) return '<span style="color: var(--jira-text-subtle);">-</span>';
            return '<span style="color: var(--jira-text-primary);">' + data + '</span>' +
                (row.job_title ? '<br><small style="color: var(--jira-text-secondary);">' + row.job_title + '</small>' : '');
        },
        performed_datetime: function(data, type, row) {
            if (!data) return '<span style="color: var(--jira-text-subtle);">-</span>';
            const date = new Date(data);
            const dateOptions = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit'
            };
            return '<span style="color: var(--jira-text-primary);">' + date.toLocaleDateString('en-GB', dateOptions) + '</span>' +
                '<br><small style="color: var(--jira-text-secondary);">' +
                date.toLocaleTimeString('en-GB', timeOptions) +
                '</small>';
        },
        priority: function(data, type, row) {
            if (!data || data === 'Normal') return '<span style="color: var(--jira-text-subtle);">Normal</span>';

            let badgeClass = 'badge';
            let icon = '<i class="fa fa-exclamation-circle"></i>';

            if (data.toLowerCase() === 'high') {
                badgeClass += ' badge-danger';
                icon = '<i class="fa fa-exclamation-circle"></i>';
            } else if (data.toLowerCase() === 'urgent') {
                badgeClass += ' badge-danger';
                icon = '<i class="fa fa-exclamation-triangle"></i>';
            }

            return '<span class="' + badgeClass + '">' + icon + ' ' + data + '</span>';
        },
        status: function(data, type, row) {
            let badgeClass = 'badge badge-secondary';
            let statusText = data || 'Draft';

            if (statusText.toLowerCase() === 'completed') {
                badgeClass = 'badge badge-success';
            } else if (statusText.toLowerCase() === 'scheduled') {
                badgeClass = 'badge badge-info';
            } else if (statusText.toLowerCase() === 'cancelled') {
                badgeClass = 'badge badge-danger';
            }

            return '<span class="' + badgeClass + '">' + statusText + '</span>';
        },
        is_flagged: function(data, type, row) {
            if (data == 1) {
                let reason = row.flag_reason ? '<br><small style="color: var(--jira-text-secondary);">' + row.flag_reason + '</small>' : '';
                return '<span class="badge badge-danger"><i class="fa fa-flag"></i> Flagged</span>' + reason;
            }
            return '<span style="color: var(--jira-text-subtle);">-</span>';
        }
    };

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "patient_logs",
        apiPath: "patient_logs/logsList",
        selector: "patientLogsTable",
        columnRenderers: columnRenderers
    });
</script>
