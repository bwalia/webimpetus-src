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

    .quick-action-btn.danger {
        background-color: #ef4444;
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

    .status-running {
        background-color: #dbeafe;
        color: #1e40af;
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .7; }
    }

    .status-stopped {
        background-color: #e5e7eb;
        color: #374151;
    }

    .status-completed {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-invoiced {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-draft {
        background-color: #f3f4f6;
        color: #6b7280;
    }

    .timer-indicator {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .timer-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #ef4444;
        animation: blink 1s ease-in-out infinite;
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    .hours-cell {
        font-weight: 600;
        font-family: 'Courier New', monospace;
        color: #667eea;
    }

    .amount-cell {
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }

    .billable-amount {
        color: #059669;
    }

    .non-billable {
        color: #6b7280;
        text-decoration: line-through;
    }

    /* Enhanced table styling */
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

    /* Bulk action toolbar */
    #bulkActionToolbar {
        background: #667eea;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 15px;
        display: none;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
    }

    #bulkActionToolbar.show {
        display: flex;
    }

    .select-column {
        width: 30px;
        text-align: center;
    }

    input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-between mb-3" style="padding-bottom: 0;">
        <div>
            <?php if ($can_create ?? true): ?>
                <a href="/timesheets/edit" class="btn btn-success mr-2">
                    <i class="fa fa-clock"></i> Start New Timer
                </a>
            <?php endif; ?>
            <button type="button" onclick="window.location.reload();" class="btn btn-primary">
                <i class="fa fa-refresh"></i> Refresh
            </button>
        </div>
        <div>
            <a href="/timeslips" class="btn btn-secondary">
                <i class="fa fa-history"></i> Legacy Timeslips
            </a>
        </div>
    </div>
</div>

<!-- Bulk Action Toolbar -->
<div id="bulkActionToolbar">
    <div>
        <strong><span id="selectedCount">0</span> timesheet(s) selected</strong>
    </div>
    <div>
        <button type="button" class="btn btn-light btn-sm" onclick="createInvoiceFromSelected()">
            <i class="fa fa-file-invoice"></i> Create Invoice from Selected
        </button>
        <button type="button" class="btn btn-secondary btn-sm ml-2" onclick="clearSelection()">
            <i class="fa fa-times"></i> Clear Selection
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-clock"></i> Hours This Week</div>
            <div class="summary-card-value" id="hoursThisWeek"><?= number_format($stats['hours_this_week'] ?? 0, 2) ?></div>
            <div class="summary-card-subtitle">billable hours tracked</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-calendar"></i> Hours This Month</div>
            <div class="summary-card-value" id="hoursThisMonth"><?= number_format($stats['hours_this_month'] ?? 0, 2) ?></div>
            <div class="summary-card-subtitle">total this month</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-pound-sign"></i> Uninvoiced Amount</div>
            <div class="summary-card-value" id="uninvoicedAmount">£<?= number_format($stats['uninvoiced_amount'] ?? 0, 2) ?></div>
            <div class="summary-card-subtitle">ready to invoice</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-play-circle"></i> Running Timers</div>
            <div class="summary-card-value" id="runningTimers"><?= $stats['running_timers'] ?? 0 ?></div>
            <div class="summary-card-subtitle">active right now</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="white_card_body">
    <div class="row mb-3">
        <div class="col-md-3">
            <label>Status</label>
            <select class="form-control" id="filterStatus">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="running">Running</option>
                <option value="stopped">Stopped</option>
                <option value="completed">Completed</option>
                <option value="invoiced">Invoiced</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Employee</label>
            <select class="form-control select-employee-filter-ajax" id="filterEmployee">
                <option value="">All Employees</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Project</label>
            <select class="form-control select-project-filter-ajax" id="filterProject">
                <option value="">All Projects</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>&nbsp;</label><br>
            <button type="button" class="btn btn-primary" onclick="applyFilters()">
                <i class="fa fa-filter"></i> Apply Filters
            </button>
            <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                <i class="fa fa-times"></i> Clear
            </button>
        </div>
    </div>
</div>

<!-- Timesheets Table -->
<div class="white_card_body">
    <div class="QA_table" id="timesheetsTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    let selectedTimesheets = [];

    let columnsTitle = ['', 'ID', 'Employee', 'Project', 'Task', 'Start Time', 'Duration', 'Hours', 'Rate', 'Amount', 'Status', 'Billable'];
    let columnsMachineName = ['select', 'id', 'employee_full_name', 'project_name', 'task_name', 'start_time', 'duration', 'billable_hours', 'hourly_rate', 'total_amount', 'status', 'is_billable'];

    // Custom column renderers
    const columnRenderers = {
        select: function(data, type, row) {
            // Only show checkbox for non-invoiced, billable timesheets
            if (row.is_invoiced == 0 && row.is_billable == 1 && row.status !== 'running') {
                return '<input type="checkbox" class="timesheet-select" data-id="' + row.id + '" onchange="toggleSelection(this)">';
            }
            return '';
        },
        employee_full_name: function(data, type, row) {
            return '<strong>' + (data || '-') + '</strong>';
        },
        project_name: function(data, type, row) {
            let html = data || '-';
            return html;
        },
        task_name: function(data, type, row) {
            return data || '-';
        },
        start_time: function(data, type, row) {
            if (!data) return '-';
            const date = new Date(data);
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return date.toLocaleDateString('en-GB', options);
        },
        duration: function(data, type, row) {
            const minutes = parseInt(row.duration_minutes || 0);
            if (row.is_running == 1) {
                return '<span class="timer-indicator"><span class="timer-dot"></span>Running</span>';
            }
            if (minutes === 0) return '-';
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            return hours + 'h ' + mins + 'm';
        },
        billable_hours: function(data, type, row) {
            const hours = parseFloat(data || 0);
            if (row.is_running == 1) {
                return '<span class="timer-indicator">-</span>';
            }
            return '<span class="hours-cell">' + hours.toFixed(2) + '</span>';
        },
        hourly_rate: function(data, type, row) {
            const rate = parseFloat(data || 0);
            return '<span class="amount-cell">£' + rate.toFixed(2) + '</span>';
        },
        total_amount: function(data, type, row) {
            const amount = parseFloat(data || 0);
            let className = 'amount-cell';

            if (row.is_billable == 1) {
                className += ' billable-amount';
            } else {
                className += ' non-billable';
            }

            return '<span class="' + className + '">£' + amount.toFixed(2) + '</span>';
        },
        status: function(data, type, row) {
            let badgeClass = 'status-draft';
            let statusText = data || 'Draft';
            let icon = 'fa-file';

            switch(statusText.toLowerCase()) {
                case 'running':
                    badgeClass = 'status-running';
                    icon = 'fa-play-circle';
                    break;
                case 'stopped':
                    badgeClass = 'status-stopped';
                    icon = 'fa-stop-circle';
                    break;
                case 'completed':
                    badgeClass = 'status-completed';
                    icon = 'fa-check-circle';
                    break;
                case 'invoiced':
                    badgeClass = 'status-invoiced';
                    icon = 'fa-file-invoice';
                    break;
            }

            return '<span class="status-badge ' + badgeClass + '"><i class="fa ' + icon + '"></i> ' + statusText + '</span>';
        },
        is_billable: function(data, type, row) {
            if (data == 1) {
                return '<span style="color: #10b981;"><i class="fa fa-check-circle"></i> Yes</span>';
            }
            return '<span style="color: #6b7280;"><i class="fa fa-times-circle"></i> No</span>';
        }
    };

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "timesheets",
        apiPath: "timesheets/timesheetsList",
        selector: "timesheetsTable",
        columnRenderers: columnRenderers
    });

    // Selection management
    function toggleSelection(checkbox) {
        const id = parseInt(checkbox.dataset.id);
        if (checkbox.checked) {
            if (!selectedTimesheets.includes(id)) {
                selectedTimesheets.push(id);
            }
        } else {
            selectedTimesheets = selectedTimesheets.filter(item => item !== id);
        }
        updateBulkActionToolbar();
    }

    function updateBulkActionToolbar() {
        const toolbar = document.getElementById('bulkActionToolbar');
        const count = document.getElementById('selectedCount');
        count.textContent = selectedTimesheets.length;

        if (selectedTimesheets.length > 0) {
            toolbar.classList.add('show');
        } else {
            toolbar.classList.remove('show');
        }
    }

    function clearSelection() {
        selectedTimesheets = [];
        document.querySelectorAll('.timesheet-select').forEach(cb => cb.checked = false);
        updateBulkActionToolbar();
    }

    // Create invoice from selected timesheets
    function createInvoiceFromSelected() {
        if (selectedTimesheets.length === 0) {
            alert('Please select at least one timesheet to invoice.');
            return;
        }

        if (!confirm('Create an invoice from ' + selectedTimesheets.length + ' selected timesheet(s)?')) {
            return;
        }

        const formData = new FormData();
        formData.append('timesheet_ids', JSON.stringify(selectedTimesheets));

        fetch('/timesheets/createInvoice', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.status) {
                alert(result.message);
                // Redirect to the new invoice
                window.location.href = '/sales_invoices/edit/' + result.invoice_uuid;
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error creating invoice:', error);
            alert('An error occurred while creating the invoice.');
        });
    }

    // Filter functions
    function applyFilters() {
        const status = document.getElementById('filterStatus').value;
        const employee = document.getElementById('filterEmployee').value;
        const project = document.getElementById('filterProject').value;

        let url = '/timesheets/timesheetsList?';
        if (status) url += 'status=' + status + '&';
        if (employee) url += 'employee_id=' + employee + '&';
        if (project) url += 'project_id=' + project + '&';

        // Reload DataTable with new filters
        window.location.href = '/timesheets?' + url.slice(0, -1);
    }

    function clearFilters() {
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterEmployee').value = '';
        document.getElementById('filterProject').value = '';
        window.location.href = '/timesheets';
    }

    // Auto-refresh running timers every 30 seconds
    setInterval(function() {
        const runningCount = <?= $stats['running_timers'] ?? 0 ?>;
        if (runningCount > 0) {
            // Reload the table to update running timer durations
            location.reload();
        }
    }, 30000);

    // Initialize Select2 with AJAX for filter dropdowns
    $(document).ready(function() {
        // Employee filter
        $(".select-employee-filter-ajax").select2({
            ajax: {
                url: "/common/searchEmployees",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.first_name + ' ' + item.surname,
                                id: item.id
                            }
                        })
                    };
                }
            },
            minimumInputLength: 0,
            placeholder: "All Employees",
            allowClear: true
        });

        // Project filter
        $(".select-project-filter-ajax").select2({
            ajax: {
                url: "/common/searchProjects",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                }
            },
            minimumInputLength: 0,
            placeholder: "All Projects",
            allowClear: true
        });
    });
</script>
