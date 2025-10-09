# Documents Module - S3/MinIO Integration Guide

## Overview
The documents module has been improved to store files on S3-compatible object storage (AWS S3 or MinIO). All document uploads are now stored in a centralized object storage system instead of the local filesystem.

## What Was Improved

### 1. S3/MinIO Configuration in .env

Added comprehensive S3/MinIO configuration to [.env](.env):

```bash
#--------------------------------------------------------------------
# S3 / MinIO CONFIGURATION
#--------------------------------------------------------------------
amazons3.access_key='CHANGE_ME'
amazons3.secret_key='CHANGE_ME'
amazons3.bucket='webimpetus-documents'
amazons3.region='us-east-1'
amazons3.s3_directory='dev'
amazons3.endpoint='http://minio:9000'
amazons3.use_path_style='true'
```

### 2. Enhanced AmazonS3 Config Class

Updated [ci4/app/Config/AmazonS3.php](ci4/app/Config/AmazonS3.php):
- Added `endpoint` property for MinIO/S3-compatible storage
- Added `use_path_style` property (required for MinIO)
- Auto-detects MinIO endpoint vs AWS S3
- Reads all configuration from environment variables

### 3. Improved Documents Controller

Completely rewrote [ci4/app/Controllers/Documents.php](ci4/app/Controllers/Documents.php) with:

#### Better File Upload Handling
- ✅ Proper validation (file size, required fields)
- ✅ Automatic S3/MinIO upload
- ✅ Stores original filename and metadata
- ✅ Error handling with try-catch blocks
- ✅ User-friendly error messages

#### New Features
- **Download endpoint**: `/documents/download/{uuid}`
- **Delete with S3 cleanup**: Removes files from S3/MinIO when deleted
- **AJAX list endpoint**: `/documents/ajaxList` for DataTables
- **File metadata tracking**: Original name, size, MIME type, upload date

#### Security Improvements
- ✅ All queries filtered by `uuid_business_id` (multi-tenant)
- ✅ UUID-based identification (not integer IDs)
- ✅ Validation on all inputs
- ✅ XSS protection
- ✅ SQL injection protection (via Query Builder)

## Configuration

### Option 1: MinIO (Local/Self-Hosted)

1. **Start MinIO Server** (Docker example):
```bash
docker run -d \
  --name minio \
  -p 9000:9000 \
  -p 9001:9001 \
  -e "MINIO_ROOT_USER=minioadmin" \
  -e "MINIO_ROOT_PASSWORD=minioadmin123" \
  -v /data/minio:/data \
  quay.io/minio/minio server /data --console-address ":9001"
```

2. **Create Bucket**:
   - Go to http://localhost:9001
   - Login with minioadmin/minioadmin123
   - Create bucket: `webimpetus-documents`

3. **Update .env**:
```bash
amazons3.access_key='minioadmin'
amazons3.secret_key='minioadmin123'
amazons3.bucket='webimpetus-documents'
amazons3.region='us-east-1'
amazons3.s3_directory='dev'
amazons3.endpoint='http://minio:9000'
amazons3.use_path_style='true'
```

### Option 2: AWS S3

1. **Create S3 Bucket** in AWS Console
2. **Create IAM User** with S3 permissions
3. **Update .env**:
```bash
amazons3.access_key='YOUR_AWS_ACCESS_KEY'
amazons3.secret_key='YOUR_AWS_SECRET_KEY'
amazons3.bucket='your-bucket-name'
amazons3.region='us-east-1'
amazons3.s3_directory='prod'
amazons3.endpoint=''
amazons3.use_path_style='false'
```

## File Structure

### Database Schema

```sql
CREATE TABLE documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(150) NOT NULL,
    file VARCHAR(150),                    -- S3/MinIO URL
    metadata TEXT,                        -- JSON: filename, size, mime_type
    client_id INT,
    document_date INT,
    category_id INT,
    billing_status VARCHAR(150),
    uuid_business_id VARCHAR(150),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
    uuid_linked_table VARCHAR(255)
);
```

