<?php
    header('Content-Type: text/html');
    require_once (APPPATH . 'Views/common/list-title.php');
?>

<style>
    /* Services module uses global JIRA theme summary-cards */

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

    .service-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .service-link:hover {
        text-decoration: underline;
    }

    .secret-count-badge {
        background: #dbeafe;
        color: #1e40af;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-left: 6px;
    }
</style>

<div class="white_card_body">
    <!-- Action Buttons -->
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/services/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Service
        </a>
    </div>
</div>

<div class="white_card_body">
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-cogs"></i> <?= lang('App.services') ?></div>
            <div class="summary-card-value" id="totalServices">0</div>
            <div class="summary-card-subtitle"><?= lang('App.total') ?> <?= strtolower(lang('App.services')) ?></div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-key"></i> <?= lang('App.service_secrets') ?></div>
            <div class="summary-card-value" id="totalSecrets">0</div>
            <div class="summary-card-subtitle"><?= lang('App.total') ?></div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-globe"></i> <?= lang('App.domains') ?></div>
            <div class="summary-card-value" id="domainsConfigured">0</div>
            <div class="summary-card-subtitle"><?= lang('App.active') ?> <?= strtolower(lang('App.domains')) ?></div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-tags"></i> <?= lang('App.tags') ?></div>
            <div class="summary-card-value" id="taggedServices">0</div>
            <div class="summary-card-subtitle"><?= lang('App.total') ?> <?= strtolower(lang('App.tags')) ?></div>
        </div>
    </div>

    <!-- DataTable -->
    <div class="QA_table" id="servicesTable"></div>
</div>

<?php require_once (APPPATH . 'Views/services/footer.php'); ?>
<script>
    const columnRenderers = {
        name: function(data, type, row) {
            let html = '<a href="/services/edit/' + row.uuid + '" class="service-link">' + data + '</a>';
            if (row.secret_count && row.secret_count > 0) {
                html += '<span class="secret-count-badge"><i class="fa fa-key"></i> ' + row.secret_count + ' secrets</span>';
            }
            return html;
        },
        tag_names: function(data, type, row) {
            if (!data) return '<span style="color: #9ca3af;">No tags</span>';

            const tagNames = data.split(', ');
            const tagColors = (row.tag_colors || '').split(',');

            let html = '<div style="display: flex; flex-wrap: wrap; gap: 4px;">';
            tagNames.forEach((tag, index) => {
                const color = tagColors[index]?.trim() || '#667eea';
                html += '<span class="tag" style="background-color: ' + color + '\">' + tag + '</span>';
            });
            html += '</div>';
            return html;
        },
        status: function(data, type, row) {
            if (data == 1) {
                return '<span style="color: #10b981; font-weight: 600;"><i class="fa fa-check-circle"></i> Active</span>';
            } else {
                return '<span style="color: #ef4444; font-weight: 600;"><i class="fa fa-times-circle"></i> Inactive</span>';
            }
        }
    };

    let columnsTitle = ['<?= lang('App.id') ?>', '<?= lang('App.name') ?>', '<?= lang('App.category') ?>', '<?= lang('App.service') ?>', '<?= lang('App.tags') ?>', '<?= lang('App.status') ?>'];
    let columnsMachineName = ['id', 'name', 'category', 'code', 'tag_names', 'status'];

    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "services",
            apiPath: "api/v2/services",
            selector: "servicesTable",
            columnRenderers: columnRenderers
        }
    );

    // Load summary data
    function updateSummaryCards() {
        fetch('/api/v2/services?limit=10000&offset=0')
            .then(response => response.json())
            .then(result => {
                if (result.data) {
                    calculateMetrics(result.data);
                }
            })
            .catch(error => console.error('Error loading summary:', error));
    }

    function calculateMetrics(data) {
        // Total services
        const total = data.length;
        $('#totalServices').text(total);

        // Total secrets across all services
        const totalSecrets = data.reduce((sum, service) => {
            return sum + (parseInt(service.secret_count) || 0);
        }, 0);
        $('#totalSecrets').text(totalSecrets);

        // Services with domains (rough estimate based on service count)
        // In real implementation, this would come from actual domain relationships
        const domainsCount = data.filter(s => s.secret_count > 0).length;
        $('#domainsConfigured').text(domainsCount);

        // Tagged services
        const tagged = data.filter(s => s.tag_names && s.tag_names.length > 0).length;
        $('#taggedServices').text(tagged);
    }

    // Initialize
    $(document).ready(function() {
        updateSummaryCards();
    });

    $('.table-listing-items  tr  td').on('click', function (e) {
        var dataClickable = $(this).parent().attr('data-link');
        if ($(this).is(':last-child') || $(this).is(':nth-last-child(2)')) {

        } else {
            if (dataClickable && dataClickable.length > 0) {

                window.location = dataClickable;
            }
        }

    });
</script>