<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<?php //$customers = $additional_data["customers"]; 
?>
<div class="white_card_body">
    <div class="row">
        <div class="col-lg-8">
            <div class="card-body">
                <form id="addcustomer" method="post" action=<?php echo "/" . $tableName . "/update"; ?> enctype="multipart/form-data">
                    <div class="form-row">
                        <input type="hidden" class="form-control" name="uuid" placeholder="" value="<?= @$template->uuid ?>" />
                        <div class="form-group required col-md-6">
                            <label for="inputEmail4">Code</label>
                            <input type="input" class="form-control required" id="code" name="code" placeholder="" value="<?= @$template->code ?>">
                        </div>
                        <div class="form-group required col-md-6">
                            <label for="inputEmail4">Subject</label>
                            <input type="input" class="form-control required" id="subject" name="subject" placeholder="" value="<?= @$template->subject ?>">
                        </div>
                        <div class="form-group required col-md-12">
                            <label for="inputEmail4">Template Content</label>
                            <textarea class="form-control required template_content" id="template_content" name="template_content" placeholder="" rows="40" column="20" value=""><?= @$template->template_content ?></textarea>
                        </div>
                        <div class="form-group required col-md-12">
                            <label for="inputEmail4">Comment</label>
                            <textarea row='40' col='40' class="form-control required" id="comment" name="comment" placeholder="" value=""><?= @$template->comment ?></textarea>
                        </div>
                        <div class="form-group required col-md-6">
                            <label for="inputModule">Choose Module</label>
                            <select name="module_name" class="form-control required">
                                <option value="">--Choose Module--</option>
                                <option value="timeslips" <?= @$template->module_name == 'timeslips' ? 'selected' : ''; ?>>Timeslips</option>
                                <option value="sales_invoices" <?= @$template->module_name == 'sales_invoices' ? 'selected' : ''; ?>>Sales Invoices</option>
                                <option value="purchase_invoices" <?= @$template->module_name == 'purchase_invoices' ? 'selected' : ''; ?>>Purchase Invoices</option>
                                <option value="purchase_orders" <?= @$template->module_name == 'purchase_orders' ? 'selected' : ''; ?>>Purchase Orders</option>
                                <option value="work_orders" <?= @$template->module_name == 'work_orders' ? 'selected' : ''; ?>>Work Orders</option>
                                <option value="services" <?= @$template->module_name == 'services' ? 'selected' : ''; ?>>Services</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputModule">Default Template</label>
                            <div class="help-block">
                                <input type="checkbox" name="is_default" <?= !empty(@$template->is_default) ? "checked" : ''; ?>>
                            </div>
                        </div>
                    </div>

                    <!-- Tags Section -->
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="template_tags">
                                <i class="fa fa-tags"></i> Tags
                                <a href="/tags/manage" target="_blank" style="font-size: 0.85rem; margin-left: 8px;">
                                    <i class="fa fa-cog"></i> Manage Tags
                                </a>
                            </label>
                            <select id="template_tags" name="template_tags[]" class="form-control select2" multiple="multiple"
                                    data-placeholder="Select tags for this template...">
                                <!-- Populated by JavaScript -->
                            </select>
                            <small class="form-text text-muted">
                                Select multiple tags to categorize this template. You can create new tags from the Manage Tags page.
                            </small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
        <div class="col-lg-4 d-flex justify-content-center bg-light pt-3">
            <div class="card-body">
                <h5>Drag and drop Blocks in "Content"</h5>
                <div class="input-group mb-3">
                    <input type="text" name="search-token" class="form-control" placeholder="Search here.." aria-label="Search here.." aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary search-token-button" type="button"><i class="fa fa-search"></i></button>
                    </div>
                </div>
                <ul class="list-group token-list overflow-auto tokenlist">
                    <?php foreach ($blocks_lists as $row) : ?>
                        <li class="list-group-item list-group-item-action dragtoken" aria-current="true">
                            <a target="_blank" href="<?= site_url('blocks/edit/' . $row['id']) ?>"><?= $row['code']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->
<script>
    $(document).on("click", ".form-check-input", function() {
        if ($(this).prop("checked") == false) {
            $(this).val(0);
        } else {
            $(this).val(1);
        }
    });
    $("textarea.template_content").on("input", function() {
        let text = $(this).val();
        var js_start_tag = "<script>";
        if (text.indexOf(js_start_tag) != -1) {
            alert("You are not allowed to enter JavaScript code here!");
            $("textarea.template_content").val(text.replace(js_start_tag, ""));
            return false;
        }
    });

    // Load tags functionality
    $(document).ready(function() {
        loadTemplateTags();
    });

    function loadTemplateTags() {
        const templateId = '<?= @$template->id ?>';

        // Load all tags
        $.ajax({
            url: '/tags/tagsList',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const tags = response.data;
                    const $select = $('#template_tags');

                    // Populate select options
                    tags.forEach(function(tag) {
                        const option = new Option(tag.name, tag.id, false, false);
                        $(option).attr('data-color', tag.color);
                        $select.append(option);
                    });

                    // Initialize select2 with custom template
                    $select.select2({
                        placeholder: 'Select tags for this template...',
                        allowClear: true,
                        templateResult: formatTemplateTag,
                        templateSelection: formatTemplateTagSelection
                    });

                    // Load currently assigned tags if editing
                    if (templateId) {
                        loadCurrentTemplateTags(templateId);
                    }
                }
            }
        });
    }

    function loadCurrentTemplateTags(templateId) {
        $.ajax({
            url: '/tags/getEntityTags/template/' + templateId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const currentTagIds = response.data.map(function(tag) {
                        return tag.id.toString();
                    });
                    $('#template_tags').val(currentTagIds).trigger('change');
                }
            }
        });
    }

    function formatTemplateTag(tag) {
        if (!tag.id) return tag.text;

        const color = $(tag.element).data('color') || '#667eea';
        const $tag = $(
            '<span style="display: flex; align-items: center; gap: 8px;">' +
                '<span style="width: 12px; height: 12px; border-radius: 50%; background-color: ' + color + ';"></span>' +
                '<span>' + tag.text + '</span>' +
            '</span>'
        );
        return $tag;
    }

    function formatTemplateTagSelection(tag) {
        if (!tag.id) return tag.text;
        return tag.text;
    }

    // Save tags when form is submitted
    $('#addcustomer').on('submit', function(e) {
        const templateId = '<?= @$template->id ?>';

        if (templateId) {
            e.preventDefault();

            // Save tags first
            const selectedTags = $('#template_tags').val() || [];

            $.ajax({
                url: '/tags/attach',
                method: 'POST',
                data: {
                    entity_type: 'template',
                    entity_id: templateId,
                    tag_ids: selectedTags
                },
                dataType: 'json',
                success: function(response) {
                    // Now submit the main form
                    $('#addcustomer').off('submit').submit();
                },
                error: function() {
                    // Submit anyway if tag saving fails
                    $('#addcustomer').off('submit').submit();
                }
            });
        }
    });
</script>