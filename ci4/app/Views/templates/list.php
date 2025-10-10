<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/templates/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Template
        </a>
    </div>
</div>

<style>
    /* Summary Cards */

    /* Module badges */
    .module-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .module-timeslips { background: #dbeafe; color: #1e40af; }
    .module-sales_invoices { background: #dcfce7; color: #166534; }
    .module-purchase_invoices { background: #fef3c7; color: #92400e; }
    .module-purchase_orders { background: #f3e8ff; color: #6b21a8; }
    .module-work_orders { background: #fee2e2; color: #991b1b; }
    .module-services { background: #e0f2fe; color: #075985; }

    /* Default badge */
    .default-badge {
        background: #fef3c7;
        color: #92400e;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-left: 6px;
    }

    /* Tag display */
    .tag {
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        color: white;
        display: inline-block;
        margin-right: 4px;
        margin-bottom: 4px;
    }

    .template-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .template-link:hover {
        text-decoration: underline;
    }
</style>

<div class="white_card_body">
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-file-alt"></i> Total Templates</div>
            <div class="summary-card-value" id="totalTemplates">0</div>
            <div class="summary-card-subtitle">All email templates</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-star"></i> Default Templates</div>
            <div class="summary-card-value" id="defaultTemplates">0</div>
            <div class="summary-card-subtitle">Active defaults</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-layer-group"></i> Modules Covered</div>
            <div class="summary-card-value" id="modulesCovered">0</div>
            <div class="summary-card-subtitle">Unique modules</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-clock"></i> Recent Templates</div>
            <div class="summary-card-value" id="recentTemplates">0</div>
            <div class="summary-card-subtitle">Last 30 days</div>
        </div>
    </div>

    <!-- DataTable -->
    <div class="QA_table" id="templatesTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    const columnRenderers = {
        code: function(data, type, row) {
            let html = '<a href="/templates/edit/' + row.uuid + '" class="template-link">' + data + '</a>';
            if (row.is_default == 1) {
                html += '<span class="default-badge"><i class="fa fa-star"></i> Default</span>';
            }
            return html;
        },
        module_name: function(data, type, row) {
            if (!data) return '<span style="color: #9ca3af;">N/A</span>';

            const moduleName = data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            return '<span class="module-badge module-' + data + '">' + moduleName + '</span>';
        },
        tag_names: function(data, type, row) {
            if (!data) return '<span style="color: #9ca3af;">No tags</span>';

            const tagNames = data.split(', ');
            const tagColors = (row.tag_colors || '').split(',');

            let html = '<div style="display: flex; flex-wrap: wrap; gap: 4px;">';
            tagNames.forEach((tag, index) => {
                const color = tagColors[index]?.trim() || '#667eea';
                html += '<span class="tag" style="background-color: ' + color + '">' + tag + '</span>';
            });
            html += '</div>';
            return html;
        }
    };

    let columnsTitle = ['ID', 'Code', 'Subject', 'Module', 'Tags'];
    let columnsMachineName = ['id', 'code', 'subject', 'module_name', 'tag_names'];

    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "templates",
            apiPath: "templates/templateList",
            selector: "templatesTable",
            columnRenderers: columnRenderers
        }
    );

    // Load summary data
    function updateSummaryCards() {
        fetch('/templates/templateList?limit=10000&offset=0')
            .then(response => response.json())
            .then(result => {
                if (result.data) {
                    calculateMetrics(result.data);
                }
            })
            .catch(error => console.error('Error loading summary:', error));
    }

    function calculateMetrics(data) {
        // Total templates
        const total = data.length;
        $('#totalTemplates').text(total);

        // Default templates
        const defaults = data.filter(t => t.is_default == 1).length;
        $('#defaultTemplates').text(defaults);

        // Modules covered
        const modules = new Set(data.filter(t => t.module_name).map(t => t.module_name));
        $('#modulesCovered').text(modules.size);

        // Recent templates (last 30 days)
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

        const recent = data.filter(t => {
            if (!t.created_at) return false;
            const createdDate = new Date(t.created_at);
            return createdDate >= thirtyDaysAgo;
        }).length;

        $('#recentTemplates').text(recent);
    }

    // Initialize
    $(document).ready(function() {
        updateSummaryCards();
    });
</script>
