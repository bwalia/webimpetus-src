<?php require_once (APPPATH.'Views/common/list-title.php'); ?>

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
        <a href="/gallery/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Media File
        </a>
    </div>
</div>

<div class="white_card_body">
    <div class="QA_table gallaryTable" id="gallaryTable"></div>
</div>
<?php require_once (APPPATH.'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Code', 'Status', 'Created at'];
    let columnsMachineName = ['id', 'code', 'status', 'created'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "gallary",
            apiPath: "gallary/enquiriesList",
            selector: "gallaryTable"
        }
    );
</script>