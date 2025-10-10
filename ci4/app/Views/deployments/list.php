<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<style>
    .deployment-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .env-development { background: #dbeafe; color: #1e40af; }
    .env-testing { background: #fef3c7; color: #92400e; }
    .env-acceptance { background: #fce7f3; color: #831843; }
    .env-production { background: #dcfce7; color: #14532d; }

    .status-planned { background: #e0e7ff; color: #3730a3; }
    .status-inprogress { background: #dbeafe; color: #1e40af; }
    .status-completed { background: #d1fae5; color: #065f46; }
    .status-failed { background: #fee2e2; color: #991b1b; }
    .status-rolledback { background: #fef3c7; color: #92400e; }

    .priority-low { color: #6b7280; }
    .priority-medium { color: #2563eb; }
    .priority-high { color: #dc2626; }
    .priority-critical { background: #dc2626; color: white; padding: 2px 8px; border-radius: 10px; }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/deployments/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Deployment
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-rocket"></i> Total Deployments</div>
            <div class="summary-card-value" id="totalDeployments">0</div>
            <div class="summary-card-subtitle">All time</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Completed</div>
            <div class="summary-card-value" id="completedDeployments">0</div>
            <div class="summary-card-subtitle">Successful deployments</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-spinner"></i> In Progress</div>
            <div class="summary-card-value" id="inProgressDeployments">0</div>
            <div class="summary-card-subtitle">Currently deploying</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-calendar"></i> This Month</div>
            <div class="summary-card-value" id="monthlyDeployments">0</div>
            <div class="summary-card-subtitle">Deployments this month</div>
        </div>
    </div>
</div>

<!-- Deployments Table -->
<div class="white_card_body">
    <div class="QA_table" id="deploymentsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>
    const columnRenderers = {
        deployment_name: function(data, type, row) {
            let html = '<a href="/deployments/edit/' + row.uuid + '" style="color: #667eea; font-weight: 600;">' + data + '</a>';
            if (row.version) {
                html += '<br><small style="color: #6b7280;">v' + row.version + '</small>';
            }
            return html;
        },
        service_name: function(data, type, row) {
            if (!data) return '<span style="color: #9ca3af;">-</span>';
            return '<a href="/services/edit/' + row.uuid_service_id + '" style="color: #667eea;">' + data + '</a>';
        },
        environment: function(data, type, row) {
            const envClass = 'env-' + data.toLowerCase();
            return '<span class="deployment-badge ' + envClass + '">' + data + '</span>';
        },
        deployment_status: function(data, type, row) {
            const statusClass = 'status-' + data.toLowerCase().replace(' ', '');
            return '<span class="deployment-badge ' + statusClass + '">' + data + '</span>';
        },
        deployment_type: function(data, type, row) {
            const icons = {
                'Initial': 'fa-star',
                'Update': 'fa-arrow-up',
                'Hotfix': 'fa-fire',
                'Rollback': 'fa-undo',
                'Configuration': 'fa-cog'
            };
            return '<i class="fa ' + (icons[data] || 'fa-circle') + '"></i> ' + data;
        },
        priority: function(data, type, row) {
            const priorityClass = 'priority-' + data.toLowerCase();
            return '<span class="' + priorityClass + '"><i class="fa fa-flag"></i> ' + data + '</span>';
        },
        deployment_date: function(data, type, row) {
            if (!data) return '<span style="color: #9ca3af;">Not scheduled</span>';
            const date = new Date(data);
            return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) +
                   '<br><small>' + date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }) + '</small>';
        },
        deployed_by_name: function(data, type, row) {
            if (!data) return '<span style="color: #9ca3af;">-</span>';
            return '<i class="fa fa-user"></i> ' + data;
        }
    };

    let columnsTitle = ['ID', 'Deployment', 'Service', 'Environment', 'Status', 'Type', 'Priority', 'Date', 'Deployed By'];
    let columnsMachineName = ['id', 'deployment_name', 'service_name', 'environment', 'deployment_status', 'deployment_type', 'priority', 'deployment_date', 'deployed_by_name'];

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "deployments",
        apiPath: "deployments/deploymentsList",
        selector: "deploymentsTable",
        columnRenderers: columnRenderers
    });

    // Load summary data
    function updateSummaryCards() {
        fetch('/api/v2/deployments?uuid_business_id=' + encodeURIComponent('<?= session('uuid_business') ?>'))
            .then(response => response.json())
            .then(result => {
                if (result.data) {
                    calculateMetrics(result.data);
                }
            })
            .catch(error => console.error('Error loading summary:', error));
    }

    function calculateMetrics(data) {
        // Total deployments
        $('#totalDeployments').text(data.length);

        // Completed deployments
        const completed = data.filter(d => d.deployment_status === 'Completed').length;
        $('#completedDeployments').text(completed);

        // In Progress deployments
        const inProgress = data.filter(d => d.deployment_status === 'In Progress').length;
        $('#inProgressDeployments').text(inProgress);

        // This month's deployments
        const now = new Date();
        const thisMonth = data.filter(d => {
            if (!d.deployment_date) return false;
            const deployDate = new Date(d.deployment_date);
            return deployDate.getMonth() === now.getMonth() &&
                   deployDate.getFullYear() === now.getFullYear();
        }).length;
        $('#monthlyDeployments').text(thisMonth);
    }

    // Initialize
    $(document).ready(function() {
        updateSummaryCards();
    });
</script>
