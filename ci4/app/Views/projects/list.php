<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<style>
    /* Summary Cards */

    /* View Toggle */
    .view-toggle {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        align-items: center;
    }

    .view-btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: 2px solid #667eea;
        background: white;
        color: #667eea;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .view-btn.active {
        background: #667eea;
        color: white;
    }

    .view-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    }

    /* Quick Actions */
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

    .quick-action-btn.success { background-color: #10b981; color: white; }
    .quick-action-btn.primary { background-color: #667eea; color: white; }
    .quick-action-btn.secondary { background-color: #6366f1; color: white; }

    /* Kanban Board */
    .kanban-board {
        display: none;
        gap: 20px;
        overflow-x: auto;
        padding-bottom: 20px;
    }

    .kanban-board.active {
        display: flex;
    }

    .kanban-column {
        min-width: 300px;
        background: #f9fafb;
        border-radius: 12px;
        padding: 16px;
        flex-shrink: 0;
    }

    .kanban-column-header {
        font-weight: 700;
        font-size: 0.875rem;
        text-transform: uppercase;
        color: #374151;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .kanban-count {
        background: #667eea;
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 0.75rem;
    }

    .kanban-card {
        background: white;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.2s;
        border-left: 4px solid #667eea;
    }

    .kanban-card:hover {
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .kanban-card-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .kanban-card-customer {
        font-size: 0.8rem;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .kanban-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
        font-size: 0.75rem;
    }

    .kanban-card-progress {
        width: 100%;
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        margin: 8px 0;
    }

    .kanban-card-progress-bar {
        height: 100%;
        background: #10b981;
        border-radius: 2px;
        transition: width 0.3s;
    }

    /* Tags */
    .tag {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        margin: 2px;
        color: white;
    }

    /* Status Badges */
    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        text-transform: capitalize;
    }

    .status-planning { background-color: #e5e7eb; color: #374151; }
    .status-active { background-color: #d1fae5; color: #065f46; }
    .status-on-hold { background-color: #fef3c7; color: #92400e; }
    .status-completed { background-color: #dbeafe; color: #1e40af; }
    .status-cancelled { background-color: #fee2e2; color: #991b1b; }

    /* Priority Badges */
    .priority-badge {
        padding: 2px 8px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-block;
    }

    .priority-low { background-color: #dbeafe; color: #1e40af; }
    .priority-medium { background-color: #fef3c7; color: #92400e; }
    .priority-high { background-color: #fed7aa; color: #9a3412; }
    .priority-critical { background-color: #fee2e2; color: #991b1b; }

    /* Progress Bar */
    .progress-bar-container {
        width: 80px;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
        display: inline-block;
        vertical-align: middle;
    }

    .progress-bar {
        height: 100%;
        background: #10b981;
        transition: width 0.3s;
    }

    /* List View */
    .list-view {
        display: block;
    }

    .list-view.hidden {
        display: none;
    }

    .project-link {
        color: #667eea;
        font-weight: 600;
        text-decoration: none;
    }

    .project-link:hover {
        text-decoration: underline;
    }

    .amount-cell {
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button class="btn btn-primary mr-2" onclick="switchView('list')" id="listViewBtn">
            <i class="fa fa-list"></i> List View
        </button>
        <button class="btn btn-outline-primary mr-2" onclick="switchView('kanban')" id="kanbanViewBtn">
            <i class="fa fa-columns"></i> Board View
        </button>
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/projects/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Project
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-project-diagram"></i> Active Projects</div>
            <div class="summary-card-value" id="activeProjects">0</div>
            <div class="summary-card-subtitle">currently running</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> On Track</div>
            <div class="summary-card-value" id="onTrackProjects">0</div>
            <div class="summary-card-subtitle">meeting deadlines</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-exclamation-triangle"></i> At Risk</div>
            <div class="summary-card-value" id="atRiskProjects">0</div>
            <div class="summary-card-subtitle">needs attention</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-money-bill-wave"></i> Total Budget</div>
            <div class="summary-card-value" id="totalBudget">£0</div>
            <div class="summary-card-subtitle">all projects</div>
        </div>
    </div>
</div>

<!-- Kanban Board View -->
<div class="kanban-board" id="kanbanBoard">
    <div class="kanban-column">
        <div class="kanban-column-header">
            <span>Planning</span>
            <span class="kanban-count" id="planningCount">0</span>
        </div>
        <div id="planningColumn"></div>
    </div>

    <div class="kanban-column">
        <div class="kanban-column-header">
            <span>Active</span>
            <span class="kanban-count" id="activeCount">0</span>
        </div>
        <div id="activeColumn"></div>
    </div>

    <div class="kanban-column">
        <div class="kanban-column-header">
            <span>On Hold</span>
            <span class="kanban-count" id="onHoldCount">0</span>
        </div>
        <div id="onHoldColumn"></div>
    </div>

    <div class="kanban-column">
        <div class="kanban-column-header">
            <span>Completed</span>
            <span class="kanban-count" id="completedCount">0</span>
        </div>
        <div id="completedColumn"></div>
    </div>
</div>

<!-- List View -->
<div class="white_card_body list-view" id="listView">
    <div class="QA_table" id="projectsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
let currentView = 'list';
let projectsData = [];

// Column configuration
let columnsTitle = ['Id', 'Project', 'Customer', 'Status', 'Priority', 'Progress', 'Budget', 'Deadline', 'PM', 'Tags'];
let columnsMachineName = ['id', 'name', 'customer_name', 'status', 'priority', 'progress', 'budget', 'deadline_date', 'project_manager_name', 'tag_names'];

// Custom column renderers
const columnRenderers = {
    name: function(data, type, row) {
        const color = row.color || '#3b82f6';
        return '<div style="border-left: 4px solid ' + color + '; padding-left: 8px;"><a href="/projects/edit/' + row.uuid + '" class="project-link">' + data + '</a></div>';
    },
    status: function(data, type, row) {
        const status = (data || 'planning').toLowerCase();
        return '<span class="status-badge status-' + status + '">' + data + '</span>';
    },
    priority: function(data, type, row) {
        const priority = (data || 'medium').toLowerCase();
        return '<span class="priority-badge priority-' + priority + '">' + data + '</span>';
    },
    progress: function(data, type, row) {
        const progress = parseInt(data || 0);
        return '<div style="display: flex; align-items: center; gap: 8px;"><div class="progress-bar-container"><div class="progress-bar" style="width: ' + progress + '%"></div></div><span style="font-size: 0.8rem;">' + progress + '%</span></div>';
    },
    budget: function(data, type, row) {
        const amount = parseFloat(data || 0);
        const currency = row.currency || '£';
        return '<span class="amount-cell">' + currency + amount.toFixed(0) + '</span>';
    },
    deadline_date: function(data, type, row) {
        if (!data) return '-';
        const date = new Date(parseInt(data) * 1000);
        const today = new Date();
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        let dateStr = date.toLocaleDateString('en-GB', options);

        // Highlight if overdue
        if (date < today && row.status !== 'completed') {
            dateStr = '<span style="color: #dc2626; font-weight: 600;">' + dateStr + '</span>';
        }

        return dateStr;
    },
    tag_names: function(data, type, row) {
        if (!data) return '-';

        const tagNames = data.split(', ');
        const tagColors = (row.tag_colors || '').split(',');

        let html = '';
        tagNames.forEach((tag, index) => {
            const color = tagColors[index] || '#667eea';
            html += '<span class="tag" style="background-color: ' + color + '">' + tag + '</span>';
        });

        return html || '-';
    }
};

// Initialize data table
initializeGridTable({
    columnsTitle,
    columnsMachineName,
    tableName: "projects",
    apiPath: "projects/projectsList",
    selector: "projectsTable",
    columnRenderers: columnRenderers
});

// Switch between views
function switchView(view) {
    currentView = view;

    if (view === 'list') {
        document.getElementById('listView').classList.remove('hidden');
        document.getElementById('kanbanBoard').classList.remove('active');

        // Update button styles
        document.getElementById('listViewBtn').className = 'btn btn-primary mr-2';
        document.getElementById('kanbanViewBtn').className = 'btn btn-outline-primary mr-2';
    } else {
        document.getElementById('listView').classList.add('hidden');
        document.getElementById('kanbanBoard').classList.add('active');

        // Update button styles
        document.getElementById('listViewBtn').className = 'btn btn-outline-primary mr-2';
        document.getElementById('kanbanViewBtn').className = 'btn btn-primary mr-2';
        renderKanbanBoard();
    }
}

// Render Kanban board
function renderKanbanBoard() {
    if (projectsData.length === 0) {
        fetchProjectsForKanban();
        return;
    }

    // Clear columns
    document.getElementById('planningColumn').innerHTML = '';
    document.getElementById('activeColumn').innerHTML = '';
    document.getElementById('onHoldColumn').innerHTML = '';
    document.getElementById('completedColumn').innerHTML = '';

    let planningCount = 0, activeCount = 0, onHoldCount = 0, completedCount = 0;

    projectsData.forEach(project => {
        const card = createKanbanCard(project);
        const status = (project.status || 'planning').toLowerCase();

        if (status === 'planning') {
            document.getElementById('planningColumn').innerHTML += card;
            planningCount++;
        } else if (status === 'active') {
            document.getElementById('activeColumn').innerHTML += card;
            activeCount++;
        } else if (status === 'on-hold') {
            document.getElementById('onHoldColumn').innerHTML += card;
            onHoldCount++;
        } else if (status === 'completed') {
            document.getElementById('completedColumn').innerHTML += card;
            completedCount++;
        }
    });

    document.getElementById('planningCount').textContent = planningCount;
    document.getElementById('activeCount').textContent = activeCount;
    document.getElementById('onHoldCount').textContent = onHoldCount;
    document.getElementById('completedCount').textContent = completedCount;
}

// Create Kanban card HTML
function createKanbanCard(project) {
    const color = project.color || '#667eea';
    const progress = parseInt(project.progress || 0);
    const priority = project.priority || 'medium';
    const budget = parseFloat(project.budget || 0);
    const currency = project.currency || '£';

    let tagsHtml = '';
    if (project.tag_names) {
        const tagNames = project.tag_names.split(', ');
        const tagColors = (project.tag_colors || '').split(',');
        tagNames.forEach((tag, index) => {
            const tagColor = tagColors[index] || '#667eea';
            tagsHtml += '<span class="tag" style="background-color: ' + tagColor + '">' + tag + '</span>';
        });
    }

    return `
        <div class="kanban-card" style="border-left-color: ${color}" onclick="window.location.href='/projects/edit/${project.uuid}'">
            <div class="kanban-card-title">${project.name}</div>
            <div class="kanban-card-customer">${project.customer_name || 'No Customer'}</div>
            ${tagsHtml}
            <div class="kanban-card-progress">
                <div class="kanban-card-progress-bar" style="width: ${progress}%"></div>
            </div>
            <div class="kanban-card-footer">
                <span class="priority-badge priority-${priority.toLowerCase()}">${priority}</span>
                <span class="amount-cell">${currency}${budget.toFixed(0)}</span>
            </div>
        </div>
    `;
}

// Fetch projects for Kanban
function fetchProjectsForKanban() {
    const businessUuid = '<?php echo session('uuid_business'); ?>';

    fetch('/projects/projectsList?limit=10000&offset=0&uuid_business_id=' + businessUuid)
        .then(response => response.json())
        .then(result => {
            if (result && result.data) {
                projectsData = result.data;
                renderKanbanBoard();
            }
        })
        .catch(error => console.error('Error fetching projects for Kanban:', error));
}

// Update summary cards
function updateProjectSummaryCards() {
    const businessUuid = '<?php echo session('uuid_business'); ?>';

    fetch('/projects/projectsList?limit=10000&offset=0&uuid_business_id=' + businessUuid)
        .then(response => response.json())
        .then(result => {
            if (result && result.data) {
                projectsData = result.data;
                calculateProjectMetrics(result.data);
            }
        })
        .catch(error => console.error('Error fetching project summary:', error));
}

// Calculate project metrics
function calculateProjectMetrics(projects) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let activeCount = 0;
    let onTrackCount = 0;
    let atRiskCount = 0;
    let totalBudget = 0;
    let totalHours = 0;
    let totalProgress = 0;
    let progressCount = 0;

    projects.forEach(project => {
        const status = (project.status || 'planning').toLowerCase();
        const budget = parseFloat(project.budget || 0);
        const hours = parseFloat(project.actual_hours || 0);
        const progress = parseInt(project.progress || 0);
        const deadline = project.deadline_date ? new Date(parseInt(project.deadline_date) * 1000) : null;

        // Active projects
        if (status === 'active') {
            activeCount++;

            // On track vs at risk
            if (deadline) {
                if (deadline >= today || progress >= 80) {
                    onTrackCount++;
                } else {
                    atRiskCount++;
                }
            } else {
                onTrackCount++;
            }
        }

        // Budget
        totalBudget += budget;

        // Hours
        totalHours += hours;

        // Progress
        if (status !== 'cancelled') {
            totalProgress += progress;
            progressCount++;
        }
    });

    const avgProgress = progressCount > 0 ? Math.round(totalProgress / progressCount) : 0;

    // Update UI
    $('#activeProjects').text(activeCount);
    $('#onTrackProjects').text(onTrackCount);
    $('#atRiskProjects').text(atRiskCount);
    $('#totalBudget').text('£' + totalBudget.toFixed(0));
    $('#totalHours').text(totalHours.toFixed(0));
    $('#completionRate').text(avgProgress + '%');

    console.log('Project metrics updated:', {
        active: activeCount,
        onTrack: onTrackCount,
        atRisk: atRiskCount,
        totalBudget: totalBudget,
        totalHours: totalHours,
        avgProgress: avgProgress
    });
}

// Initialize on page load
$(document).ready(function() {
    setTimeout(function() {
        updateProjectSummaryCards();
    }, 1000);
});
</script>
