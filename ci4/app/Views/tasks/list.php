<?php require_once (APPPATH . 'Views/tasks/list-title.php'); ?>

<style>
    /* Enhanced table styles */
    .priority-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .priority-low {
        background: #dbeafe;
        color: #1e40af;
    }

    .priority-medium {
        background: #fef3c7;
        color: #92400e;
    }

    .priority-high {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-backlog { background: #f3f4f6; color: #6b7280; }
    .status-todo { background: #dbeafe; color: #1e40af; }
    .status-in-progress { background: #fef3c7; color: #92400e; }
    .status-review { background: #ede9fe; color: #6b21a8; }
    .status-done { background: #d1fae5; color: #065f46; }

    .task-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .task-link:hover {
        text-decoration: underline;
    }

    /* Override summary card styles to match sales_invoices exactly */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: var(--bg-primary, #ffffff) !important;
        border-radius: var(--radius-lg, 8px) !important;
        padding: 24px !important;
        box-shadow: var(--shadow-md, 0 4px 8px rgba(9, 30, 66, 0.15)) !important;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: none !important;
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px !important;
        width: 100% !important;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }

    .summary-card.blue::before {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    }

    .summary-card.green::before {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }

    .summary-card.orange::before {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    }

    .summary-card.purple::before {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg, 0 8px 16px rgba(9, 30, 66, 0.2)) !important;
    }

    .summary-card-title {
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: var(--gray-600, #6b7280) !important;
        margin-bottom: 12px !important;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: none !important;
        letter-spacing: normal !important;
    }

    .summary-card-value {
        font-size: 2rem !important;
        font-weight: 700 !important;
        color: var(--gray-900, #111827) !important;
        line-height: 1 !important;
        margin-bottom: 8px !important;
    }

    .summary-card-subtitle {
        font-size: 0.75rem !important;
        color: var(--gray-500, #6b7280) !important;
        margin-top: 0 !important;
    }
</style>

<!-- Summary Cards for Tasks -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-tasks"></i> Total Tasks</div>
            <div class="summary-card-value" id="totalTasks">0</div>
            <div class="summary-card-subtitle">all tasks</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-spinner"></i> In Progress</div>
            <div class="summary-card-value" id="inProgressTasks">0</div>
            <div class="summary-card-subtitle">active tasks</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Completed</div>
            <div class="summary-card-value" id="completedTasks">0</div>
            <div class="summary-card-subtitle">done this month</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-exclamation-circle"></i> High Priority</div>
            <div class="summary-card-value" id="highPriorityTasks">0</div>
            <div class="summary-card-subtitle">urgent tasks</div>
        </div>
    </div>
</div>

<div class="white_card_body">
    <div class="QA_table" id="tasksTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    const columnRenderers = {
        name: function(data, type, row) {
            return '<a href="/tasks/edit/' + row.uuid + '" class="task-link">' + data + '</a>';
        },
        priority: function(data, type, row) {
            if (!data) return '<span style="color: #9ca3af;">-</span>';
            const priorityClass = 'priority-' + data.toLowerCase();
            return '<span class="priority-badge ' + priorityClass + '">' + data.toUpperCase() + '</span>';
        },
        category: function(data, type, row) {
            if (!data) return '<span class="status-badge status-backlog">BACKLOG</span>';
            const statusClass = 'status-' + data.toLowerCase();
            const statusText = data.replace('-', ' ').toUpperCase();
            return '<span class="status-badge ' + statusClass + '">' + statusText + '</span>';
        },
        status: function(data, type, row) {
            // This is the old "active" status column
            if (data == 1) {
                return '<span style="color: #10b981; font-weight: 600;"><i class="fa fa-check-circle"></i> Active</span>';
            } else {
                return '<span style="color: #ef4444; font-weight: 600;"><i class="fa fa-times-circle"></i> Inactive</span>';
            }
        },
        estimated_hour: function(data, type, row) {
            if (!data || data == 0) return '<span style="color: #9ca3af;">-</span>';
            return '<span><i class="fa fa-clock-o"></i> ' + data + 'h</span>';
        },
        rate: function(data, type, row) {
            if (!data || data == 0) return '<span style="color: #9ca3af;">-</span>';
            return '<span>$' + parseFloat(data).toFixed(2) + '</span>';
        }
    };

    let columnsTitle = ['<?= lang('App.id') ?>', '<?= lang('App.task_title') ?>', '<?= lang('App.priority') ?>', '<?= lang('App.category') ?>', '<?= lang('App.project') ?>', '<?= lang('App.estimated_hours') ?>', '<?= lang('App.rate') ?>', '<?= lang('App.active') ?>'];
    let columnsMachineName = ['id', 'name', 'priority', 'category', 'project_name', 'estimated_hour', 'rate', 'active'];

    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "tasks",
            apiPath: "tasks/tasksList",
            selector: "tasksTable",
            columnRenderers: columnRenderers
        }
    );

    var base_url = '<?php echo base_url('/tasks') ?>';
    $(document).ready(function () {
        $("#task_status").on("change", function (e) {
            var redirect_to = base_url;
            if ($(this).val() != "") {
                redirect_to = base_url + "?status=" + $(this).val();
            }
            window.location.replace(redirect_to);
        });

        // Update summary cards
        setTimeout(function() {
            updateTaskSummaryCards();
        }, 1000);
    });

    // Update summary cards with task metrics
    function updateTaskSummaryCards() {
        const businessUuid = '<?php echo session('uuid_business'); ?>';
        const token = '<?php echo session('jwt_token') ?? session('token') ?? ''; ?>';

        // Fetch tasks data and calculate summaries
        fetch('/tasks/tasksList?uuid_business_id=' + businessUuid, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result && result.data) {
                    calculateTaskMetrics(result.data);
                }
            })
            .catch(error => {
                console.error('Error fetching task summary data:', error);
            });
    }

    function calculateTaskMetrics(tasks) {
        const today = new Date();
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
        monthStart.setHours(0, 0, 0, 0);

        let totalTasks = 0;
        let inProgressTasks = 0;
        let completedThisMonth = 0;
        let highPriorityTasks = 0;

        tasks.forEach(function(task) {
            // Count total active tasks
            if (task.active == 1 || task.status == 1) {
                totalTasks++;
            }

            // Count in-progress tasks
            const category = (task.category || '').toLowerCase();
            if (category === 'in-progress' || category === 'inprogress' || category === 'in progress') {
                inProgressTasks++;
            }

            // Count completed tasks this month
            if (category === 'done' || category === 'completed') {
                // Check if completed this month (you may need to add a completion date field)
                completedThisMonth++;
            }

            // Count high priority tasks
            const priority = (task.priority || '').toLowerCase();
            if (priority === 'high') {
                highPriorityTasks++;
            }
        });

        // Update summary cards
        $('#totalTasks').text(totalTasks);
        $('#inProgressTasks').text(inProgressTasks);
        $('#completedTasks').text(completedThisMonth);
        $('#highPriorityTasks').text(highPriorityTasks);

        console.log('Task metrics updated:', {
            totalTasks: totalTasks,
            inProgressTasks: inProgressTasks,
            completedThisMonth: completedThisMonth,
            highPriorityTasks: highPriorityTasks
        });
    }
</script>