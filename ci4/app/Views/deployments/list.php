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
    <div class="d-flex justify-content-between mb-3" style="padding-bottom: 0;">
        <div>
            <a href="/deployments/managePermissions" class="btn btn-warning">
                <i class="fa fa-shield"></i> Manage Permissions
            </a>
        </div>
        <div>
            <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
                <i class="fa fa-refresh"></i> Refresh
            </button>
            <a href="/deployments/edit" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add New Deployment
            </a>
        </div>
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
        actions: function(data, type, row) {
            let html = '<div class="btn-group" role="group">';
            html += '<a href="/deployments/edit/' + row.uuid + '" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></a>';

            // Only show Deploy button for Planned or Failed deployments
            if (row.deployment_status === 'Planned' || row.deployment_status === 'Failed') {
                html += '<button type="button" class="btn btn-sm btn-success deploy-btn" data-uuid="' + row.uuid + '" data-environment="' + row.environment + '" title="Deploy Service"><i class="fa fa-rocket"></i> Deploy</button>';
            }

            html += '</div>';
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

    let columnsTitle = ['ID', 'Deployment', 'Service', 'Environment', 'Status', 'Type', 'Priority', 'Date', 'Deployed By', 'Actions'];
    let columnsMachineName = ['id', 'deployment_name', 'service_name', 'environment', 'deployment_status', 'deployment_type', 'priority', 'deployment_date', 'deployed_by_name', 'actions'];

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

        // Handle Deploy button click
        $(document).on('click', '.deploy-btn', function() {
            const uuid = $(this).data('uuid');
            const environment = $(this).data('environment');

            // Check if user has permission to deploy
            $.ajax({
                url: '/deployments/checkDeploymentAccess',
                method: 'POST',
                data: { deployment_uuid: uuid },
                success: function(response) {
                    if (response.can_deploy) {
                        // User has permission, show confirmation modal
                        showDeployConfirmation(uuid, environment, 'permission');
                    } else if (response.requires_passcode) {
                        // Show passcode modal
                        showPasscodeModal(uuid, environment);
                    } else {
                        // No access
                        Swal.fire({
                            icon: 'error',
                            title: 'Access Denied',
                            text: response.message,
                            confirmButtonColor: '#dc2626'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to check deployment permissions',
                        confirmButtonColor: '#dc2626'
                    });
                }
            });
        });
    });

    function showPasscodeModal(uuid, environment) {
        Swal.fire({
            title: 'Deployment Passcode Required',
            html: `
                <p class="mb-3">You need a valid passcode to deploy to <strong>${environment}</strong> environment.</p>
                <input type="text" id="deployment-passcode" class="swal2-input" placeholder="Enter 6-digit passcode" maxlength="6" style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem;">
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Verify & Deploy',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            preConfirm: () => {
                const passcode = document.getElementById('deployment-passcode').value;
                if (!passcode) {
                    Swal.showValidationMessage('Please enter a passcode');
                    return false;
                }
                if (passcode.length !== 6) {
                    Swal.showValidationMessage('Passcode must be 6 digits');
                    return false;
                }
                return passcode;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                verifyPasscodeAndDeploy(uuid, environment, result.value);
            }
        });
    }

    function verifyPasscodeAndDeploy(uuid, environment, passcode) {
        $.ajax({
            url: '/deployments/executeDeployment',
            method: 'POST',
            data: {
                deployment_uuid: uuid,
                passcode: passcode
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deployment Initiated',
                        html: `
                            <p><strong>Environment:</strong> ${response.environment}</p>
                            <p><strong>Method:</strong> ${response.method === 'passcode' ? 'Passcode Verified' : 'Permission Granted'}</p>
                            <p class="mt-3">The deployment is now in progress.</p>
                        `,
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        // Reload the table
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Deployment Failed',
                        text: response.message,
                        confirmButtonColor: '#dc2626'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to execute deployment',
                    confirmButtonColor: '#dc2626'
                });
            }
        });
    }

    function showDeployConfirmation(uuid, environment, method) {
        Swal.fire({
            title: 'Confirm Deployment',
            html: `
                <p>Are you sure you want to deploy this service to <strong>${environment}</strong>?</p>
                <p class="text-muted mt-2">This action will update the deployment status to "In Progress".</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Deploy Now',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                executeDeployment(uuid);
            }
        });
    }

    function executeDeployment(uuid) {
        $.ajax({
            url: '/deployments/executeDeployment',
            method: 'POST',
            data: { deployment_uuid: uuid },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deployment Initiated',
                        html: `
                            <p><strong>Environment:</strong> ${response.environment}</p>
                            <p><strong>Method:</strong> ${response.method === 'passcode' ? 'Passcode Verified' : 'Permission Granted'}</p>
                            <p class="mt-3">The deployment is now in progress.</p>
                        `,
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Deployment Failed',
                        text: response.message,
                        confirmButtonColor: '#dc2626'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to execute deployment',
                    confirmButtonColor: '#dc2626'
                });
            }
        });
    }
</script>
