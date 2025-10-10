# Document Preview & Download Feature - Complete Guide

## Overview

I've added document preview and download functionality that retrieves files from MinIO and serves them directly through the application.

## Features Added

### 1. **Preview Documents**
View documents inline in the browser (images, PDFs, etc.)

**Endpoint**: `GET /documents/preview/{document-uuid}`

### 2. **Download Documents**
Force download of documents with original filename

**Endpoint**: `GET /documents/download/{document-uuid}`

## Implementation Details

### Code Added ([ci4/app/Controllers/Documents.php](ci4/app/Controllers/Documents.php))

#### Preview Function (Lines 376-437)
```php
public function preview($uuid = null)
{
    // 1. Validates UUID
    // 2. Fetches document from database
    // 3. Retrieves file from MinIO using AWS S3 SDK
    // 4. Sets proper Content-Type header
    // 5. Serves file inline (Content-Disposition: inline)
    // 6. Caches for 1 hour
}
```

#### Download Function (Lines 442-497)
```php
public function download($uuid = null)
{
    // 1. Validates UUID
    // 2. Fetches document from database
    // 3. Retrieves file from MinIO
    // 4. Sets download headers (Content-Disposition: attachment)
    // 5. Uses original filename
    // 6. Forces browser download
}
```

#### MIME Type Helper (Lines 502-520)
Supports common file types:
- Images: JPG, PNG, GIF
- Documents: PDF, DOC, DOCX, XLS, XLSX
- Archives: ZIP
- Text: TXT

### Routes Added ([ci4/app/Config/Routes.php](ci4/app/Config/Routes.php:117-118))

```php
$routes->get('documents/preview/(:segment)', 'Documents::preview/$1');
$routes->get('documents/download/(:segment)', 'Documents::download/$1');
```

## Usage

### 1. Preview in Browser

**URL Format**:
```
http://localhost:5500/documents/preview/{document-uuid}
```

**Example**:
```
http://localhost:5500/documents/preview/abc123-def456-ghi789
```

**Behavior**:
- Images: Display in browser
- PDFs: Open in browser PDF viewer
- Other files: Display or prompt download based on browser

**Headers Set**:
```
Content-Type: [auto-detected from file]
Content-Disposition: inline; filename="..."
Cache-Control: public, max-age=3600
```

### 2. Download File

**URL Format**:
```
http://localhost:5500/documents/download/{document-uuid}
```

**Example**:
```
http://localhost:5500/documents/download/abc123-def456-ghi789
```

**Behavior**:
- Always forces download
- Uses original filename
- Works for all file types

**Headers Set**:
```
Content-Type: [auto-detected]
Content-Disposition: attachment; filename="original-name.pdf"
Content-Length: [file-size]
```

## Integration with Documents List

### Add Preview/Download Links

In your documents list view, add preview and download links:

```html
<!-- In documents/list.php or similar -->
<a href="/documents/preview/<?= $doc['uuid'] ?>"
   target="_blank"
   class="btn btn-sm btn-info">
   <i class="fa fa-eye"></i> Preview
</a>

<a href="/documents/download/<?= $doc['uuid'] ?>"
   class="btn btn-sm btn-success">
   <i class="fa fa-download"></i> Download
</a>
```

### Add Preview Modal

For inline preview without leaving the page:

```html
<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Document Preview</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" style="height: 80vh;">
        <iframe id="previewFrame"
                style="width: 100%; height: 100%; border: none;">
        </iframe>
      </div>
    </div>
  </div>
</div>

<script>
function previewDocument(uuid) {
    $('#previewFrame').attr('src', '/documents/preview/' + uuid);
    $('#previewModal').modal('show');
}
</script>
```

## Security Features

### 1. Business UUID Validation
Only users from the same business can access documents:
```php
->where('uuid_business_id', session('uuid_business'))
```

### 2. Authentication Required
Preview/download routes inherit CommonController authentication

### 3. No Direct MinIO Access
Files are proxied through the application, not exposed directly

### 4. Error Handling
- 400: Bad request (missing UUID)
- 404: Document not found
- 500: Server error (logged for debugging)

## File Flow

```
User Request
    ↓
/documents/preview/{uuid}
    ↓
Documents Controller
    ↓
MySQL (fetch metadata)
    ↓
MinIO/S3 SDK (fetch BLOB)
    ↓
Stream to Browser
```

## Supported File Types

### Images (Preview in Browser)
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)

### Documents (Browser Dependent)
- PDF (.pdf) - Most browsers show inline
- Word (.doc, .docx) - Usually downloads
- Excel (.xls, .xlsx) - Usually downloads

