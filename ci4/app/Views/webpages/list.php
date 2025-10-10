<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/webpages/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Page
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-file-alt"></i> Total Content Pages</div>
            <div class="summary-card-value" id="totalPages">0</div>
            <div class="summary-card-subtitle">All pages</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Active Pages</div>
            <div class="summary-card-value" id="activePages">0</div>
            <div class="summary-card-subtitle">Published content</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-pause-circle"></i> Inactive Pages</div>
            <div class="summary-card-value" id="inactivePages">0</div>
            <div class="summary-card-subtitle">Draft or unpublished</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-clock"></i> Recent Pages</div>
            <div class="summary-card-value" id="recentPages">0</div>
            <div class="summary-card-subtitle">Last 30 days</div>
        </div>
    </div>
</div>

<div class="white_card_body ">
    <div class="QA_table" id="webpagesTable"></div>
</div>
<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Title', 'Sub Title', 'Status', 'Code'];
    let columnsMachineName = ['id', 'title', 'sub_title', 'status', 'code'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "webpages",
            apiPath: "api/v2/webpages",
            selector: "webpagesTable"
        }
    );

    // Load summary data
    function updateSummaryCards() {
        fetch('/api/v2/webpages?limit=10000&offset=0')
            .then(response => response.json())
            .then(result => {
                if (result.data) {
                    calculateMetrics(result.data);
                }
            })
            .catch(error => console.error('Error loading summary:', error));
    }

    function calculateMetrics(data) {
        // Total pages
        const total = data.length;
        $('#totalPages').text(total);

        // Active pages (status = 1 or 'active' or 'Active')
        const active = data.filter(p => p.status == '1' || p.status == 'active' || p.status == 'Active').length;
        $('#activePages').text(active);

        // Inactive pages (status = 0 or 'inactive' or 'Inactive')
        const inactive = data.filter(p => p.status == '0' || p.status == 'inactive' || p.status == 'Inactive').length;
        $('#inactivePages').text(inactive);

        // Recent pages (last 30 days)
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

        const recent = data.filter(p => {
            if (!p.created_at) return false;
            const createdDate = new Date(p.created_at);
            return createdDate >= thirtyDaysAgo;
        }).length;

        $('#recentPages').text(recent);
    }

    // Initialize
    $(document).ready(function() {
        updateSummaryCards();
    });
</script>