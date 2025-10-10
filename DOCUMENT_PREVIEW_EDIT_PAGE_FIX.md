# Document Preview on Edit Page - FIXED

## Issue
The `/documents/edit/6` page was not showing document previews.

## Root Cause
The edit view ([ci4/app/Views/documents/edit.php](ci4/app/Views/documents/edit.php#L29)) was using Google Drive viewer:

```php
<iframe src="https://drive.google.com/viewerng/viewer?embedded=true&url=<?= @$document->file ?>"
        width="960" height="1200"></iframe>
```

**Why this failed:**
- MinIO URLs are not publicly accessible from Google's servers
- Google Drive viewer cannot access private/local MinIO instances
- The document file is stored in MinIO at `http://172.178.0.1:9000/webimpetus/dev/documents/...`

## Solution Applied

### Updated edit.php View
Replaced Google Drive viewer with direct preview using our MinIO-compatible preview endpoints:

```php
<?php if (isset($document) && !empty($document->file)) {
    $fileExt = pathinfo($document->file, PATHINFO_EXTENSION);
    $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    $isImage = in_array(strtolower($fileExt), $imageExts);
?>
    <div class="row form-group">
        <div class="col-md-12">
            <h5>Current Document Preview</h5>

            <?php if ($isImage) { ?>
                <!-- Images display directly -->
                <img src="/documents/preview/<?= $document->uuid ?>"
                     style="max-width: 100%; height: auto;" />

            <?php } else if (strtolower($fileExt) === 'pdf') { ?>
                <!-- PDFs in iframe -->
                <iframe src="/documents/preview/<?= $document->uuid ?>"
                        width="100%" height="800"></iframe>

            <?php } else { ?>
                <!-- Other files show download button -->
                <a href="/documents/download/<?= $document->uuid ?>"
                   class="btn btn-primary">
                    <i class="fa fa-download"></i> Download Document
                </a>
            <?php } ?>
        </div>
    </div>
<?php } ?>
```

### Preview Strategy by File Type

| File Type | Display Method | Preview URL |
|-----------|----------------|-------------|
| **Images** (jpg, png, gif) | Direct `<img>` tag | `/documents/preview/{uuid}` |
| **PDFs** | Embedded `<iframe>` | `/documents/preview/{uuid}` |
| **Other files** | Download button | `/documents/download/{uuid}` |

### How It Works

1. **Image Files**: Browser renders directly from MinIO via preview endpoint
2. **PDF Files**: Browser's PDF viewer displays in iframe
3. **Other Files**: User downloads to view locally

### Example Document
Document ID 6 details:
- **UUID**: `a2f6e5f8-5292-55f3-913b-2958649c9360`
- **File**: `http://172.178.0.1:9000/webimpetus/dev/documents/1760080169/build-a-full-vat-return-app-in-15-mins.png`
- **Type**: Image (PNG)
- **Preview URL**: `/documents/preview/a2f6e5f8-5292-55f3-913b-2958649c9360`

## Files Modified

1. **[ci4/app/Views/documents/edit.php](ci4/app/Views/documents/edit.php#L26-L64)**
   - Removed Google Drive viewer iframe
   - Added smart preview based on file type
   - Uses UUID-based preview/download endpoints

## Routes Used

```php
// ci4/app/Config/Routes.php
$routes->get('documents/preview/(:segment)', 'Documents::preview/$1');  // Line 117
$routes->get('documents/download/(:segment)', 'Documents::download/$1'); // Line 118
```

## Testing

### Test Document Edit Page
```
http://localhost:5500/documents/edit/6
```

**Expected Result:**
- ✅ Page loads without fatal error
- ✅ Shows "Current Document Preview" heading
- ✅ Displays PNG image directly from MinIO
- ✅ Image loads from: `/documents/preview/a2f6e5f8-5292-55f3-913b-2958649c9360`

### Test Different File Types

**Image (current document 6):**
- Should show inline image preview
- Full size, responsive

**PDF Documents:**
- Will show in embedded iframe
- Browser's native PDF viewer

**Office/Other Files:**
- Shows download button
- Click to download from MinIO

## Status
✅ **FIXED** - Document previews now work on edit page using MinIO endpoints
