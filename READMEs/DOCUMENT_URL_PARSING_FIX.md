# Document Preview/Download URL Parsing - FIXED

## Issue
Download/preview URLs were failing with error:
```
Error downloading document: Error executing "GetObject" on
"http://172.178.0.1:9000/workerra-ci/http%3A//172.178.0.1%3A9000/workerra-ci/dev/documents/..."
XMinioInvalidObjectName (client): Object name contains unsupported characters.
```

## Root Cause

### Problem 1: Double Path
The `file` field in the database contained **full URLs**:
```
http://172.178.0.1:9000/workerra-ci/dev/documents/1760080169/build-a-full-vat-return-app-in-15-mins.png
```

But the preview/download methods treated it as a **key** and prepended bucket + endpoint again:
```
Bucket: workerra-ci
Key: http://172.178.0.1:9000/workerra-ci/dev/documents/...
Result: http://172.178.0.1:9000/workerra-ci/http://172.178.0.1:9000/workerra-ci/dev/documents/...
```

### Problem 2: Newline Character
Database value had trailing newline: `...png\n`

This created an invalid object name in MinIO.

## Solution Applied

### Updated Both Methods
Modified `preview()` and `download()` methods in [ci4/app/Controllers/Documents.php](ci4/app/Controllers/Documents.php):

```php
// Clean up the file path - remove newlines and trim
$filePath = trim($document['file']);

// If file is a full URL, extract just the key/path
if (filter_var($filePath, FILTER_VALIDATE_URL)) {
    // Parse the URL to extract the path after the bucket name
    $parsedUrl = parse_url($filePath);
    $path = $parsedUrl['path'] ?? '';

    // Remove leading slash and bucket name from path
    // Example: /workerra-ci/dev/documents/... -> dev/documents/...
    $path = ltrim($path, '/');
    if (strpos($path, $bucket . '/') === 0) {
        $filePath = substr($path, strlen($bucket) + 1);
    } else {
        $filePath = $path;
    }
}
```

### URL Parsing Logic

**Input (from database):**
```
http://172.178.0.1:9000/workerra-ci/dev/documents/1760080169/build-a-full-vat-return-app-in-15-mins.png\n
```

**Step 1 - Trim:**
```
http://172.178.0.1:9000/workerra-ci/dev/documents/1760080169/build-a-full-vat-return-app-in-15-mins.png
```

**Step 2 - Parse URL:**
```php
parse_url() -> ['path' => '/workerra-ci/dev/documents/1760080169/build-a-full-vat-return-app-in-15-mins.png']
```

**Step 3 - Remove leading slash:**
```
workerra-ci/dev/documents/1760080169/build-a-full-vat-return-app-in-15-mins.png
```

**Step 4 - Remove bucket name:**
```
dev/documents/1760080169/build-a-full-vat-return-app-in-15-mins.png
```

**Final S3 Request:**
```php
$s3Client->getObject([
    'Bucket' => 'workerra-ci',
    'Key' => 'dev/documents/1760080169/build-a-full-vat-return-app-in-15-mins.png'
]);
```

## Files Modified

1. **[ci4/app/Controllers/Documents.php](ci4/app/Controllers/Documents.php#L362-L379)**
   - `preview()` method: Added URL parsing logic (lines 362-379)

2. **[ci4/app/Controllers/Documents.php](ci4/app/Controllers/Documents.php#L444-L461)**
   - `download()` method: Added URL parsing logic (lines 444-461)

## Testing

### Test Preview
```bash
curl -v "http://localhost:5500/documents/preview/a2f6e5f8-5292-55f3-913b-2958649c9360"
```

**Expected:**
- ✅ 200 OK
- ✅ Content-Type: image/png
- ✅ Image data in response body

### Test Download
```bash
curl -v "http://localhost:5500/documents/download/a2f6e5f8-5292-55f3-913b-2958649c9360"
```

**Expected:**
- ✅ 200 OK
- ✅ Content-Disposition: attachment
- ✅ File downloads successfully

### Test in Browser
Visit: `http://localhost:5500/documents/edit/6`

**Expected:**
- ✅ Image preview displays inline
- ✅ No errors in console
- ✅ Image loads from MinIO via preview endpoint

## Database Cleanup (Optional)

To remove trailing newlines from existing records:

```sql
UPDATE documents
SET file = TRIM(file)
WHERE file LIKE '%\n';
```

## Status
✅ **FIXED** - Preview and download now correctly parse full URLs and extract object keys
