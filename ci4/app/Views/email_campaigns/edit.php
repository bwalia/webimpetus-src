<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="campaignForm" method="post" action="/email_campaigns/update">

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group required">
                        <label for="name">Campaign Name</label>
                        <input type="text" class="form-control required" id="name" name="name"
                               value="<?= @$campaign->name ?>" required>
                    </div>

                    <div class="form-group required">
                        <label for="subject">Email Subject</label>
                        <input type="text" class="form-control required" id="subject" name="subject"
                               value="<?= @$campaign->subject ?>" required>
                        <small class="form-text text-muted">You can use merge fields: {{company_name}}, {{first_name}}, {{last_name}}</small>
                    </div>

                    <div class="form-group required">
                        <label for="template_body">Email Template</label>
                        <div id="merge-fields-toolbar" style="margin-bottom: 10px; padding: 10px; background: #f9fafb; border-radius: 8px;">
                            <label style="font-weight: 600; margin-right: 10px;">Insert Merge Fields:</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertMergeField('{{company_name}}')">Company Name</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertMergeField('{{first_name}}')">First Name</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertMergeField('{{last_name}}')">Last Name</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertMergeField('{{email}}')">Email</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertMergeField('{{phone}}')">Phone</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertMergeField('{{address1}}')">Address</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertMergeField('{{city}}')">City</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertMergeField('{{country}}')">Country</button>
                        </div>
                        <textarea class="form-control required" id="template_body" name="template_body" rows="15" required><?= @$campaign->template_body ?></textarea>
                        <small class="form-text text-muted">
                            Create your email template using HTML. Use merge fields like {{company_name}}, {{first_name}}, etc. to personalize emails.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="campaign_tags">
                            <i class="fa fa-tags"></i> Target Customer Tags
                            <a href="/tags/manage" target="_blank" style="font-size: 0.85rem; margin-left: 8px;">
                                <i class="fa fa-cog"></i> Manage Tags
                            </a>
                        </label>
                        <select id="campaign_tags" name="campaign_tags[]" class="form-control select2" multiple="multiple"
                                data-placeholder="Select customer tags to target...">
                            <!-- Populated by JavaScript -->
                        </select>
                        <small class="form-text text-muted">
                            Select tags to target customers. Campaign will be sent to all customers with at least one of these tags.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="status">Campaign Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="draft" <?= @$campaign->status == 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="scheduled" <?= @$campaign->status == 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                            <option value="paused" <?= @$campaign->status == 'paused' ? 'selected' : '' ?>>Paused</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                        <div class="card-header" style="background: white; border-bottom: 2px solid #667eea;">
                            <h5 class="mb-0"><i class="fa fa-users"></i> Recipients Preview</h5>
                        </div>
                        <div class="card-body">
                            <div id="recipients-preview">
                                <p class="text-muted">Select tags to see recipients...</p>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3" style="background: #f0f9ff; border: 1px solid #bfdbfe;">
                        <div class="card-header" style="background: white; border-bottom: 2px solid #3b82f6;">
                            <h5 class="mb-0"><i class="fa fa-info-circle"></i> Mail Merge Help</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Available Fields:</strong></p>
                            <ul style="font-size: 0.875rem; list-style: none; padding-left: 0;">
                                <li><code>{{company_name}}</code></li>
                                <li><code>{{first_name}}</code></li>
                                <li><code>{{last_name}}</code></li>
                                <li><code>{{email}}</code></li>
                                <li><code>{{phone}}</code></li>
                                <li><code>{{address1}}</code></li>
                                <li><code>{{address2}}</code></li>
                                <li><code>{{city}}</code></li>
                                <li><code>{{postal_code}}</code></li>
                                <li><code>{{country}}</code></li>
                            </ul>
                            <p style="font-size: 0.875rem; margin-top: 15px;">
                                <strong>Example:</strong><br>
                                <em>Dear {{first_name}},<br>
                                We hope this email finds you well at {{company_name}}...</em>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="id" value="<?= @$campaign->id ?>" />
            <input type="hidden" name="uuid" value="<?= @$campaign->uuid ?>" />

            <div class="form-row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Save Campaign
                    </button>
                    <?php if (@$campaign->id && @$campaign->status == 'draft'): ?>
                    <button type="button" class="btn btn-success" onclick="sendCampaignNow()">
                        <i class="fa fa-paper-plane"></i> Send Campaign Now
                    </button>
                    <?php endif; ?>
                    <a href="/email_campaigns" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    function insertMergeField(field) {
        const textarea = document.getElementById('template_body');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;

        textarea.value = text.substring(0, start) + field + text.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + field.length;
    }

    // Load tags system
    $(document).ready(function() {
        const campaignId = '<?= @$campaign->id ?>';

        // Load all tags
        $.ajax({
            url: '/tags/tagsList',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const tags = response.data;
                    const $select = $('#campaign_tags');

                    tags.forEach(function(tag) {
                        const option = new Option(tag.name, tag.id, false, false);
                        $(option).attr('data-color', tag.color);
                        $select.append(option);
                    });

                    $select.select2({
                        placeholder: 'Select customer tags to target...',
                        templateResult: formatTag,
                        templateSelection: formatTag
                    });

                    if (campaignId) {
                        loadCurrentCampaignTags(campaignId);
                    }

                    // Load recipients preview when tags change
                    $select.on('change', function() {
                        loadRecipientsPreview();
                    });
                }
            }
        });
    });

    function loadCurrentCampaignTags(campaignId) {
        $.ajax({
            url: '/tags/getEntityTags/email_campaign/' + campaignId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const currentTagIds = response.data.map(tag => tag.id.toString());
                    $('#campaign_tags').val(currentTagIds).trigger('change');
                }
            }
        });
    }

    function formatTag(tag) {
        if (!tag.id) return tag.text;

        const color = $(tag.element).data('color') || '#667eea';
        const $tag = $(
            '<span style="display: inline-flex; align-items: center; gap: 6px;">' +
                '<span style="width: 12px; height: 12px; border-radius: 3px; background-color: ' + color + ';"></span>' +
                '<span>' + tag.text + '</span>' +
            '</span>'
        );

        return $tag;
    }

    function loadRecipientsPreview() {
        const campaignId = '<?= @$campaign->id ?>';

        if (!campaignId) {
            $('#recipients-preview').html('<p class="text-muted">Save the campaign first to see recipients.</p>');
            return;
        }

        $.ajax({
            url: '/email_campaigns/getRecipientsPreview',
            method: 'POST',
            data: { campaign_id: campaignId },
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const customers = response.data;

                    if (customers.length === 0) {
                        $('#recipients-preview').html('<p class="text-muted">No customers found with selected tags.</p>');
                        return;
                    }

                    let html = `<p><strong>${customers.length} recipient(s) will receive this email:</strong></p>`;
                    html += '<ul style="max-height: 300px; overflow-y: auto; font-size: 0.875rem;">';

                    customers.forEach(customer => {
                        const name = customer.company_name || `${customer.contact_firstname} ${customer.contact_lastname}`;
                        html += `<li>${name} (${customer.email})</li>`;
                    });

                    html += '</ul>';
                    $('#recipients-preview').html(html);
                }
            }
        });
    }

    function sendCampaignNow() {
        const uuid = '<?= @$campaign->uuid ?>';

        if (confirm('Are you sure you want to send this campaign now? This will send emails to all customers with the selected tags.')) {
            $.ajax({
                url: '/email_campaigns/sendCampaign',
                method: 'POST',
                data: { uuid: uuid },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        window.location.href = '/email_campaigns';
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Failed to send campaign');
                }
            });
        }
    }
</script>

<style>
    #merge-fields-toolbar .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }

    code {
        background: #e5e7eb;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.85em;
    }
</style>
