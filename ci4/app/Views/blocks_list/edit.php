<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<div class="white_card_body">
    <div class="card-body">

        <form id="adddomain" method="post" action="/blocks/update" enctype="multipart/form-data">
            <div class="form-row">

                <div class="form-group col-md-12">
                    <label for="inputEmail4">Code</label>
                    <input type="text" class="form-control" id="title" name="code" placeholder="" value="<?= @$blocks->code ?>">
                    <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$blocks->id ?>" />
                    <input type="hidden" class="form-control" name="uuid" placeholder="" value="<?= @$blocks->uuid ?>" />
                </div>
                <div class="form-group col-md-12">
                    <label for="inputEmail4">Title</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="" value="<?= @$blocks->title ?>">
                </div>

                <div class="form-group col-md-12">
                    <label for="inputPassword4">Text</label>
                    <textarea class="form-control block-text" name="text" style="width:100%!important;height:700px"><?= @$blocks->text ?></textarea>
                </div>

                <div class="form-group col-md-12">
                    <label for="block_tags">
                        <i class="fa fa-tags"></i> Tags
                        <a href="/tags/manage" target="_blank" style="font-size: 0.85rem; margin-left: 8px;">
                            <i class="fa fa-cog"></i> Manage Tags
                        </a>
                    </label>
                    <select id="block_tags" name="block_tags[]" class="form-control select2" multiple="multiple"
                            data-placeholder="Select tags for this block...">
                        <!-- Populated by JavaScript -->
                    </select>
                    <small class="form-text text-muted">
                        Select multiple tags to categorize this block.
                    </small>
                </div>

                <div class="form-group col-md-12">
                    <label for="inputStatus">Status</label>
                </div>
                <div class="form-group col-md-12">
                    <label for="inputStatus"><input type="radio" value="1" class="form-control" id="status" name="status" <?= @$blocks->status == 1 ? 'checked' : '' ?>> Yes</label>
                    <label for="inputStatus"><input type="radio" <?= @$blocks->status == 0 ? 'checked' : '' ?> value="0" class="form-control" id="status" name="status"> No</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<!-- main content part end -->

<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    var role = "<?= $role ?>";

    // Tags functionality
    const blockId = "<?= @$blocks->uuid ?>";

    function loadBlockTags() {
        $.ajax({
            url: '/tags/list',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.data && Array.isArray(response.data)) {
                    populateBlockTagsSelect(response.data);
                }
            }
        });
    }

    function populateBlockTagsSelect(tags) {
        const $select = $('#block_tags');

        // Populate select options
        tags.forEach(function(tag) {
            const option = new Option(tag.name, tag.id, false, false);
            $(option).attr('data-color', tag.color);
            $select.append(option);
        });

        // Initialize select2 with custom template
        $select.select2({
            placeholder: 'Select tags for this block...',
            allowClear: true,
            templateResult: formatBlockTag,
            templateSelection: formatBlockTagSelection
        });

        // Load currently assigned tags if editing
        if (blockId) {
            loadCurrentBlockTags(blockId);
        }
    }

    function loadCurrentBlockTags(blockId) {
        $.ajax({
            url: '/tags/getEntityTags',
            method: 'GET',
            data: {
                entity_type: 'block',
                entity_id: blockId
            },
            dataType: 'json',
            success: function(response) {
                if (response.data && Array.isArray(response.data)) {
                    const currentTagIds = response.data.map(tag => tag.id);
                    $('#block_tags').val(currentTagIds).trigger('change');
                }
            }
        });
    }

    function formatBlockTag(tag) {
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

    function formatBlockTagSelection(tag) {
        if (!tag.id) return tag.text;

        const color = $(tag.element).data('color') || '#667eea';
        return $('<span style="display: flex; align-items: center; gap: 6px;">' +
            '<span style="width: 10px; height: 10px; border-radius: 50%; background-color: ' + color + ';"></span>' +
            '<span>' + tag.text + '</span>' +
            '</span>');
    }

    // Load tags on page load
    $(document).ready(function() {
        loadBlockTags();
    });

    // Save tags before form submission
    $('#adddomain').on('submit', function(e) {
        if (blockId) {
            e.preventDefault();
            const selectedTags = $('#block_tags').val() || [];

            $.ajax({
                url: '/tags/attach',
                method: 'POST',
                data: {
                    entity_type: 'block',
                    entity_id: blockId,
                    tag_ids: selectedTags
                },
                dataType: 'json',
                success: function(response) {
                    // Now submit the main form
                    $('#adddomain').off('submit').submit();
                },
                error: function() {
                    // Submit anyway if tag saving fails
                    $('#adddomain').off('submit').submit();
                }
            });
        }
    });
</script>
<script src="/assets/js/block_edit.js"></script>