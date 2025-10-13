<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<style>
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

    .summary-card.red::before {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
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

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/incidents/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Incident
        </a>
    </div>
</div>

<!-- Summary Cards for Incidents -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-exclamation-triangle"></i> Total Incidents</div>
            <div class="summary-card-value" id="totalIncidents">0</div>
            <div class="summary-card-subtitle">all incidents</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-hourglass-half"></i> Open</div>
            <div class="summary-card-value" id="openIncidents">0</div>
            <div class="summary-card-subtitle">pending resolution</div>
        </div>

        <div class="summary-card red">
            <div class="summary-card-title"><i class="fa fa-fire"></i> Critical</div>
            <div class="summary-card-value" id="criticalIncidents">0</div>
            <div class="summary-card-subtitle">high priority</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Resolved</div>
            <div class="summary-card-value" id="resolvedIncidents">0</div>
            <div class="summary-card-subtitle">closed this month</div>
        </div>
    </div>
</div>

<div class="white_card_body ">
    <div class="QA_table" id="incidentsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Incident #', 'Title', 'Priority', 'Status', 'Category', 'Created'];
    let columnsMachineName = ['id', 'incident_number', 'title', 'priority', 'status', 'category', 'created_at'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "incidents",
            apiPath: "incidents/incidentsList",
            selector: "incidentsTable"
        }
    );

    // Update summary cards
    $(document).ready(function() {
        setTimeout(function() {
            updateIncidentSummaryCards();
        }, 1000);
    });

    // Update summary cards with incident metrics
    function updateIncidentSummaryCards() {
        const businessUuid = '<?php echo session('uuid_business'); ?>';
        const token = '<?php echo session('jwt_token') ?? session('token') ?? ''; ?>';

        // Fetch incidents data and calculate summaries
        fetch('/incidents/incidentsList?uuid_business_id=' + businessUuid, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result && result.data) {
                    calculateIncidentMetrics(result.data);
                }
            })
            .catch(error => {
                console.error('Error fetching incident summary data:', error);
            });
    }

    function calculateIncidentMetrics(incidents) {
        const today = new Date();
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
        monthStart.setHours(0, 0, 0, 0);

        let totalIncidents = incidents.length;
        let openIncidents = 0;
        let criticalIncidents = 0;
        let resolvedThisMonth = 0;

        incidents.forEach(function(incident) {
            // Count open incidents
            const status = (incident.status || '').toLowerCase();
            if (status === 'open' || status === 'new' || status === 'in-progress' || status === 'inprogress') {
                openIncidents++;
            }

            // Count critical/high priority incidents
            const priority = (incident.priority || '').toLowerCase();
            if (priority === 'critical' || priority === 'high') {
                criticalIncidents++;
            }

            // Count resolved incidents this month
            if (status === 'resolved' || status === 'closed' || status === 'completed') {
                // Check if resolved this month (you may need to add a resolved_at date field)
                resolvedThisMonth++;
            }
        });

        // Update summary cards
        $('#totalIncidents').text(totalIncidents);
        $('#openIncidents').text(openIncidents);
        $('#criticalIncidents').text(criticalIncidents);
        $('#resolvedIncidents').text(resolvedThisMonth);

        console.log('Incident metrics updated:', {
            totalIncidents: totalIncidents,
            openIncidents: openIncidents,
            criticalIncidents: criticalIncidents,
            resolvedThisMonth: resolvedThisMonth
        });
    }
</script>
