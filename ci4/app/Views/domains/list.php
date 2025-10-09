<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<style>
    /* JIRA-style table enhancements */
    .white_card_body {
        padding: 20px;
    }

    .QA_table table,
    .dataTable {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
    }

    .QA_table thead th,
    .dataTable thead th {
        background: var(--gray-50, #f9fafb) !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.5px !important;
        color: var(--gray-600, #4b5563) !important;
        padding: 12px 16px !important;
        border-bottom: 2px solid var(--gray-200, #e5e7eb) !important;
    }

    .QA_table tbody td,
    .dataTable tbody td {
        padding: 12px 16px !important;
        font-size: 0.875rem !important;
        color: var(--gray-700, #374151) !important;
        border-bottom: 1px solid var(--gray-100, #f3f4f6) !important;
    }

    .QA_table tbody tr:hover,
    .dataTable tbody tr:hover {
        background: var(--gray-50, #f9fafb) !important;
        transition: background 0.2s ease;
    }

    .QA_table tbody tr:last-child td,
    .dataTable tbody tr:last-child td {
        border-bottom: none !important;
    }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/domains/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-globe"></i> Total Domains</div>
            <div class="summary-card-value" id="totalDomains">0</div>
            <div class="summary-card-subtitle">All configured domains</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Active Domains</div>
            <div class="summary-card-value" id="activeDomains">0</div>
            <div class="summary-card-subtitle">Currently active</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-server"></i> Unique Services</div>
            <div class="summary-card-value" id="uniqueServices">0</div>
            <div class="summary-card-subtitle">Different services</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-users"></i> Unique Customers</div>
            <div class="summary-card-value" id="uniqueCustomers">0</div>
            <div class="summary-card-subtitle">Different customers</div>
        </div>
    </div>
</div>

<!-- Domains Table -->
<div class="white_card_body">
    <div class="QA_table" id="domainsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['ID', 'Domain Name', 'Service', 'Customer', 'Path', 'Port'];
    let columnsMachineName = ['id', 'name', 'sname', 'customer_uuid', 'domain_path', 'domain_service_port'];

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "domains",
        apiPath: "domains/domainList",
        selector: "domainsTable"
    });

    // Load domain statistics
    function loadDomainStatistics() {
        $.ajax({
            url: '/domains/domainList',
            method: 'GET',
            success: function(response) {
                if (response && response.data) {
                    const domains = response.data;

                    // Total domains
                    $('#totalDomains').text(domains.length);

                    // Active domains (you may need to adjust based on your status field)
                    const activeDomains = domains.filter(domain => {
                        return domain.status == 1 || domain.status == '1' ||
                               domain.active == 1 || domain.active == '1';
                    });
                    $('#activeDomains').text(activeDomains.length);

                    // Unique services
                    const uniqueServices = new Set(domains
                        .filter(d => d.sname || d.service_name)
                        .map(d => d.sname || d.service_name)
                    );
                    $('#uniqueServices').text(uniqueServices.size);

                    // Unique customers
                    const uniqueCustomers = new Set(domains
                        .filter(d => d.customer_uuid || d.customer_id)
                        .map(d => d.customer_uuid || d.customer_id)
                    );
                    $('#uniqueCustomers').text(uniqueCustomers.size);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading domain statistics:', error);
                // Set default values on error
                $('#totalDomains').text('0');
                $('#activeDomains').text('0');
                $('#uniqueServices').text('0');
                $('#uniqueCustomers').text('0');
            }
        });
    }

    // Load statistics on page load
    loadDomainStatistics();
</script>

<style>
    .QA_table table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 300px;
    }

    .QA_table table td:hover {
        overflow: visible;
        white-space: normal;
        word-wrap: break-word;
    }
</style>