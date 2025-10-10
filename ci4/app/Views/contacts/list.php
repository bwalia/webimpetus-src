<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<style>
    /* Contacts module uses global JIRA theme summary-cards */

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

    .quick-action-btn.primary {
        background-color: #667eea;
        color: white;
    }

    .quick-action-btn.success {
        background-color: #10b981;
        color: white;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .status-active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .contact-link {
        color: #667eea;
        font-weight: 600;
        text-decoration: none;
    }

    .contact-link:hover {
        text-decoration: underline;
    }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/contacts/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Contact
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-address-book"></i> Total Contacts</div>
            <div class="summary-card-value" id="totalContacts">0</div>
            <div class="summary-card-subtitle">in database</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-user-check"></i> Web Access</div>
            <div class="summary-card-value" id="webAccessCount">0</div>
            <div class="summary-card-subtitle">portal access enabled</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-envelope"></i> Newsletter</div>
            <div class="summary-card-value" id="newsletterCount">0</div>
            <div class="summary-card-subtitle">subscribed</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-calendar-plus"></i> New This Month</div>
            <div class="summary-card-value" id="newThisMonth">0</div>
            <div class="summary-card-subtitle">contacts added</div>
        </div>
    </div>
</div>

<div class="white_card_body">
    <div class="QA_table" id="contactsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Full Name', 'Email', 'Mobile', 'Direct Phone', 'Web Access', 'Newsletter'];
    let columnsMachineName = ['id', 'full_name', 'email', 'mobile', 'direct_phone', 'allow_web_access', 'news_letter_status'];

    // Custom column renderers
    const columnRenderers = {
        full_name: function(data, type, row) {
            const fullName = (row.first_name || '') + ' ' + (row.surname || '');
            return '<a href="/contacts/edit/' + row.uuid + '" class="contact-link">' + fullName.trim() + '</a>';
        },
        email: function(data, type, row) {
            if (data) {
                return '<a href="mailto:' + data + '" style="color: #667eea;">' + data + '</a>';
            }
            return '-';
        },
        mobile: function(data, type, row) {
            if (data) {
                return '<a href="tel:' + data + '" style="color: #667eea;">' + data + '</a>';
            }
            return '-';
        },
        direct_phone: function(data, type, row) {
            if (data) {
                return '<a href="tel:' + data + '" style="color: #667eea;">' + data + '</a>';
            }
            return '-';
        },
        allow_web_access: function(data, type, row) {
            if (data == 1 || data === true) {
                return '<span class="status-badge status-active"><i class="fa fa-check"></i> Yes</span>';
            } else {
                return '<span class="status-badge status-inactive"><i class="fa fa-times"></i> No</span>';
            }
        },
        news_letter_status: function(data, type, row) {
            if (data && data.toLowerCase() === 'subscribed') {
                return '<span class="status-badge status-active"><i class="fa fa-envelope"></i> Yes</span>';
            } else {
                return '<span style="color: #9ca3af;">No</span>';
            }
        }
    };

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "contacts",
        apiPath: "contacts/contactsList",
        selector: "contactsTable",
        columnRenderers: columnRenderers
    });

    // Update summary cards
    function updateContactSummaryCards() {
        const businessUuid = '<?php echo session('uuid_business'); ?>';

        fetch('/contacts/contactsList?limit=10000&offset=0&uuid_business_id=' + businessUuid)
            .then(response => response.json())
            .then(result => {
                if (result && result.data) {
                    calculateContactMetrics(result.data);
                }
            })
            .catch(error => {
                console.error('Error fetching contact summary:', error);
            });
    }

    function calculateContactMetrics(contacts) {
        const today = new Date();
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);

        let totalCount = contacts.length;
        let webAccessCount = 0;
        let newsletterCount = 0;
        let newThisMonth = 0;

        contacts.forEach(function(contact) {
            // Count web access
            if (contact.allow_web_access == 1 || contact.allow_web_access === true) {
                webAccessCount++;
            }

            // Count newsletter subscribers
            if (contact.news_letter_status && contact.news_letter_status.toLowerCase() === 'subscribed') {
                newsletterCount++;
            }

            // Count new this month
            if (contact.created_at) {
                const createdDate = new Date(contact.created_at);
                if (createdDate >= monthStart) {
                    newThisMonth++;
                }
            }
        });

        // Update cards
        $('#totalContacts').text(totalCount);
        $('#webAccessCount').text(webAccessCount);
        $('#newsletterCount').text(newsletterCount);
        $('#newThisMonth').text(newThisMonth);

        console.log('Contact metrics updated:', {
            total: totalCount,
            webAccess: webAccessCount,
            newsletter: newsletterCount,
            newThisMonth: newThisMonth
        });
    }

    $(document).ready(function() {
        setTimeout(function() {
            updateContactSummaryCards();
        }, 1000);
    });
</script>
