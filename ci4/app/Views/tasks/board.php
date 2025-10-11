<?php require_once(APPPATH . 'Views/tasks/list-title.php'); ?>

<style>
    /* All core JIRA theme styles now loaded globally from jira-theme.css */
    /* Only task-specific overrides remain here */

    /* Increase overall font size by 2-3px */
    .kanban-board-container {
        font-size: 15px;
    }

    .task-card {
        font-size: 14px;
    }

    .task-title {
        font-size: 15px !important;
    }

    .task-description {
        font-size: 13px !important;
    }

    .kanban-column-title {
        font-size: 16px !important;
    }

    .task-meta, .task-footer {
        font-size: 13px !important;
    }

    .jira-filter-select {
        font-size: 14px !important;
    }

    .task-project {
        padding: 2px 6px;
        background: #f3f4f6;
        border-radius: 4px;
        font-size: 12px;
    }
</style>

<div class="white_card card_height_100 mb_20">
<div class="kanban-board-container">
    <!-- Board Filters -->
    <div class="jira-filters">
        <select class="jira-filter-select" id="filterProject">
            <option value="">All Projects</option>
            <?php foreach ($projects as $project): ?>
                <option value="<?= $project['id'] ?>"><?= $project['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <select class="jira-filter-select" id="filterSprint">
            <option value="">All Sprints</option>
            <?php foreach ($sprints as $sprint): ?>
                <option value="<?= $sprint['id'] ?>"><?= $sprint['sprint_name'] ?></option>
            <?php endforeach; ?>
        </select>

        <select class="jira-filter-select" id="filterPriority">
            <option value="">All Priorities</option>
            <option value="high">High Priority</option>
            <option value="medium">Medium Priority</option>
            <option value="low">Low Priority</option>
        </select>

        <select class="jira-filter-select" id="filterAssignee">
            <option value="">All Assignees</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Kanban Board -->
    <div class="kanban-board" id="kanbanBoard">
        <!-- Backlog Column -->
        <div class="kanban-column" data-status="backlog">
            <div class="kanban-column-header">
                <div class="kanban-column-title">
                    <span class="status-indicator backlog"></span>
                    Backlog
                    <span class="kanban-column-count" id="count-backlog">0</span>
                </div>
            </div>
            <div class="kanban-column-body jira-scrollbar" id="column-backlog" data-status="backlog">
                <div class="jira-loading">
                    <div class="jira-spinner"></div>
                </div>
            </div>
        </div>

        <!-- To Do Column -->
        <div class="kanban-column" data-status="todo">
            <div class="kanban-column-header">
                <div class="kanban-column-title">
                    <span class="status-indicator todo"></span>
                    To Do
                    <span class="kanban-column-count" id="count-todo">0</span>
                </div>
            </div>
            <div class="kanban-column-body jira-scrollbar" id="column-todo" data-status="todo">
                <div class="jira-loading">
                    <div class="jira-spinner"></div>
                </div>
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="kanban-column" data-status="in-progress">
            <div class="kanban-column-header">
                <div class="kanban-column-title">
                    <span class="status-indicator in-progress"></span>
                    In Progress
                    <span class="kanban-column-count" id="count-in-progress">0</span>
                </div>
            </div>
            <div class="kanban-column-body jira-scrollbar" id="column-in-progress" data-status="in-progress">
                <div class="jira-loading">
                    <div class="jira-spinner"></div>
                </div>
            </div>
        </div>

        <!-- Review Column -->
        <div class="kanban-column" data-status="review">
            <div class="kanban-column-header">
                <div class="kanban-column-title">
                    <span class="status-indicator review"></span>
                    Review
                    <span class="kanban-column-count" id="count-review">0</span>
                </div>
            </div>
            <div class="kanban-column-body jira-scrollbar" id="column-review" data-status="review">
                <div class="jira-loading">
                    <div class="jira-spinner"></div>
                </div>
            </div>
        </div>

        <!-- Done Column -->
        <div class="kanban-column" data-status="done">
            <div class="kanban-column-header">
                <div class="kanban-column-title">
                    <span class="status-indicator done"></span>
                    Done
                    <span class="kanban-column-count" id="count-done">0</span>
                </div>
            </div>
            <div class="kanban-column-body jira-scrollbar" id="column-done" data-status="done">
                <div class="jira-loading">
                    <div class="jira-spinner"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
let allTasks = [];
let filters = {
    project: '',
    sprint: '',
    priority: '',
    assignee: ''
};

// Load tasks from API
function loadTasks() {
    $.ajax({
        url: '/tasks/boardData',
        method: 'GET',
        success: function(response) {
            if (response.status) {
                allTasks = response.data;
                renderBoard();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading tasks:', error);
        }
    });
}

// Render board with filtered tasks
function renderBoard() {
    const statuses = ['backlog', 'todo', 'in-progress', 'review', 'done'];

    statuses.forEach(status => {
        const columnBody = document.getElementById(`column-${status}`);
        const filteredTasks = getFilteredTasks(status);

        columnBody.innerHTML = '';

        if (filteredTasks.length === 0) {
            columnBody.innerHTML = `
                <div class="jira-empty-state">
                    <div class="jira-empty-state-icon"><i class="fa fa-inbox"></i></div>
                    <div class="jira-empty-state-text">No tasks</div>
                </div>
            `;
        } else {
            filteredTasks.forEach(task => {
                columnBody.appendChild(createTaskCard(task));
            });
        }

        // Update count
        document.getElementById(`count-${status}`).textContent = filteredTasks.length;
    });

    // Initialize drag and drop
    initializeDragDrop();
}

// Get filtered tasks for a specific status
function getFilteredTasks(status) {
    return allTasks.filter(task => {
        // Status filter
        const taskCategory = task.category || 'backlog';
        if (taskCategory !== status) return false;

        // Project filter
        if (filters.project && task.projects_id != filters.project) return false;

        // Sprint filter
        if (filters.sprint && task.sprint_id != filters.sprint) return false;

        // Priority filter
        if (filters.priority && task.priority !== filters.priority) return false;

        // Assignee filter
        if (filters.assignee && task.assigned_to != filters.assignee) return false;

        return true;
    });
}

// Create task card element
function createTaskCard(task) {
    const card = document.createElement('div');
    card.className = 'task-card';
    card.draggable = true;
    card.dataset.taskId = task.id;
    card.dataset.taskUuid = task.uuid;

    const priorityClass = task.priority ? `priority-${task.priority}` : 'priority-medium';
    const assigneeInitials = task.assigned_to_name ? task.assigned_to_name.split(' ').map(n => n[0]).join('').toUpperCase() : '?';

    const description = task.description ?
        task.description.replace(/<[^>]*>/g, '').substring(0, 100) :
        'No description';

    card.innerHTML = `
        <div class="task-card-header">
            <span class="task-id">#${task.id}</span>
            ${task.priority ? `<span class="task-priority ${priorityClass}">${task.priority}</span>` : ''}
        </div>
        <div class="task-title">${task.name}</div>
        <div class="task-description">${description}...</div>
        <div class="task-meta">
            ${task.project_name ? `<span class="task-project">${task.project_name}</span>` : ''}
            ${task.estimated_hour ? `<span class="task-meta-item"><i class="fa fa-clock-o"></i> ${task.estimated_hour}h</span>` : ''}
            ${task.assigned_to_name ? `
                <div class="task-assignee">
                    <div class="assignee-avatar">${assigneeInitials}</div>
                </div>
            ` : ''}
        </div>
    `;

    // Click to edit
    card.addEventListener('click', function(e) {
        if (!e.target.closest('.task-priority')) {
            window.location.href = `/tasks/edit/${task.uuid}`;
        }
    });

    return card;
}

// Initialize drag and drop
function initializeDragDrop() {
    const cards = document.querySelectorAll('.task-card');
    const columns = document.querySelectorAll('.kanban-column-body');

    cards.forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
    });

    columns.forEach(column => {
        column.addEventListener('dragover', handleDragOver);
        column.addEventListener('drop', handleDrop);
        column.addEventListener('dragleave', handleDragLeave);
    });
}