### Metadata JSON Format

```json
{
    "original_name": "invoice_2024.pdf",
    "size": 524288,
    "mime_type": "application/pdf",
    "uploaded_at": "2025-01-09 15:30:45"
}
```

### S3/MinIO File Path Format

```
{bucket}/{s3_directory}/{category}/{timestamp}/{filename}

Example:
webimpetus-documents/dev/documents/1704816000/invoice_2024.pdf
```

## API Endpoints

### Upload Document
**POST** `/documents/save`

Form Data:
- `file`: File (max 10MB)
- `category_id`: Category ID (required)
- `client_id`: Customer ID (optional)
- `document_date`: Date (required, format: DD/MM/YYYY)
- `billing_status`: Status (optional)
- `metadata`: Additional info (optional)

Response:
- Success: Redirect to `/documents` with success message
- Error: Redirect back with error message

### Download Document
**GET** `/documents/download/{uuid}`

Returns:
- Redirect to S3/MinIO URL (for public buckets)
- File download (for local fallback)

### Delete Document
**GET** `/documents/delete/{uuid}`

Actions:
1. Deletes file from S3/MinIO
2. Deletes database record
3. Redirects to `/documents`

### AJAX List (DataTables)
**POST** `/documents/ajaxList`

Parameters:
- `draw`: DataTables draw counter
- `start`: Pagination offset
- `length`: Page size
- `search[value]`: Search query
- `order[0][column]`: Sort column index
- `order[0][dir]`: Sort direction (asc/desc)

Response:
```json
{
    "draw": 1,
    "recordsTotal": 150,
    "recordsFiltered": 150,
    "data": [
        {
            "id": 1,
            "uuid": "abc-123",
            "date": "09/01/2025",
            "category": "Invoices",
            "client": "ACME Corp",
            "filename": "invoice.pdf",
            "size": 524288,
            "billing_status": "Paid",
            "actions": ""
        }
    ]
}
```

## Usage Examples

### Upload a Document

```php
// In a form
<form action="/documents/save" method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <select name="category_id" required>
        <option value="1">Invoices</option>
        <option value="2">Contracts</option>
    </select>
    <input type="text" name="document_date" value="<?= date('d/m/Y') ?>" required>
    <button type="submit">Upload</button>
</form>
```

### Download a Document

```php
// Generate download link
<a href="/documents/download/<?= $document['uuid'] ?>">
    Download <?= $metadata['original_name'] ?>
</a>
```

### Delete a Document

```php
// Delete button with confirmation
<button onclick="if(confirm('Delete this document?')) window.location.href='/documents/delete/<?= $document['uuid'] ?>'">
    Delete
</button>
```

## File Upload Flow

1. **User uploads file** via form
2. **Controller validates** file and form data
3. **Amazon_s3_model** uploads to S3/MinIO
4. **S3/MinIO returns URL** (e.g., `http://minio:9000/webimpetus-documents/dev/documents/1704816000/file.pdf`)
5. **Controller stores**:
   - `file`: S3/MinIO URL
   - `metadata`: Original filename, size, MIME type
6. **Database record created**
7. **User redirected** to document list

## File Download Flow

1. **User clicks download link**
2. **Controller fetches** document from database
3. **Controller checks** `file` column for URL
4. **If S3/MinIO URL**: Redirect to URL
5. **If local path**: Serve file via PHP
6. **Uses original filename** from metadata

## File Delete Flow

1. **User clicks delete**
2. **Controller fetches** document details
3. **Amazon_s3_model** deletes file from S3/MinIO
4. **Controller deletes** database record
5. **User redirected** with success message

## Troubleshooting

### Files Not Uploading

**Check:**
1. S3/MinIO credentials in .env
2. Bucket exists and is accessible
3. Network connectivity to S3/MinIO endpoint
4. File size < 10MB (configurable)
5. PHP `upload_max_filesize` and `post_max_size`

