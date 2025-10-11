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

    .status-scheduled {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .status-completed {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-cancelled {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .category-badge {
        padding: 3px 10px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .cat-medication {
        background-color: #fef3c7;
        color: #92400e;
    }

    .cat-vitalsigns {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .cat-treatment {
        background-color: #e0e7ff;
        color: #3730a3;
    }

    .cat-labresult {
        background-color: #fce7f3;
        color: #9f1239;
    }

    .flagged-badge {
        background-color: #fee2e2;
        color: #991b1b;
        padding: 2px 8px;
        border-radius: 8px;
        font-size: 0.7rem;
    }

    .priority-high {
        color: #dc2626;
        font-weight: 600;
    }

    .priority-urgent {
        color: #991b1b;
        font-weight: 700;
    }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <a href="/patient_logs/flagged" class="btn btn-danger mr-2">
                <i class="fa fa-flag"></i> Flagged Logs
            </a>
            <a href="/patient_logs/scheduled" class="btn btn-info mr-2">
                <i class="fa fa-calendar"></i> Scheduled
            </a>
            <a href="/patient_logs/quickLog" class="btn btn-success mr-2">
                <i class="fa fa-bolt"></i> Quick Log
            </a>
        </div>
        <div>
            <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
                <i class="fa fa-refresh"></i> Refresh
            </button>
            <a href="/patient_logs/edit" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add New Log
            </a>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-file-medical"></i> Total Logs</div>
            <div class="summary-card-value"><?= $total_logs ?></div>
            <div class="summary-card-subtitle">all time</div>
        </div>

        <div class="summary-card red">
            <div class="summary-card-title"><i class="fa fa-flag"></i> Flagged</div>
            <div class="summary-card-value"><?= $flagged_logs ?></div>
            <div class="summary-card-subtitle">need attention</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-calendar-day"></i> Today</div>
            <div class="summary-card-value"><?= $today_logs ?></div>
            <div class="summary-card-subtitle">logs recorded</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-chart-pie"></i> Categories</div>
            <div class="summary-card-value"><?= count($log_categories) ?></div>
            <div class="summary-card-subtitle">active categories</div>
        </div>
    </div>
</div>

<!-- Category Breakdown -->
<?php if (!empty($log_categories)): ?>
<div class="white_card_body">
    <h5 class="mb-3"><i class="fa fa-chart-bar"></i> Logs by Category</h5>
    <div class="row">
        <?php foreach ($log_categories as $category): ?>
            <div class="col-md-3 mb-2">
                <div class="card">
                    <div class="card-body">
                        <h6><?= $category['log_category'] ?></h6>
                        <h4><?= $category['count'] ?></h4>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
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

    // Custom column renderers
    const columnRenderers = {
        log_number: function(data, type, row) {
            return '<a href="/patient_logs/timeline/' + row.patient_contact_id + '" style="color: #667eea; font-weight: 600;">' + data + '</a>';
        },
        patient_name: function(data, type, row) {
            if (!data) return '-';
            return '<strong>' + data + '</strong>' +
                (row.patient_phone ? '<br><small style="color: #6b7280;">' + row.patient_phone + '</small>' : '');
        },
        log_category: function(data, type, row) {
            if (!data) return '-';
            let badgeClass = 'category-badge';

            const cat = data.toLowerCase().replace(/\s+/g, '');
            if (cat.includes('medication')) {
                badgeClass += ' cat-medication';
            } else if (cat.includes('vital')) {
                badgeClass += ' cat-vitalsigns';
            } else if (cat.includes('treatment') || cat.includes('procedure')) {
                badgeClass += ' cat-treatment';
            } else if (cat.includes('lab')) {
                badgeClass += ' cat-labresult';
            }

            return '<span class="' + badgeClass + '">' + data + '</span>';
        },
        log_type: function(data, type, row) {
            return data || '-';
        },
        staff_name: function(data, type, row) {
            if (!data) return '-';
            return data + (row.job_title ? '<br><small style="color: #6b7280;">' + row.job_title + '</small>' : '');
        },
        performed_datetime: function(data, type, row) {
            if (!data) return '-';
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
            return date.toLocaleDateString('en-GB', dateOptions) +
                '<br><small style="color: #6b7280;">' +
                date.toLocaleTimeString('en-GB', timeOptions) +
                '</small>';
        },
        priority: function(data, type, row) {
            if (!data || data === 'Normal') return '-';
            let className = '';
            let icon = '<i class="fa fa-exclamation-circle"></i>';

            if (data.toLowerCase() === 'high') {
                className = 'priority-high';
            } else if (data.toLowerCase() === 'urgent') {
                className = 'priority-urgent';
                icon = '<i class="fa fa-exclamation-triangle"></i>';
            }

            return '<span class="' + className + '">' + icon + ' ' + data + '</span>';
        },
        status: function(data, type, row) {
            let badgeClass = 'status-draft';
            let statusText = data || 'Draft';

            if (statusText.toLowerCase() === 'completed') {
                badgeClass = 'status-completed';
            } else if (statusText.toLowerCase() === 'scheduled') {
                badgeClass = 'status-scheduled';
            } else if (statusText.toLowerCase() === 'cancelled') {
                badgeClass = 'status-cancelled';
            }

            return '<span class="status-badge ' + badgeClass + '">' + statusText + '</span>';
        },
        is_flagged: function(data, type, row) {
            if (data == 1) {
                let reason = row.flag_reason ? '<br><small>' + row.flag_reason + '</small>' : '';
                return '<span class="flagged-badge"><i class="fa fa-flag"></i> Flagged</span>' + reason;
            }
            return '-';
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
