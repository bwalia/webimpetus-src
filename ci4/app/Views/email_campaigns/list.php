<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<style>
    .campaign-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-draft {
        background: #f3f4f6;
        color: #6b7280;
    }

    .status-scheduled {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-sending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-sent {
        background: #d1fae5;
        color: #065f46;
    }

    .status-paused {
        background: #fee2e2;
        color: #991b1b;
    }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/email_campaigns/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create New Campaign
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-envelope"></i> Total Campaigns</div>
            <div class="summary-card-value" id="totalCampaigns">0</div>
            <div class="summary-card-subtitle">All email campaigns</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Sent Campaigns</div>
            <div class="summary-card-value" id="sentCampaigns">0</div>
            <div class="summary-card-subtitle">Successfully sent</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-clock"></i> Draft Campaigns</div>
            <div class="summary-card-value" id="draftCampaigns">0</div>
            <div class="summary-card-subtitle">In draft status</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-paper-plane"></i> Total Recipients</div>
            <div class="summary-card-value" id="totalRecipients">0</div>
            <div class="summary-card-subtitle">Emails sent</div>
        </div>
    </div>
</div>

<!-- Campaigns Table -->
<div class="white_card_body">
    <div class="QA_table" id="campaignsTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
$(document).ready(function() {
    let columnsTitle = ['ID', 'Name', 'Subject', 'Status', 'Recipients', 'Sent', 'Failed', 'Created', 'Actions'];
    let columnsMachineName = ['id', 'name', 'subject', 'status', 'total_recipients', 'total_sent', 'total_failed', 'created_at', 'uuid'];

    // Custom formatter for status
    const statusFormatter = (cell, row) => {
        const statusMap = {
            'draft': 'Draft',
            'scheduled': 'Scheduled',
            'sending': 'Sending',
            'sent': 'Sent',
            'paused': 'Paused'
        };
        return gridjs.html(`<span class="campaign-status status-${cell}">${statusMap[cell] || cell}</span>`);
    };

    // Custom formatter for actions
    const actionsFormatter = (cell, row) => {
        const uuid = cell;
        const status = row.cells[3].data;

        let sendButton = '';
        if (status === 'draft') {
            sendButton = `<button type='button' class='btn btn-success btn-sm' onclick='sendCampaign("${uuid}")' title='Send Campaign'><i class="fa fa-paper-plane"></i></button>`;
        }

        return gridjs.html(`
            <div class='action-button-wrapper'>
                <a href='/email_campaigns/edit/${uuid}' class='btn btn-primary btn-sm' title='Edit'><i class="fa fa-edit"></i></a>
                ${sendButton}
                <button type='button' class='btn btn-danger btn-sm' onclick='deleteCampaign("${uuid}")' title='Delete'><i class="fa fa-trash"></i></button>
            </div>
        `);
    };

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "email_campaigns",
        apiPath: "/email_campaigns/campaignsList",
        selector: "campaignsTable",
        customFormatters: {
            'status': statusFormatter,
            'uuid': actionsFormatter
        }
    });

    // Load campaign statistics
    function loadCampaignStatistics() {
        $.ajax({
            url: '/email_campaigns/campaignsList',
            method: 'GET',
            data: { limit: 1000, offset: 0 },
            success: function(response) {
                if (response && response.data) {
                    const campaigns = response.data;

                    $('#totalCampaigns').text(campaigns.length);

                    const sentCampaigns = campaigns.filter(c => c.status === 'sent');
                    $('#sentCampaigns').text(sentCampaigns.length);

                    const draftCampaigns = campaigns.filter(c => c.status === 'draft');
                    $('#draftCampaigns').text(draftCampaigns.length);

                    const totalRecipients = campaigns.reduce((sum, c) => sum + (parseInt(c.total_sent) || 0), 0);
                    $('#totalRecipients').text(totalRecipients);
                }
            }
        });
    }

    loadCampaignStatistics();

    // Make functions global so they can be called from onclick handlers
    window.deleteCampaign = function(uuid) {
        if (confirm('Are you sure you want to delete this campaign?')) {
            window.location.href = '/email_campaigns/delete/' + uuid;
        }
    };

    window.sendCampaign = function(uuid) {
        if (confirm('Are you sure you want to send this campaign? This will send emails to all customers with the selected tags.')) {
            $.ajax({
                url: '/email_campaigns/sendCampaign',
                method: 'POST',
                data: { uuid: uuid },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        window.location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Failed to send campaign');
                }
            });
        }
    };
});
</script>

<style>
    .action-button-wrapper {
        display: flex;
        gap: 5px;
    }

    .action-button-wrapper .btn {
        padding: 4px 8px;
    }
</style>
