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
</style>

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
    });
</script>