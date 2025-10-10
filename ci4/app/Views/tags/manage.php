<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<style>
    .tags-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }

    .tag-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.2s;
        border-left: 4px solid;
    }

    .tag-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }

    .tag-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .tag-card-name {
        font-weight: 600;
        font-size: 1rem;
        color: #1f2937;
    }

    .tag-card-actions {
        display: flex;
        gap: 8px;
    }

    .tag-card-btn {
        padding: 4px 8px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        font-size: 0.8rem;
        transition: all 0.2s;
    }

    .tag-card-btn:hover {
        transform: scale(1.1);
    }

    .btn-edit {
        background: #3b82f6;
        color: white;
    }

    .btn-delete {
        background: #ef4444;
        color: white;
    }

    .tag-card-description {
        color: #6b7280;
        font-size: 0.875rem;
        margin-top: 8px;
    }

    .tag-preview {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 8px;
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
        max-width: 500px;
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

    .color-picker-wrapper {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .color-picker {
        width: 60px;
        height: 40px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        cursor: pointer;
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
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #9ca3af;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-left: 10px;
    }

    .btn-secondary:hover {
        background: #6b7280;
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
</style>

<div class="white_card_body">
    <button class="quick-action-btn" onclick="openTagModal()">
        <i class="fa fa-plus"></i> Create New Tag
    </button>

    <div class="tags-container">
        <?php foreach ($tags as $tag): ?>
            <div class="tag-card" style="border-left-color: <?= $tag['color'] ?>">
                <div class="tag-card-header">
                    <div class="tag-card-name"><?= htmlspecialchars($tag['name']) ?></div>
                    <div class="tag-card-actions">
                        <button class="tag-card-btn btn-edit" onclick="editTag(<?= htmlspecialchars(json_encode($tag)) ?>)">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="tag-card-btn btn-delete" onclick="deleteTag(<?= $tag['id'] ?>, '<?= htmlspecialchars($tag['name']) ?>')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php if ($tag['description']): ?>
                    <div class="tag-card-description"><?= htmlspecialchars($tag['description']) ?></div>
                <?php endif; ?>
                <div class="tag-preview" style="background-color: <?= $tag['color'] ?>">
                    <?= htmlspecialchars($tag['name']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Tag Modal -->
<div id="tagModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeTagModal()">&times;</span>
        <div class="modal-header">
            <span id="modalTitle">Create New Tag</span>
        </div>
        <form id="tagForm" onsubmit="saveTag(event)">
            <input type="hidden" id="tagId" name="id">

            <div class="form-group">
                <label class="form-label">Tag Name *</label>
                <input type="text" class="form-control" id="tagName" name="name" required placeholder="Enter tag name">
            </div>

            <div class="form-group">
                <label class="form-label">Color *</label>
                <div class="color-picker-wrapper">
                    <input type="color" class="color-picker" id="tagColor" name="color" value="#667eea">
                    <input type="text" class="form-control" id="tagColorHex" value="#667eea" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="tagDescription" name="description" rows="3" placeholder="Optional description"></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-primary">
                    <i class="fa fa-save"></i> Save Tag
                </button>
                <button type="button" class="btn-secondary" onclick="closeTagModal()">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>
// Open modal for new tag
function openTagModal() {
    document.getElementById('tagModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Create New Tag';
    document.getElementById('tagForm').reset();
    document.getElementById('tagId').value = '';
    document.getElementById('tagColor').value = '#667eea';
    document.getElementById('tagColorHex').value = '#667eea';
}

// Open modal for editing tag
function editTag(tag) {
    document.getElementById('tagModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Edit Tag';
    document.getElementById('tagId').value = tag.id;
    document.getElementById('tagName').value = tag.name;
    document.getElementById('tagColor').value = tag.color;
    document.getElementById('tagColorHex').value = tag.color;
    document.getElementById('tagDescription').value = tag.description || '';
}

// Close modal
function closeTagModal() {
    document.getElementById('tagModal').style.display = 'none';
}

// Update hex display when color picker changes
document.getElementById('tagColor').addEventListener('input', function() {
    document.getElementById('tagColorHex').value = this.value;
});

// Save tag (create or update)
function saveTag(event) {
    event.preventDefault();

    const formData = new FormData(event.target);

    fetch('/tags/save', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error saving tag: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving tag');
    });
}

// Delete tag
function deleteTag(id, name) {
    if (confirm('Are you sure you want to delete the tag "' + name + '"? This will remove it from all projects, customers, and contacts.')) {
        window.location.href = '/tags/delete/' + id;
    }
}

// Close modal if clicked outside
window.onclick = function(event) {
    const modal = document.getElementById('tagModal');
    if (event.target == modal) {
        closeTagModal();
    }
}
</script>
