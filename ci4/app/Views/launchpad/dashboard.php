<?php require_once (APPPATH . 'Views/common/header.php'); ?>
<?php require_once (APPPATH . 'Views/common/sidebar.php'); ?>
<section class="main_content dashboard_part large_header_bg">
<?php require_once (APPPATH . 'Views/common/top-header.php'); ?>

<style>
/* Main Container */
.launchpad-container {
    padding: 0;
    background: transparent;
    width: 100%;
    max-width: 100%;
    overflow-x: hidden;
}

/* Adjust main content area based on sidebar state */
.main_content_iner {
    transition: margin-left 0.5s ease;
}

/* When sidebar is expanded (default - 270px wide) */
@media (min-width: 992px) {
    .main_content_iner {
        margin-left: 0;
    }
}

/* When sidebar is collapsed (70px wide) */
@media (min-width: 992px) {
    .full_main_content .main_content_iner {
        margin-left: 0;
    }
}

/* Filter Bar */
.filter-bar {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
    width: 100%;
    box-sizing: border-box;
}

.filter-tabs {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
}

.filter-chip {
    padding: 8px 16px;
    border-radius: 20px;
    background: #e5e7eb;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid transparent;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.filter-chip:hover {
    background: #d1d5db;
}

.filter-chip.active {
    background: #667eea;
    color: white;
    border-color: #5a67d8;
}

.filter-chip .count {
    background: rgba(255,255,255,0.3);
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 11px;
}

/* Category Sections */
.category-section {
    margin-bottom: 30px;
    width: 100%;
    box-sizing: border-box;
}

.category-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    padding: 12px 0;
    border-bottom: 2px solid #e5e7eb;
}

.category-title {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 8px;
}

.category-count {
    background: #667eea;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

/* Bookmark Grid */
.bookmarks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 14px;
    width: 100%;
    box-sizing: border-box;
}

/* Bookmark Card - Tiny Boxes */
.bookmark-card {
    background: white;
    border-radius: 10px;
    padding: 14px;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    position: relative;
    border-top: 3px solid #667eea;
    min-height: 130px;
    display: flex;
    flex-direction: column;
}

.bookmark-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
}

.bookmark-card.favorite {
    border-top-color: #fbbf24;
}

/* Bookmark Icon */
.bookmark-icon {
    width: 40px;
    height: 40px;
    margin: 0 auto 10px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
}

/* Bookmark Content */
.bookmark-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
    font-size: 12px;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    flex-grow: 1;
}

.bookmark-category-tag {
    font-size: 10px;
    color: #6b7280;
    background: #f3f4f6;
    padding: 3px 8px;
    border-radius: 8px;
    display: inline-block;
    margin-top: auto;
}

/* Badges */
.favorite-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    color: #fbbf24;
    font-size: 12px;
}

.shared-badge {
    position: absolute;
    top: 6px;
    left: 6px;
    color: #10b981;
    font-size: 11px;
}

/* Quick Actions Menu */
.bookmark-actions {
    position: absolute;
    top: 8px;
    right: 8px;
    opacity: 0;
    transition: opacity 0.2s;
}

.bookmark-card:hover .bookmark-actions {
    opacity: 1;
}

.action-btn {
    background: rgba(0,0,0,0.6);
    color: white;
    border: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 11px;
    margin-left: 4px;
    transition: all 0.2s;
}

.action-btn:hover {
    background: rgba(0,0,0,0.8);
    transform: scale(1.1);
}

.action-btn.share-btn {
    background: #3b82f6;
}

.action-btn.copy-btn {
    background: #10b981;
}

.action-btn.share-btn:hover {
    background: #2563eb;
}

.action-btn.copy-btn:hover {
    background: #059669;
}

/* Stats Bar */
.bookmark-stats {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #e5e7eb;
    font-size: 10px;
    color: #9ca3af;
}

/* Add Button */
.add-bookmark-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-size: 24px;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    cursor: pointer;
    z-index: 1000;
    transition: all 0.3s;
}

.add-bookmark-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 24px rgba(102, 126, 234, 0.6);
}

/* Search Box */
.search-box {
    flex: 1;
    max-width: 400px;
}

.search-box input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border: 2px solid #e5e7eb;
    border-radius: 24px;
    font-size: 14px;
    transition: all 0.2s;
}

.search-box input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

/* Share Modal Styles */
.share-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.share-modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}

.share-modal-header {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #1f2937;
}

.user-list {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 20px;
}

