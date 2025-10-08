<?php require_once (APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="addkb" method="post" action="/knowledge_base/update" enctype="multipart/form-data">
            <input type="hidden" class="form-control" name="id" value="<?= @$article->id ?>" />
            <input type="hidden" class="form-control" name="uuid" value="<?= @$article->uuid ?>" />

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="article_number">Article Number</label>
                    <input type="text" class="form-control" id="article_number" name="article_number"
                           value="<?= @$article->article_number ?>" readonly>
                </div>

                <div class="form-group required col-md-6">
                    <label for="title">Title</label>
                    <input type="text" class="form-control required" id="title" name="title"
                           value="<?= @$article->title ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="content">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="10"><?= @$article->content ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="category">Category</label>
                    <input type="text" class="form-control" id="category" name="category"
                           value="<?= @$article->category ?>">
                </div>

                <div class="form-group col-md-4">
                    <label for="status">Status</label>
                    <select class="form-control select2" id="status" name="status">
                        <option value="draft" <?= @$article->status == 'draft' || empty($article) ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= @$article->status == 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="archived" <?= @$article->status == 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label for="visibility">Visibility</label>
                    <select class="form-control select2" id="visibility" name="visibility">
                        <option value="public" <?= @$article->visibility == 'public' ? 'selected' : '' ?>>Public</option>
                        <option value="internal" <?= @$article->visibility == 'internal' || empty($article) ? 'selected' : '' ?>>Internal</option>
                        <option value="private" <?= @$article->visibility == 'private' ? 'selected' : '' ?>>Private</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="keywords">Keywords</label>
                    <input type="text" class="form-control" id="keywords" name="keywords"
                           value="<?= @$article->keywords ?>" placeholder="Comma separated keywords">
                </div>

                <div class="form-group col-md-6">
                    <label for="tags">Tags</label>
                    <input type="text" class="form-control" id="tags" name="tags"
                           value="<?= @$article->tags ?>" placeholder="Comma separated tags">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="author_id">Author</label>
                    <select class="form-control select2" id="author_id" name="author_id">
                        <option value="">--Select Author--</option>
                        <?php if (isset($users)) {
                            foreach ($users as $user): ?>
                                <option value="<?= $user['id']; ?>" <?= $user['id'] == @$article->author_id ? 'selected' : '' ?>>
                                    <?= $user['name']; ?>
                                </option>
                            <?php endforeach;
                        } ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="published_date">Published Date</label>
                    <input type="text" class="form-control datetimepicker" id="published_date" name="published_date"
                           value="<?= @$article->published_date ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="helpful_count">Helpful Count</label>
                    <input type="number" class="form-control" id="helpful_count" name="helpful_count"
                           value="<?= @$article->helpful_count ?? 0 ?>" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="view_count">View Count</label>
                    <input type="number" class="form-control" id="view_count" name="view_count"
                           value="<?= @$article->view_count ?? 0 ?>" readonly>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="/knowledge_base" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>
    // Initialize CKEditor for content field
    if (CKEDITOR.instances['content']) {
        CKEDITOR.instances['content'].destroy();
    }
    CKEDITOR.replace('content', {
        filebrowserBrowseUrl: '/assets/ckfinder/ckfinder.html',
        filebrowserUploadUrl: '/assets/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserWindowWidth: '900',
        filebrowserWindowHeight: '700'
    });
</script>