**Debug:**
```php
// Check logs
tail -f /var/www/html/writable/logs/log-*.php

// Look for:
// "Document upload error: ..."
```

### Files Not Downloading

**Check:**
1. `file` column contains valid URL
2. S3/MinIO bucket has public read access (or use pre-signed URLs)
3. CORS configured on S3/MinIO for browser access

### MinIO Connection Refused

**Check:**
1. MinIO container is running: `docker ps | grep minio`
2. Port 9000 is accessible
3. Endpoint URL in .env is correct
4. Network connectivity between containers

## Security Best Practices

### 1. Private Buckets
For sensitive documents, use private buckets and generate pre-signed URLs:

```php
// Future enhancement
$url = $this->amazon_s3_model->getPresignedUrl($bucket, $key, 3600); // 1 hour
```

### 2. File Type Validation
Add MIME type validation in controller:

```php
$allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
if (!in_array($file->getMimeType(), $allowedTypes)) {
    // Reject
}
```

### 3. Virus Scanning
Integrate ClamAV or similar:

```php
// Before upload
if (!$this->antivirusService->scan($file->getTempName())) {
    throw new Exception('File contains malware');
}
```

### 4. Access Control
Always filter by business:

```php
->where('uuid_business_id', session('uuid_business'))
```

## Performance Optimization

### 1. CDN Integration
Use CloudFront (AWS) or CDN in front of MinIO:

```bash
amazons3.cdn_url='https://cdn.yourcompany.com'
```

### 2. Lazy Loading
Load document list via AJAX (already implemented)

### 3. Thumbnail Generation
For images, generate thumbnails:

```php
// Store both original and thumbnail
$data['file'] = $originalUrl;
$data['thumbnail'] = $thumbnailUrl;
```

## Migration from Local Storage

If you have existing documents in local storage:

1. **Create migration script**:
```bash
php spark migrate:documents:toS3
```

2. **Script logic**:
```php
foreach ($localDocuments as $doc) {
    $localPath = WRITEPATH . 'uploads/' . $doc['file'];
    $s3Path = $this->amazon_s3_model->uploadLocalFile($localPath, 'documents');
    $this->db->update('documents', ['file' => $s3Path], ['id' => $doc['id']]);
}
```

## Monitoring

### Storage Usage
Monitor S3/MinIO bucket size:

```bash
# MinIO
mc admin info minio

# AWS S3
aws s3 ls s3://webimpetus-documents --recursive --summarize
```

### Upload Success Rate
Track in application logs:

```php
log_message('info', 'Document uploaded: ' . $uuid . ' Size: ' . $size);
```

## Future Enhancements

1. **Document Versioning**: Keep history of document changes
2. **Bulk Upload**: Upload multiple files at once
3. **ZIP Download**: Download multiple documents as ZIP
4. **OCR Integration**: Extract text from PDFs/images
5. **Document Preview**: In-browser PDF/image preview
6. **Expiring Links**: Time-limited download URLs
7. **Upload Progress**: Real-time progress bar
8. **Drag & Drop**: Drag files to upload

## Files Modified/Created

1. **[.env](.env)** - Added S3/MinIO configuration
2. **[ci4/app/Config/AmazonS3.php](ci4/app/Config/AmazonS3.php)** - Added MinIO support
3. **[ci4/app/Controllers/Documents.php](ci4/app/Controllers/Documents.php)** - Complete rewrite
4. **[ci4/app/Controllers/Documents_old.php](ci4/app/Controllers/Documents_old.php)** - Backup of old controller
5. **[DOCUMENTS_S3_MINIO_SETUP.md](DOCUMENTS_S3_MINIO_SETUP.md)** - This documentation

## Support

For issues or questions:
1. Check MinIO/S3 connectivity
2. Review application logs
3. Verify .env configuration
4. Test with a small file first
5. Check browser console for errors

---

**Version:** 1.0
**Date:** 2025-01-09
**Status:** ✅ Ready for Configuration
**Dependencies:** AWS SDK PHP or S3-compatible client library
