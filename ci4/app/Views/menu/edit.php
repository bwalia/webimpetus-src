<?php require_once(APPPATH . 'Views/common/edit-title.php');

$str = file_get_contents(APPPATH . 'languages.json');
$json = json_decode($str, true);
//print_r($json); die;
?>
<div class="white_card_body">
    <div class="card-body">

        <form id="addcustomer" method="post" action=<?php echo "/" . $tableName . "/update"; ?>
            enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group required col-md-6">
                    <label for="inputEmail4">Name</label>
                    <input type="input" class="form-control required" id="name" name="name" placeholder=""
                        value="<?= @$data->name ?>">
                </div>
                <div class="form-group required col-md-6">
                    <label for="inputEmail4">Link</label>
                    <input type="input" class="form-control required" id="link" name="link" placeholder=""
                        value="<?= @$data->link ?>">
                </div>

            </div>
            <div class="form-row">
                <div class="form-group required col-md-6">
                    <label for="inputEmail4">Icon</label>
                    <input type="input" class="form-control required" id="icon" name="icon" placeholder=""
                        value="<?= @$data->icon ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputEmail4">Categories</label>
                    <select id="categories" name="categories[]" multiple class="form-control select2">
                        <?php
                        if (isset($categories) && (!empty($categories))) {
                            foreach ($categories as $row): ?>
                                <option value="<?= $row['id']; ?>" <?php if (!empty($selected_cat))
                                      echo
                                          in_array($row['id'], $selected_cat) ? 'selected="selected"' : '' ?>>
                                    <?= $row['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group required col-md-6">
                    <label for="inputEmail4">Language Code</label>
                    <select name="language_code" class="required form-control">
                        <?php foreach ($json as $key => $row): ?>
                            <option value="<?= $key; ?>" <?= @$data->language_code == $key ? 'selected="selected"' : ''; ?>>
                                <?= $row; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="menu_tags">
                        <i class="fa fa-tags"></i> Tags
                        <a href="/tags/manage" target="_blank" style="font-size: 0.85rem; margin-left: 8px;">
                            <i class="fa fa-cog"></i> Manage Tags
                        </a>
                    </label>
                    <select id="menu_tags" name="menu_tags[]" class="form-control select2" multiple="multiple"
                            data-placeholder="Select tags for this menu...">
                        <!-- Populated by JavaScript -->
                    </select>
                    <small class="form-text text-muted">
                        Select multiple tags to categorize this menu item.
                    </small>
                </div>
            </div>

            <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$data->id ?>" />
            <input type="hidden" class="form-control" name="uuid" placeholder="" value="<?= @$data->uuid ?>" />
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->

<script>
    $(document).on("click", ".form-check-input", function () {
        if ($(this).prop("checked") == false) {
            $(this).val(0);
        } else {
            $(this).val(1);
        }
    });

    // Load tags system
    $(document).ready(function() {
        const menuId = '<?= @$data->id ?>';

        // Load all tags
        $.ajax({
            url: '/tags/tagsList',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const tags = response.data;
                    const $select = $('#menu_tags');

                    // Populate select options
                    tags.forEach(function(tag) {
                        const option = new Option(tag.name, tag.id, false, false);
                        $(option).attr('data-color', tag.color);
                        $select.append(option);
                    });

                    // Initialize select2 with custom template
                    $select.select2({
                        placeholder: 'Select tags for this menu...',
                        templateResult: formatMenuTag,
                        templateSelection: formatMenuTag
                    });

                    // Load current menu tags if editing
                    if (menuId) {
                        loadCurrentMenuTags(menuId);
                    }
                }
            }
        });
    });

    function loadCurrentMenuTags(menuId) {
        $.ajax({
            url: '/tags/getEntityTags/menu/' + menuId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.data) {
                    const currentTagIds = response.data.map(function(tag) {
                        return tag.id.toString();
                    });
                    $('#menu_tags').val(currentTagIds).trigger('change');
                }
            }
        });
    }

    function formatMenuTag(tag) {
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

    // Save tags when form is submitted
    $('#addcustomer').on('submit', function(e) {
        const menuId = '<?= @$data->id ?>';

        if (menuId) {
            e.preventDefault();

            // Save tags first
            const selectedTags = $('#menu_tags').val() || [];

            $.ajax({
                url: '/tags/attach',
                method: 'POST',
                data: {
                    entity_type: 'menu',
                    entity_id: menuId,
                    tag_ids: selectedTags
                },
                dataType: 'json',
                success: function(response) {
                    // Then submit the form normally
                    $('#addcustomer').off('submit').submit();
                },
                error: function() {
                    // Still submit the form even if tag saving fails
                    $('#addcustomer').off('submit').submit();
                }
            });
        }
    });
</script>