.user-item {
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.user-item:hover {
    background: #f3f4f6;
    border-color: #667eea;
}

.user-item.selected {
    background: #ede9fe;
    border-color: #667eea;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.3;
}

/* Tooltip */
.tooltip-custom {
    position: relative;
    display: inline-block;
}

.tooltip-custom .tooltiptext {
    visibility: hidden;
    background-color: #1f2937;
    color: white;
    text-align: center;
    padding: 6px 10px;
    border-radius: 6px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    font-size: 11px;
    opacity: 0;
    transition: opacity 0.2s;
}

.tooltip-custom:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

/* Responsive */
@media (max-width: 991px) {
    .launchpad-container {
        padding: 15px;
    }

    .filter-bar {
        padding: 15px;
    }
}

@media (max-width: 768px) {
    .bookmarks-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
    }

    .filter-tabs {
        gap: 8px;
    }

    .filter-chip {
        padding: 6px 12px;
        font-size: 12px;
    }

    .launchpad-container {
        padding: 10px;
    }

    .search-box {
        max-width: 100%;
        margin-bottom: 10px;
    }
}

@media (max-width: 575px) {
    .bookmarks-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 8px;
    }

    .bookmark-card {
        padding: 10px;
        min-height: 110px;
    }

    .bookmark-icon {
        width: 32px;
        height: 32px;
        font-size: 16px;
    }
}
</style>

<div class="main_content_iner overly_inner">
    <div class="container-fluid p-0">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="page_title_box d-flex align-items-center justify-content-between">
                    <div class="page_title_left">
                        <h3 class="f_s_30 f_w_700" style="color: #1f2937 !important;">Launchpad</h3>
                        <p class="f_s_14" style="color: #6b7280 !important;">Your personalized bookmark dashboard</p>
                    </div>
                    <div>
                        <a href="/launchpad/manage" class="btn btn-light">
                            <i class="fa fa-cog"></i> Manage Bookmarks
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="white_card card_height_100 mb_20">
                    <div class="white_card_body">
                        <div class="launchpad-container">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="filter-tabs">
                    <div class="search-box">
                        <i class="fa fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search bookmarks..." onkeyup="filterBookmarks()">
                    </div>

                    <div class="filter-chip active" data-filter="all" onclick="setFilter('all')">
                        <i class="fa fa-th"></i> All <span class="count" id="countAll">0</span>
                    </div>
                    <div class="filter-chip" data-filter="favorites" onclick="setFilter('favorites')">
                        <i class="fa fa-star"></i> Favorites <span class="count" id="countFavorites">0</span>
                    </div>
                    <div class="filter-chip" data-filter="shared" onclick="setFilter('shared')">
                        <i class="fa fa-share-alt"></i> Shared <span class="count" id="countShared">0</span>
                    </div>
                </div>
            </div>

            <!-- Bookmarks Container -->
            <div id="bookmarksContainer" style="width: 100%; overflow-x: hidden;">
                <div class="text-center" style="padding: 60px;">
                    <i class="fa fa-spinner fa-spin fa-3x text-muted"></i>
                    <p class="mt-3 text-muted">Loading bookmarks...</p>
                </div>
            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Bookmark Button -->
<button class="add-bookmark-btn" onclick="window.location.href='/launchpad/manage'" title="Add Bookmark">
    <i class="fa fa-plus"></i>
</button>

<!-- Share Modal -->
<div id="shareModal" class="share-modal">
    <div class="share-modal-content">
        <span class="close" onclick="closeShareModal()" style="float: right; cursor: pointer; font-size: 24px; color: #9ca3af;">&times;</span>
        <div class="share-modal-header">
            <i class="fa fa-share-alt"></i> Share Bookmark
        </div>
        <p class="text-muted mb-3">Select users to share "<span id="shareBookmarkTitle"></span>" with:</p>

        <div class="user-list" id="userList">
            <div class="text-center p-3">
                <i class="fa fa-spinner fa-spin"></i> Loading users...
            </div>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="canEdit">
            <label class="form-check-label" for="canEdit">Allow users to edit this bookmark</label>
        </div>

        <div style="display: flex; gap: 10px;">
            <button class="btn btn-primary" onclick="shareBookmarkWithUsers()">
                <i class="fa fa-share"></i> Share
            </button>
            <button class="btn btn-secondary" onclick="closeShareModal()">Cancel</button>
        </div>
    </div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>
// Check if jQuery is loaded
if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded!');
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('bookmarksContainer').innerHTML = '<div class="alert alert-danger">Error: jQuery is not loaded. Please refresh the page.</div>';
    });
} else {
    console.log('jQuery loaded successfully');
}

let bookmarks = [];
let users = [];
let currentFilter = 'all';
let currentBookmarkToShare = null;
let selectedUsers = new Set();

// Load data on page load
$(document).ready(function() {
    console.log('Document ready - loading bookmarks...');
    console.log('jQuery version:', $.fn.jquery);

    // Add timeout fallback
    setTimeout(function() {
        if (bookmarks.length === 0) {
            console.warn('Bookmarks still not loaded after 10 seconds');
            const container = $('#bookmarksContainer');
            if (container.find('.fa-spinner').length > 0) {
                container.html('<div class="alert alert-warning">Loading is taking longer than expected. <a href="#" onclick="loadBookmarks(); return false;">Click here to retry</a></div>');
            }
        }
    }, 10000);

    loadBookmarks();
    loadUsers();
});

