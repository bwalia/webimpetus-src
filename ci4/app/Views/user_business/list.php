<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<style>

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

    .user-link {
        color: #667eea;
        font-weight: 600;
        text-decoration: none;
    }

    .user-link:hover {
        text-decoration: underline;
    }

    .business-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        background-color: #dbeafe;
        color: #1e40af;
        margin: 2px;
    }

    .business-badge.primary {
        background-color: #d1fae5;
        color: #065f46;
    }

    .business-count {
        background: #667eea;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 8px;
    }
</style>

<!-- Summary Cards -->
<div class="user-business-summary-cards">
    <div class="summary-card">
        <div class="summary-card-title"><i class="fa fa-users"></i> Total Users</div>
        <div class="summary-card-value" id="totalUsers">0</div>
        <div class="summary-card-subtitle">with business access</div>
    </div>

    <div class="summary-card green">
        <div class="summary-card-title"><i class="fa fa-building"></i> Total Businesses</div>
        <div class="summary-card-value" id="totalBusinesses">0</div>
        <div class="summary-card-subtitle">in system</div>
    </div>

    <div class="summary-card orange">
        <div class="summary-card-title"><i class="fa fa-link"></i> Total Mappings</div>
        <div class="summary-card-value" id="totalMappings">0</div>
        <div class="summary-card-subtitle">user-business links</div>
    </div>

    <div class="summary-card blue">
        <div class="summary-card-title"><i class="fa fa-user-check"></i> Avg per User</div>
        <div class="summary-card-value" id="avgPerUser">0</div>
        <div class="summary-card-subtitle">businesses/user</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="/user_business/edit" class="quick-action-btn success">
        <i class="fa fa-plus"></i> Assign User to Business
    </a>
    <button class="quick-action-btn primary" onclick="window.location.reload()">
        <i class="fa fa-sync"></i> Refresh
    </button>
</div>

<div class="white_card_body">
    <div class="QA_table userBusinessTable" id="userBusinessTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script>
let columnsTitle = ['Id', 'User', 'Assigned Businesses', 'Primary Business'];
let columnsMachineName = ['id', 'username', 'businesses', 'business_name'];

// Custom column renderers
const columnRenderers = {
    username: function(data, type, row) {
        return '<a href="/user_business/edit/' + row.uuid + '" class="user-link"><i class="fa fa-user"></i> ' + data + '</a>';
    },
    businesses: function(data, type, row) {
        if (!row.user_business_id) return '<span style="color: #9ca3af;">No businesses assigned</span>';

        try {
            const businesses = JSON.parse(row.user_business_id);
            if (!businesses || businesses.length === 0) {
                return '<span style="color: #9ca3af;">No businesses assigned</span>';
            }

            let html = '<span class="business-count">' + businesses.length + '</span> ';
            businesses.slice(0, 3).forEach(function(businessUuid) {
                // We'll need to fetch business names or pass them in the data
                html += '<span class="business-badge"><i class="fa fa-building"></i> Business</span>';
            });

            if (businesses.length > 3) {
                html += '<span class="business-badge">+' + (businesses.length - 3) + ' more</span>';
            }

            return html;
        } catch(e) {
            return '<span style="color: #9ca3af;">-</span>';
        }
    },
    business_name: function(data, type, row) {
        if (!data) return '<span style="color: #9ca3af;">No primary</span>';
        return '<span class="business-badge primary"><i class="fa fa-star"></i> ' + data + '</span>';
    }
};

initializeGridTable({
    columnsTitle,
    columnsMachineName,
    tableName: "user_business",
    apiPath: "user_business/userBusinessList",
    selector: "userBusinessTable",
    columnRenderers: columnRenderers
});

// Update summary cards
function updateSummaryCards() {
    fetch('/user_business/userBusinessList?limit=10000&offset=0')
        .then(response => response.json())
        .then(result => {
            if (result && result.data) {
                calculateMetrics(result.data);
            }
        })
        .catch(error => console.error('Error fetching summary:', error));
}

function calculateMetrics(data) {
    const uniqueUsers = new Set();
    const uniqueBusinesses = new Set();
    let totalMappings = 0;

    data.forEach(row => {
        if (row.user_uuid) {
            uniqueUsers.add(row.user_uuid);
        }

        if (row.user_business_id) {
            try {
                const businesses = JSON.parse(row.user_business_id);
                if (businesses && businesses.length > 0) {
                    businesses.forEach(b => uniqueBusinesses.add(b));
                    totalMappings += businesses.length;
                }
            } catch(e) {}
        }
    });

    const avgPerUser = uniqueUsers.size > 0 ? Math.round(totalMappings / uniqueUsers.size * 10) / 10 : 0;

    $('#totalUsers').text(uniqueUsers.size);
    $('#totalBusinesses').text(uniqueBusinesses.size);
    $('#totalMappings').text(totalMappings);
    $('#avgPerUser').text(avgPerUser);

    console.log('Summary updated:', {
        users: uniqueUsers.size,
        businesses: uniqueBusinesses.size,
        mappings: totalMappings,
        avg: avgPerUser
    });
}

$(document).ready(function() {
    setTimeout(function() {
        updateSummaryCards();
    }, 1000);
});
</script>
