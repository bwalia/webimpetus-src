# Documents Download Fatal Error - FIXED

## Issue
Fatal error when accessing `/documents/edit/6`:
```
Fatal error: Cannot redeclare App\Controllers\Documents::download()
in /var/www/html/app/Controllers/Documents.php on line 442
```

## Root Cause
The Documents.php controller had **TWO** `download()` methods defined:

1. **First download()** (line 203-243): Simple method that:
   - Redirected to S3 URL for remote files
   - Downloaded from local storage as fallback
   - Did not fetch files from MinIO/S3

2. **Second download()** (line 442-497): Enhanced method that:
   - Fetches files directly from MinIO/S3 using AWS SDK
   - Serves files with proper headers
   - Forces download with original filename

## Solution Applied

### 1. Removed Old download() Method
Deleted the simple download() method (lines 203-243) that only redirected to URLs.

### 2. Enhanced New download() Method
Updated the remaining download() method to handle metadata properly:

```php
// Get original filename from metadata or fallback
$metadata = !empty($document['metadata']) ? json_decode($document['metadata'], true) : [];
$filename = $metadata['original_name'] ?? ($document['original_filename'] ?? basename($document['file']));
```

This ensures the download uses the original filename from:
1. First priority: `metadata.original_name` (JSON field)
2. Second priority: `original_filename` database column
3. Fallback: Extract filename from file path

### 3. Restarted Container
Restarted workerra-ci container to clear OpCache:
```bash
docker-compose restart workerra-ci
```

## Current State

**Documents.php now has only ONE download() method at line 401**

### Methods Available:
- ✅ `preview($uuid)` - Line 335: View files inline in browser
- ✅ `download($uuid)` - Line 401: Download files from MinIO
- ✅ `getMimeType($filename)` - Line 461: Helper for content types

### Routes:
```php
$routes->get('documents/preview/(:segment)', 'Documents::preview/$1');
$routes->get('documents/download/(:segment)', 'Documents::download/$1');
```

## Files Modified

1. **[ci4/app/Controllers/Documents.php](ci4/app/Controllers/Documents.php)**
   - Removed duplicate download() method (old lines 203-243)
   - Enhanced remaining download() for metadata handling (line 401)

## Testing

### Test Preview
```
http://localhost:5500/documents/preview/{document-uuid}
```

### Test Download
```
http://localhost:5500/documents/download/{document-uuid}
```

### Test Edit Page (Was Broken)
```
http://localhost:5500/documents/edit/6
```

## Status
✅ **FIXED** - Fatal error resolved, only one download() method exists now