// Load bookmarks
function loadBookmarks() {
    console.log('Calling /launchpad/getBookmarks...');
    $.get('/launchpad/getBookmarks?include_shared=true', function(response) {
        console.log('Response received:', response);
        if (response.status) {
            bookmarks = response.data;
            console.log('Loaded bookmarks:', bookmarks.length);
            updateCounts();
            renderBookmarks();
        } else {
            console.error('Response status false:', response);
            $('#bookmarksContainer').html('<div class="alert alert-danger">Error loading bookmarks: ' + (response.message || 'Unknown error') + '</div>');
        }
    }).fail(function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
        console.error('Response:', xhr.responseText);
        $('#bookmarksContainer').html('<div class="alert alert-danger">Failed to load bookmarks. Error: ' + error + '</div>');
    });
}

// Load users for sharing
function loadUsers() {
    $.get('/launchpad/getUsers', function(response) {
        if (response.status) {
            users = response.data;
        }
    });
}

// Update filter counts
function updateCounts() {
    const all = bookmarks.length;
    const favorites = bookmarks.filter(b => b.is_favorite == 1).length;
    const shared = bookmarks.filter(b => b.owner_name && b.owner_name != '<?= session('uname') ?>').length;

    $('#countAll').text(all);
    $('#countFavorites').text(favorites);
    $('#countShared').text(shared);
}

// Set filter
function setFilter(filter) {
    currentFilter = filter;
    $('.filter-chip').removeClass('active');
    $(`.filter-chip[data-filter="${filter}"]`).addClass('active');
    renderBookmarks();
}

// Filter bookmarks
function filterBookmarks() {
    renderBookmarks();
}

// Render bookmarks
function renderBookmarks() {
    console.log('Rendering bookmarks...');
    const container = $('#bookmarksContainer');
    const searchTerm = $('#searchInput').val().toLowerCase();

    // Filter bookmarks
    let filtered = bookmarks;

    if (currentFilter === 'favorites') {
        filtered = bookmarks.filter(b => b.is_favorite == 1);
    } else if (currentFilter === 'shared') {
        filtered = bookmarks.filter(b => b.owner_name && b.owner_name != '<?= session('uname') ?>');
    }

    if (searchTerm) {
        filtered = filtered.filter(b =>
            b.title.toLowerCase().includes(searchTerm) ||
            b.url.toLowerCase().includes(searchTerm) ||
            (b.category && b.category.toLowerCase().includes(searchTerm))
        );
    }

    if (filtered.length === 0) {
        container.html(`
            <div class="empty-state">
                <i class="fa fa-bookmark"></i>
                <h4>No bookmarks found</h4>
                <p>Try adjusting your search or filter.</p>
            </div>
        `);
        return;
    }

    // Group by category
    const grouped = {};
    filtered.forEach(bookmark => {
        const category = bookmark.category || 'Uncategorized';
        if (!grouped[category]) {
            grouped[category] = [];
        }
        grouped[category].push(bookmark);
    });

    // Render by category
    let html = '';
    Object.keys(grouped).sort().forEach(category => {
        const items = grouped[category];
        html += `
            <div class="category-section">
                <div class="category-header">
                    <div class="category-title">
                        <i class="fa fa-folder"></i> ${category}
                    </div>
                    <div class="category-count">${items.length}</div>
                </div>
                <div class="bookmarks-grid">
                    ${items.map(b => renderBookmarkCard(b)).join('')}
                </div>
            </div>
        `;
    });

    container.html(html);
}

// Render individual bookmark card
function renderBookmarkCard(bookmark) {
    const isShared = bookmark.owner_name && bookmark.owner_name != '<?= session('uname') ?>';
    const isFavorite = bookmark.is_favorite == 1;

    return `
        <div class="bookmark-card ${isFavorite ? 'favorite' : ''}" style="border-top-color: ${bookmark.color}">
            ${isFavorite ? '<i class="fa fa-star favorite-badge"></i>' : ''}
            ${isShared ? '<i class="fa fa-share-alt shared-badge"></i>' : ''}

            <div class="bookmark-actions" onclick="event.stopPropagation()">
                <button class="action-btn copy-btn tooltip-custom" onclick="copyToClipboard('${bookmark.url}', '${bookmark.title}')" title="Copy Link">
                    <i class="fa fa-copy"></i>
                    <span class="tooltiptext">Copy Link</span>
                </button>
                ${!isShared ? `
                    <button class="action-btn share-btn tooltip-custom" onclick="openShareModal('${bookmark.uuid}', '${escapeHtml(bookmark.title)}')" title="Share">
                        <i class="fa fa-share"></i>
                        <span class="tooltiptext">Share</span>
                    </button>
                ` : ''}
            </div>

            <div onclick="openBookmark('${bookmark.uuid}', '${bookmark.url}')">
                <div class="bookmark-icon" style="background-color: ${bookmark.color}">
                    <i class="fa fa-link"></i>
                </div>
                <div class="bookmark-title">${escapeHtml(bookmark.title)}</div>
                ${bookmark.category ? `<div class="bookmark-category-tag">${escapeHtml(bookmark.category)}</div>` : ''}
                <div class="bookmark-stats">
                    <span title="Clicks"><i class="fa fa-mouse-pointer"></i> ${bookmark.click_count || 0}</span>
                </div>
            </div>
        </div>
    `;
}