let draggedTask = null;

function handleDragStart(e) {
    draggedTask = e.target;
    e.target.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
    document.querySelectorAll('.drop-zone-active').forEach(el => {
        el.classList.remove('drop-zone-active');
    });
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    e.target.classList.add('drop-zone-active');
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDragLeave(e) {
    e.target.classList.remove('drop-zone-active');
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    e.preventDefault();

    const columnBody = e.target.closest('.kanban-column-body');
    if (!columnBody) return;

    const newStatus = columnBody.dataset.status;
    const taskUuid = draggedTask.dataset.taskUuid;

    // Update task status via API
    updateTaskStatus(taskUuid, newStatus);

    return false;
}

// Update task status
function updateTaskStatus(taskUuid, newStatus) {
    $.ajax({
        url: '/tasks/updateStatus',
        method: 'POST',
        data: {
            uuid: taskUuid,
            category: newStatus
        },
        success: function(response) {
            if (response.status) {
                // Update local data
                const task = allTasks.find(t => t.uuid === taskUuid);
                if (task) {
                    task.category = newStatus;
                }
                renderBoard();

                // Show success message
                toastr.success('Task status updated successfully');
            } else {
                toastr.error('Failed to update task status');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating task:', error);
            toastr.error('Error updating task status');
            loadTasks(); // Reload to revert
        }
    });
}

// Filter handlers
$('#filterProject').on('change', function() {
    filters.project = $(this).val();
    renderBoard();
});

$('#filterSprint').on('change', function() {
    filters.sprint = $(this).val();
    renderBoard();
});

$('#filterPriority').on('change', function() {
    filters.priority = $(this).val();
    renderBoard();
});

$('#filterAssignee').on('change', function() {
    filters.assignee = $(this).val();
    renderBoard();
});

// Initialize
$(document).ready(function() {
    loadTasks();

    // Refresh every 30 seconds
    setInterval(loadTasks, 30000);
});
</script>
