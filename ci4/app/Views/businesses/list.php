<?php
require_once (APPPATH . 'Views/common/list-title.php');
$roles = getResultWithoutBusiness("roles", ["uuid" => $_SESSION['role']], false);
?>

<style>
    /* Business-specific overrides */
    .business-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .business-link:hover {
        text-decoration: underline;
    }

    .business-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        margin-left: 8px;
    }

    .badge-default {
        background: #10b981;
        color: white;
    }

    .business-contact-info {
        font-size: 0.8rem;
        color: #6b7280;
    }

    .business-contact-info i {
        margin-right: 4px;
        color: #667eea;
    }
</style>

<div class="white_card_body">
    <!-- Action Buttons -->
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/businesses/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Business
        </a>
    </div>
</div>

<div class="white_card_body">
    <!-- Summary Cards using JIRA theme -->
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-building"></i> Total Businesses</div>
            <div class="summary-card-value" id="totalBusinesses">0</div>
            <div class="summary-card-subtitle">Workspaces configured</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Active</div>
            <div class="summary-card-value" id="activeBusinesses">0</div>
            <div class="summary-card-subtitle">Currently active</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-star"></i> Default Business</div>
            <div class="summary-card-value" id="defaultBusiness">1</div>
            <div class="summary-card-subtitle">Primary workspace</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-calendar"></i> This Month</div>
            <div class="summary-card-value" id="recentBusinesses">0</div>
            <div class="summary-card-subtitle">Created this month</div>
        </div>
    </div>

    <!-- DataTable -->
    <div class="QA_table businessTable" id="businessTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    const columnRenderers = {
        name: function(data, type, row) {
            let html = '<a href="/businesses/edit/' + row.uuid + '" class="business-link">' + data + '</a>';
            if (row.default_business == 1) {
                html += '<span class="business-badge badge-default"><i class="fa fa-star"></i> Default</span>';
            }
            return html;
        },
        email: function(data, type, row) {
            if (!data) return '<span style="color: #9ca3af;">-</span>';
            return '<span class="business-contact-info"><i class="fa fa-envelope"></i>' + data + '</span>';
        },
        telephone_no: function(data, type, row) {
            if (!data) return '<span style="color: #9ca3af;">-</span>';
            return '<span class="business-contact-info"><i class="fa fa-phone"></i>' + data + '</span>';
        },
        web_site: function(data, type, row) {
            if (!data) return '<span style="color: #9ca3af;">-</span>';
            return '<a href="' + data + '" target="_blank" class="business-link"><i class="fa fa-globe"></i> Website</a>';
        },
        created_at: function(data, type, row) {
            if (!data) return '-';
            const date = new Date(data);
            return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }
    };

    let columnsTitle = ['Id', 'Business Name', 'Email', 'Phone', 'Website', 'Created'];
    let columnsMachineName = ['id', 'name', 'email', 'telephone_no', 'web_site', 'created_at'];

    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "businesses",
            apiPath: "api/v2/businesses",
            selector: "businessTable",
            columnRenderers: columnRenderers
        }
    );

    // Load summary data
    function updateSummaryCards() {
        fetch('/api/v2/businesses?limit=10000&offset=0')
            .then(response => response.json())
            .then(result => {
                if (result.data) {
                    calculateMetrics(result.data);
                }
            })
            .catch(error => console.error('Error loading summary:', error));
    }

    function calculateMetrics(data) {
        // Total businesses
        const total = data.length;
        $('#totalBusinesses').text(total);

        // Active businesses (all for now, can be enhanced)
        $('#activeBusinesses').text(total);

        // Businesses created this month
        const now = new Date();
        const thisMonth = data.filter(b => {
            const created = new Date(b.created_at);
            return created.getMonth() === now.getMonth() &&
                   created.getFullYear() === now.getFullYear();
        }).length;
        $('#recentBusinesses').text(thisMonth);

        // Default business count (should be 1)
        const defaultCount = data.filter(b => b.default_business == 1).length;
        $('#defaultBusiness').text(defaultCount);
    }

    // Initialize
    $(document).ready(function() {
        updateSummaryCards();
    });
</script>