// Open bookmark
function openBookmark(uuid, url) {
    // Record click
    $.post('/launchpad/click/' + uuid);

    // Open in new tab
    window.open(url, '_blank');
}

// Copy to clipboard
function copyToClipboard(url, title) {
    navigator.clipboard.writeText(url).then(function() {
        // Show success message
        showToast('success', `Link copied to clipboard!`, title);
    }).catch(function(err) {
        showToast('error', 'Failed to copy link', 'Please try again');
    });
}

// Open share modal
function openShareModal(uuid, title) {
    currentBookmarkToShare = uuid;
    selectedUsers.clear();
    $('#shareBookmarkTitle').text(title);
    $('#canEdit').prop('checked', false);

    // Render user list
    let userHtml = '';
    users.forEach(user => {
        userHtml += `
            <div class="user-item" onclick="toggleUserSelection('${user.uuid}')">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-user-circle" style="font-size: 24px; color: #667eea;"></i>
                    <div>
                        <div style="font-weight: 600;">${escapeHtml(user.name)}</div>
                        <div style="font-size: 12px; color: #6b7280;">${escapeHtml(user.email)}</div>
                    </div>
                    <i class="fa fa-check" style="margin-left: auto; display: none; color: #10b981;"></i>
                </div>
            </div>
        `;
    });

    $('#userList').html(userHtml || '<p class="text-muted text-center p-3">No users available</p>');
    $('#shareModal').show();
}

// Close share modal
function closeShareModal() {
    $('#shareModal').hide();
    currentBookmarkToShare = null;
    selectedUsers.clear();
}

// Toggle user selection
function toggleUserSelection(userUuid) {
    const userItem = event.currentTarget;

    if (selectedUsers.has(userUuid)) {
        selectedUsers.delete(userUuid);
        $(userItem).removeClass('selected');
        $(userItem).find('.fa-check').hide();
    } else {
        selectedUsers.add(userUuid);
        $(userItem).addClass('selected');
        $(userItem).find('.fa-check').show();
    }
}

// Share bookmark with selected users
function shareBookmarkWithUsers() {
    if (selectedUsers.size === 0) {
        showToast('warning', 'Please select at least one user', '');
        return;
    }

    const canEdit = $('#canEdit').is(':checked') ? 1 : 0;
    let completed = 0;
    let errors = 0;

    selectedUsers.forEach(userUuid => {
        $.ajax({
            url: '/launchpad/share',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                uuid_bookmark_id: currentBookmarkToShare,
                uuid_shared_with_user_id: userUuid,
                can_edit: canEdit
            }),
            success: function(response) {
                completed++;
                if (!response.status) {
                    errors++;
                }
                checkComplete();
            },
            error: function() {
                completed++;
                errors++;
                checkComplete();
            }
        });
    });

    function checkComplete() {
        if (completed === selectedUsers.size) {
            if (errors === 0) {
                showToast('success', `Bookmark shared with ${selectedUsers.size} user(s)`, '');
            } else {
                showToast('warning', `Shared with ${selectedUsers.size - errors} user(s). ${errors} failed.`, '');
            }
            closeShareModal();
        }
    }
}

// Show toast notification
function showToast(type, title, message) {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };

    const toast = $(`
        <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; background: white; padding: 16px 20px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 300px; border-left: 4px solid ${colors[type]}; animation: slideIn 0.3s ease;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fa ${icons[type]}" style="font-size: 24px; color: ${colors[type]};"></i>
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">${title}</div>
                    ${message ? `<div style="font-size: 13px; color: #6b7280;">${message}</div>` : ''}
                </div>
            </div>
        </div>
    `);

    $('body').append(toast);

    setTimeout(() => {
        toast.fadeOut(300, function() { $(this).remove(); });
    }, 3000);
}

// Escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('shareModal');
    if (event.target == modal) {
        closeShareModal();
    }
}

// Add CSS animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
`;
document.head.appendChild(style);
</script>
