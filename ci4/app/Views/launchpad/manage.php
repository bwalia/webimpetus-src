<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<style>
    .bookmarks-table-container {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-top: 20px;
    }

    .bookmark-row {
        display: grid;
        grid-template-columns: 40px 3fr 2fr 1fr 1fr 1fr 120px;
        gap: 16px;
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        align-items: center;
    }

    .bookmark-row:hover {
        background: #f9fafb;
    }

    .bookmark-header {
        font-weight: 600;
        color: #374151;
        background: #f3f4f6;
        padding: 12px;
        border-radius: 8px;
    }

    .bookmark-color {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        border: 2px solid #e5e7eb;
    }

    .bookmark-title {
        font-weight: 600;
        color: #1f2937;
    }

    .bookmark-url {
        color: #6b7280;
        font-size: 0.875rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .bookmark-stats {
        text-align: center;
        color: #6b7280;
    }

    .bookmark-actions {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .btn-edit {
        background: #3b82f6;
        color: white;
    }

    .btn-delete {
        background: #ef4444;
        color: white;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .quick-action-btn {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        background-color: #10b981;
        color: white;
        margin-bottom: 20px;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-public {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-private {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-favorite {
        background: #fef3c7;
        color: #92400e;
    }

    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .modal-header {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: #1f2937;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #374151;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.9rem;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .close {
        float: right;
        font-size: 1.5rem;
        font-weight: 700;
        cursor: pointer;
        color: #9ca3af;
    }

    .close:hover {
        color: #1f2937;
    }

    .btn-primary {
        background: #667eea;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        background: #5568d3;
    }

    .btn-secondary {
        background: #9ca3af;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        margin-left: 10px;
    }
</style>

<div class="white_card_body">
    <button class="quick-action-btn" onclick="openBookmarkModal()">
        <i class="fa fa-plus"></i> Add New Bookmark
    </button>

    <div class="bookmarks-table-container">
        <div class="bookmark-row bookmark-header">
            <div></div>
            <div>Title</div>
            <div>URL</div>
            <div>Category</div>
            <div>Clicks</div>
            <div>Visibility</div>
            <div>Actions</div>
        </div>
        <div id="bookmarksList">
            <!-- Populated by JavaScript -->
        </div>
    </div>
</div>

<!-- Bookmark Modal -->
<div id="bookmarkModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeBookmarkModal()">&times;</span>
        <div class="modal-header">
            <span id="modalTitle">Add New Bookmark</span>
        </div>
        <form id="bookmarkForm" onsubmit="saveBookmark(event)">
            <input type="hidden" id="bookmarkUuid" name="uuid">

            <div class="form-group">
                <label class="form-label">Title *</label>
                <input type="text" class="form-control" id="bookmarkTitle" name="title" required placeholder="Enter bookmark title">
            </div>

            <div class="form-group">
                <label class="form-label">URL *</label>
                <input type="url" class="form-control" id="bookmarkUrl" name="url" required placeholder="https://example.com">
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="bookmarkDescription" name="description" rows="2" placeholder="Optional description"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Category</label>
                <input type="text" class="form-control" id="bookmarkCategory" name="category" placeholder="e.g., Work, Development, Tools">
            </div>

            <div class="form-group">
                <label class="form-label">Color</label>
                <input type="color" class="form-control" id="bookmarkColor" name="color" value="#667eea">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" id="bookmarkFavorite" name="is_favorite" value="1">
                    Mark as Favorite
                </label>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" id="bookmarkPublic" name="is_public" value="1">
                    Make Public (visible to all users)
                </label>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-primary">
                    <i class="fa fa-save"></i> Save Bookmark
                </button>
                <button type="button" class="btn-secondary" onclick="closeBookmarkModal()">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>
let bookmarks = [];

// Load bookmarks on page load
$(document).ready(function() {
    loadBookmarks();
});

function loadBookmarks() {
    $.get('/launchpad/getBookmarks?include_shared=true', function(response) {
        if (response.status) {
            bookmarks = response.data;
            renderBookmarksList();
        }
    });
}

function renderBookmarksList() {
    const container = $('#bookmarksList');

    if (bookmarks.length === 0) {
        container.html('<div style="padding: 40px; text-align: center; color: #9ca3af;">No bookmarks yet. Click "Add New Bookmark" to create your first one!</div>');
        return;
    }

    let html = '';
    bookmarks.forEach(bookmark => {
        const isShared = bookmark.owner_name && bookmark.owner_name != '<?= session('name') ?>';
        html += `
            <div class="bookmark-row">
                <div>
                    <div class="bookmark-color" style="background-color: ${bookmark.color}"></div>
                </div>
                <div class="bookmark-title">
                    ${bookmark.title}
                    ${bookmark.is_favorite == 1 ? '<i class="fa fa-star" style="color: #f59e0b; margin-left: 8px;"></i>' : ''}
                </div>
                <div class="bookmark-url">
                    <a href="${bookmark.url}" target="_blank">${bookmark.url}</a>
                </div>
                <div>${bookmark.category || '-'}</div>
                <div class="bookmark-stats">${bookmark.click_count || 0}</div>
                <div>
                    ${bookmark.is_public == 1 ? '<span class="badge badge-public">Public</span>' : '<span class="badge badge-private">Private</span>'}
                    ${isShared ? '<span class="badge badge-favorite" style="margin-left: 4px;">Shared</span>' : ''}
                </div>
                <div class="bookmark-actions">
                    ${!isShared ? `
                        <button class="btn-action btn-edit" onclick='editBookmark(${JSON.stringify(bookmark)})'>
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete" onclick="deleteBookmark('${bookmark.uuid}', '${bookmark.title}')">
                            <i class="fa fa-trash"></i>
                        </button>
                    ` : '<span style="color: #9ca3af;">-</span>'}
                </div>
            </div>
        `;
    });

    container.html(html);
}

function openBookmarkModal() {
    document.getElementById('bookmarkModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Add New Bookmark';
    document.getElementById('bookmarkForm').reset();
    document.getElementById('bookmarkUuid').value = '';
    document.getElementById('bookmarkColor').value = '#667eea';
}

function editBookmark(bookmark) {
    document.getElementById('bookmarkModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Edit Bookmark';
    document.getElementById('bookmarkUuid').value = bookmark.uuid;
    document.getElementById('bookmarkTitle').value = bookmark.title;
    document.getElementById('bookmarkUrl').value = bookmark.url;
    document.getElementById('bookmarkDescription').value = bookmark.description || '';
    document.getElementById('bookmarkCategory').value = bookmark.category || '';
    document.getElementById('bookmarkColor').value = bookmark.color;
    document.getElementById('bookmarkFavorite').checked = bookmark.is_favorite == 1;
    document.getElementById('bookmarkPublic').checked = bookmark.is_public == 1;
}

function closeBookmarkModal() {
    document.getElementById('bookmarkModal').style.display = 'none';
}

function saveBookmark(event) {
    event.preventDefault();

    const formData = {
        uuid: document.getElementById('bookmarkUuid').value,
        title: document.getElementById('bookmarkTitle').value,
        url: document.getElementById('bookmarkUrl').value,
        description: document.getElementById('bookmarkDescription').value,
        category: document.getElementById('bookmarkCategory').value,
        color: document.getElementById('bookmarkColor').value,
        is_favorite: document.getElementById('bookmarkFavorite').checked ? 1 : 0,
        is_public: document.getElementById('bookmarkPublic').checked ? 1 : 0
    };

    $.ajax({
        url: '/launchpad/save',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(formData),
        success: function(response) {
            if (response.status) {
                alert('Bookmark saved successfully!');
                closeBookmarkModal();
                loadBookmarks();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error saving bookmark');
        }
    });
}

function deleteBookmark(uuid, title) {
    if (confirm(`Are you sure you want to delete "${title}"?`)) {
        $.post('/launchpad/delete/' + uuid, function(response) {
            if (response.status) {
                alert('Bookmark deleted successfully');
                loadBookmarks();
            } else {
                alert('Error deleting bookmark');
            }
        });
    }
}

// Close modal if clicked outside
window.onclick = function(event) {
    const modal = document.getElementById('bookmarkModal');
    if (event.target == modal) {
        closeBookmarkModal();
    }
}
</script>
