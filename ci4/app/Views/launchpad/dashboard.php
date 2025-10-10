<?php require_once (APPPATH . 'Views/common/header.php'); ?>

<style>
.launchpad-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    padding: 20px;
}

.bookmark-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    position: relative;
}

.bookmark-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.bookmark-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 12px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.bookmark-title {
    font-weight: 600;
    color: #172b4d;
    margin-bottom: 8px;
    font-size: 14px;
}

.bookmark-url {
    font-size: 11px;
    color: #6b7280;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.bookmark-stats {
    display: flex;
    justify-content: space-around;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
    font-size: 11px;
    color: #6b7280;
}

.favorite-star {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #fbbf24;
    font-size: 16px;
}

.add-bookmark-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #667eea;
    color: white;
    border: none;
    font-size: 24px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    cursor: pointer;
    z-index: 1000;
}

.add-bookmark-btn:hover {
    background: #5a67d8;
}
</style>

<div class="main_content_iner">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="page_title_box d-flex align-items-center justify-content-between">
                    <div class="page_title_left">
                        <h3 class="f_s_30 f_w_700 text_white">Launchpad</h3>
                    </div>
                    <div>
                        <a href="/launchpad/manage" class="btn btn-light mr-2">
                            <i class="fa fa-cog"></i> Manage Bookmarks
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="white_card">
            <div class="white_card_body">
                <div class="launchpad-grid" id="bookmarksGrid">
                    <div class="text-center" style="grid-column: 1/-1;">
                        <i class="fa fa-spinner fa-spin fa-3x text-muted"></i>
                        <p class="mt-3">Loading bookmarks...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<button class="add-bookmark-btn" onclick="showAddBookmarkModal()">
    <i class="fa fa-plus"></i>
</button>

<!-- Add/Edit Bookmark Modal -->
<div class="modal fade" id="bookmarkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Bookmark</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="bookmarkForm">
                    <input type="hidden" id="bookmark_uuid" name="uuid">
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>URL *</label>
                        <input type="url" class="form-control" name="url" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category</label>
                                <input type="text" class="form-control" name="category" placeholder="e.g., Work, Tools">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Color</label>
                                <input type="color" class="form-control" name="color" value="#667eea">
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_favorite" id="is_favorite">
                        <label class="form-check-label" for="is_favorite">Mark as Favorite</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_public" id="is_public">
                        <label class="form-check-label" for="is_public">Share with all users</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBookmark()">Save Bookmark</button>
            </div>
        </div>
    </div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>
let bookmarks = [];

function loadBookmarks() {
    $.get('/launchpad/getBookmarks', function(response) {
        if (response.status) {
            bookmarks = response.data;
            renderBookmarks();
        }
    });
}

function renderBookmarks() {
    const grid = $('#bookmarksGrid');

    if (bookmarks.length === 0) {
        grid.html('<div style="grid-column: 1/-1; text-align: center; padding: 40px;"><p class="text-muted">No bookmarks yet. Click the + button to add your first bookmark!</p></div>');
        return;
    }

    let html = '';
    bookmarks.forEach(bookmark => {
        const domain = new URL(bookmark.url).hostname;
        html += `
            <div class="bookmark-card" onclick="openBookmark('${bookmark.uuid}', '${bookmark.url}')" style="border-top: 4px solid ${bookmark.color}">
                ${bookmark.is_favorite == 1 ? '<i class="fa fa-star favorite-star"></i>' : ''}
                <div class="bookmark-icon" style="background-color: ${bookmark.color}">
                    <i class="fa fa-link"></i>
                </div>
                <div class="bookmark-title">${bookmark.title}</div>
                <div class="bookmark-url">${domain}</div>
                <div class="bookmark-stats">
                    <span><i class="fa fa-mouse-pointer"></i> ${bookmark.click_count || 0}</span>
                    ${bookmark.owner_name && bookmark.owner_name != '<?= session('name') ?>' ? '<span><i class="fa fa-share"></i> Shared</span>' : ''}
                </div>
            </div>
        `;
    });

    grid.html(html);
}

function openBookmark(uuid, url) {
    // Record click
    $.post('/launchpad/click/' + uuid);

    // Open in new tab
    window.open(url, '_blank');
}

function showAddBookmarkModal() {
    $('#bookmarkForm')[0].reset();
    $('#bookmark_uuid').val('');
    $('#bookmarkModal').modal('show');
}

function saveBookmark() {
    const formData = $('#bookmarkForm').serializeArray();
    const data = {};
    formData.forEach(item => {
        if (item.name === 'is_favorite' || item.name === 'is_public') {
            data[item.name] = $('#' + item.name).is(':checked') ? 1 : 0;
        } else {
            data[item.name] = item.value;
        }
    });

    $.ajax({
        url: '/launchpad/save',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {
            if (response.status) {
                $('#bookmarkModal').modal('hide');
                loadBookmarks();
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        }
    });
}

$(document).ready(function() {
    loadBookmarks();
});
</script>