### Text Files (Preview in Browser)
- Plain text (.txt)

### Archives (Force Download)
- ZIP (.zip)

## Error Handling

### Document Not Found
```php
return $this->response->setStatusCode(404)
    ->setBody('Document not found');
```

### File Not in MinIO
```php
return $this->response->setStatusCode(404)
    ->setBody('File not found for this document');
```

### MinIO Connection Error
```php
log_message('error', 'Document preview error: ' . $e->getMessage());
return $this->response->setStatusCode(500)
    ->setBody('Error loading document');
```

## Performance Optimization

### Caching Headers
Preview responses include:
```
Cache-Control: public, max-age=3600
```

Browser caches files for 1 hour, reducing MinIO requests.

### Content-Length
Download responses include file size for progress indication:
```
Content-Length: [bytes]
```

## Testing

### Test Preview

1. Upload a document via /documents
2. Note the document UUID from database:
   ```sql
   SELECT uuid, name, file FROM documents ORDER BY created_at DESC LIMIT 1;
   ```
3. Visit: `http://localhost:5500/documents/preview/{uuid}`
4. File should display in browser

### Test Download

1. Visit: `http://localhost:5500/documents/download/{uuid}`
2. File should download with original filename
3. Check Downloads folder

### Test via API

```bash
# Get document UUID from API
curl -X GET "http://localhost:5500/api/v2/documents?params=%7B%22filter%22%3A%7B%22uuid_business_id%22%3A%220f6c4e64-9b50-5e11-a7d1-1923b7aef282%22%7D%7D"

# Preview document
curl -X GET "http://localhost:5500/documents/preview/{uuid}" > preview.pdf

# Download document
curl -X GET "http://localhost:5500/documents/download/{uuid}" > download.pdf
```

## Troubleshooting

### Issue: 403 Forbidden

**Cause**: Wrong business UUID or not logged in

**Solution**:
- Ensure you're logged in
- Verify document belongs to your business

### Issue: Empty/Corrupted File

**Cause**: MinIO configuration incorrect

**Solution**:
1. Check .env MinIO settings
2. Verify Aws3 library loads config correctly
3. Test MinIO connection:
   ```bash
   curl http://172.178.0.1:9000/minio/health/live
   ```

### Issue: Slow Loading

**Cause**: Large files or slow MinIO connection

**Solution**:
- Enable browser caching (already implemented)
- Consider CDN for production
- Optimize images before upload

### Issue: PDF Won't Preview

**Cause**: Browser PDF viewer disabled

**Solution**:
- Enable PDF viewer in browser settings
- Or use download instead of preview

## Advanced Features (Future)

### 1. Thumbnail Generation
Generate thumbnails for images on upload:
```php
// In update() method after upload
if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
    $this->generateThumbnail($filePath);
}
```

### 2. Signed URLs
Generate temporary URLs for secure sharing:
```php
$url = $s3Client->createPresignedRequest(
    $s3Client->getCommand('GetObject', [
        'Bucket' => $bucket,
        'Key' => $filePath
    ]),
    '+20 minutes'
)->getUri();
```

### 3. Streaming for Large Files
Use streaming for files > 10MB:
```php
$stream = $result['Body'];
while (!$stream->eof()) {
    echo $stream->read(1024 * 1024); // 1MB chunks
    flush();
}
```

## Summary

✅ **Preview Function Added**: View files inline in browser
✅ **Download Function Added**: Force download with original filename
✅ **Routes Configured**: /documents/preview/{uuid} and /documents/download/{uuid}
✅ **Security Implemented**: Business UUID validation + authentication
✅ **Error Handling**: Proper HTTP status codes and logging
✅ **MIME Type Detection**: Auto-detects content type
✅ **Caching**: 1-hour browser cache for performance

**Your documents can now be previewed and downloaded directly from MinIO!** 🎉

## Quick Reference

| Action | URL | Result |
|--------|-----|--------|
| Preview | `/documents/preview/{uuid}` | Shows in browser |
| Download | `/documents/download/{uuid}` | Forces download |
| List | `/documents` | Shows all documents |
| Upload | `/documents/edit` | Upload new document |

## Permission Fix

**Note**: If you get "Unknown slash command: documents" error:

1. **Log out and log back in** to reload permissions
2. Or check your permissions include menu ID 28 (Documents)
3. Alternative: Clear browser cache/cookies

Your permission (28) is already in the database, you just need to refresh your session!
