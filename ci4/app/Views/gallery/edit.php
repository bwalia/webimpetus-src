<?php require_once (APPPATH.'Views/common/edit-title.php'); ?>

<style>
    /* Modern Gallery Image Editor */
    .gallery-editor {
        max-width: 1200px;
        margin: 0 auto;
    }

    .upload-section {
        background: var(--bg-primary, #ffffff);
        border: 2px dashed var(--border-medium, #d1d5db);
        border-radius: var(--radius-lg, 12px);
        padding: 40px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .upload-section:hover {
        border-color: var(--primary, #667eea);
        background: var(--primary-light, #f0f9ff);
    }

    .upload-section.drag-over {
        border-color: var(--primary, #667eea);
        background: rgba(102, 126, 234, 0.1);
        transform: scale(1.02);
    }

    .upload-icon {
        font-size: 3rem;
        color: var(--primary, #667eea);
        margin-bottom: 16px;
    }

    .upload-text {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-700, #374151);
        margin-bottom: 8px;
    }

    .upload-hint {
        font-size: 0.875rem;
        color: var(--gray-500, #6b7280);
    }

    .upload-section input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    /* Image Preview */
    .image-preview-container {
        margin-top: 24px;
        display: none;
    }

    .image-preview-container.active {
        display: block;
    }

    .image-preview {
        background: var(--gray-50, #f9fafb);
        border-radius: var(--radius-lg, 12px);
        padding: 20px;
        text-align: center;
    }

    .image-preview img {
        max-width: 100%;
        max-height: 500px;
        border-radius: var(--radius-md, 8px);
        box-shadow: var(--shadow-lg, 0 4px 12px rgba(0,0,0,0.12));
    }

    .image-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .image-action-btn {
        padding: 10px 20px;
        border: none;
        border-radius: var(--radius-md, 8px);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .image-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md, 0 2px 8px rgba(0,0,0,0.08));
    }

    .btn-remove {
        background: #ef4444;
        color: white;
    }

    .btn-remove:hover {
        background: #dc2626;
    }

    .btn-change {
        background: var(--primary, #667eea);
        color: white;
    }

    .btn-change:hover {
        background: var(--primary-dark, #5a67d8);
    }

    /* Form Styling */
    .form-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700, #374151);
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        border: 1px solid var(--border-medium, #d1d5db);
        border-radius: var(--radius-md, 8px);
        padding: 10px 14px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary, #667eea);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    }

    /* Radio Buttons */
    .radio-group {
        display: flex;
        gap: 24px;
        margin-top: 8px;
    }

    .radio-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-weight: 500;
        color: var(--gray-700, #374151);
    }

    .radio-label input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary, #667eea);
    }

    /* Submit Button */
    .btn-primary {
        background: var(--primary, #667eea);
        border: none;
        border-radius: var(--radius-md, 8px);
        padding: 12px 32px;
        font-weight: 600;
        color: white;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 1rem;
    }

    .btn-primary:hover {
        background: var(--primary-dark, #5a67d8);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    }

    /* Loading Indicator */
    .upload-loading {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: var(--radius-lg, 12px);
        text-align: center;
    }

    .upload-loading.active {
        display: block;
    }

    .spinner {
        border: 4px solid var(--gray-100, #f3f4f6);
        border-top-color: var(--primary, #667eea);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 12px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="white_card_body gallery-editor">
    <div class="card-body">
        <form id="galleryForm" method="post" action="/gallery/update" enctype="multipart/form-data">

            <!-- Code Field -->
            <div class="form-group">
                <label for="code">Gallery Code / Name <span style="color: #ef4444;">*</span></label>
                <input type="text" class="form-control" id="code" name="code" value="<?=@$content->code?>" placeholder="Enter gallery code or name" required>
                <input type="hidden" name="id" value="<?=@$content->id?>" />
                <input type="hidden" name="uuid" value="<?=@$content->uuid?>" />
            </div>

            <!-- Image Upload Section -->
            <div class="form-group">
                <label>Gallery Image</label>

                <!-- Upload Zone -->
                <div class="upload-section" id="uploadZone">
                    <div class="upload-icon">
                        <i class="fa fa-cloud-upload"></i>
                    </div>
                    <div class="upload-text">Click to upload or drag and drop</div>
                    <div class="upload-hint">PNG, JPG, GIF up to 10MB</div>
                    <input type="file" name="file" id="fileInput" accept="image/*">
                </div>

                <!-- Loading Indicator -->
                <div class="upload-loading" id="uploadLoading">
                    <div class="spinner"></div>
                    <div style="font-weight: 600; color: var(--gray-700);">Uploading...</div>
                </div>

                <!-- Image Preview -->
                <div class="image-preview-container <?=!empty($content->name) ? 'active' : ''?>" id="imagePreviewContainer">
                    <div class="image-preview">
                        <img id="previewImage" src="<?=@$content->name?>" alt="Preview">
                        <div class="image-actions">
                            <button type="button" class="image-action-btn btn-change" onclick="document.getElementById('fileInput').click()">
                                <i class="fa fa-sync"></i> Change Image
                            </button>
                            <button type="button" class="image-action-btn btn-remove" id="removeImage">
                                <i class="fa fa-trash"></i> Remove Image
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Field -->
            <div class="form-group">
                <label>Status</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" value="1" name="status" <?=@$content->status==1?'checked':''?>>
                        <span>Active</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" value="0" name="status" <?=@$content->status==0?'checked':''?>>
                        <span>Inactive</span>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group" style="margin-top: 32px;">
                <button type="submit" class="btn-primary">
                    <i class="fa fa-save"></i> Save Gallery Item
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once (APPPATH.'Views/common/footer.php'); ?>

<script>
const uploadZone = document.getElementById('uploadZone');
const fileInput = document.getElementById('fileInput');
const previewContainer = document.getElementById('imagePreviewContainer');
const previewImage = document.getElementById('previewImage');
const uploadLoading = document.getElementById('uploadLoading');
const removeImageBtn = document.getElementById('removeImage');

// Drag and drop handlers
uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    e.stopPropagation();
    uploadZone.classList.add('drag-over');
});

uploadZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    e.stopPropagation();
    uploadZone.classList.remove('drag-over');
});

uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    e.stopPropagation();
    uploadZone.classList.remove('drag-over');

    const files = e.dataTransfer.files;
    if (files.length > 0 && files[0].type.startsWith('image/')) {
        handleImageUpload(files[0]);
    } else {
        toastr.error('Please upload a valid image file');
    }
});

// File input change handler
fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleImageUpload(e.target.files[0]);
    }
});

// Handle image upload
function handleImageUpload(file) {
    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        toastr.error('File size must be less than 10MB');
        return;
    }

    // Show loading
    uploadLoading.classList.add('active');

    // Create FormData
    const formData = new FormData();
    formData.append('file', file);
    formData.append('mainTable', 'gallery');
    formData.append('id', '<?=@$content->id?>');

    // Upload via AJAX
    $.ajax({
        url: '/gallery/uploadMediaFiles',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(result) {
            uploadLoading.classList.remove('active');

            if (result.status == '1' || result.status == 1) {
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.add('active');
                    uploadZone.style.display = 'none';
                };
                reader.readAsDataURL(file);

                toastr.success('Image uploaded successfully');
            } else {
                toastr.error(result.msg || 'Upload failed');
            }
        },
        error: function(xhr, status, error) {
            uploadLoading.classList.remove('active');
            console.error('Upload error:', error);
            toastr.error('Failed to upload image');
        }
    });
}

// Remove image handler
removeImageBtn.addEventListener('click', () => {
    if (confirm('Are you sure you want to remove this image?')) {
        previewContainer.classList.remove('active');
        uploadZone.style.display = 'block';
        fileInput.value = '';
        previewImage.src = '';
        toastr.info('Image removed. Don\'t forget to save changes.');
    }
});

// If image exists on load, hide upload zone
<?php if (!empty($content->name)): ?>
uploadZone.style.display = 'none';
<?php endif; ?>
</script>
