# Documents Module Fix

## Issue
Error when accessing `/documents`:
```
Unknown column 'category_name' in 'field list'
```

## Root Cause
The categories table uses column name `name`, not `category_name`. The new controller was selecting a non-existent column.

## Fixes Applied

### 1. Fixed Column Name in All Queries

**File:** `ci4/app/Controllers/Documents.php`

Changed all occurrences from:
```php
->select('id, category_name')  // ❌ Wrong
```

To:
```php
->select('id, name as category_name')  // ✅ Correct
```

**Locations fixed:**
- Line 39: `index()` method - categories for filtering
- Line 81: `edit()` method - categories for form dropdown
- Line 311: `ajaxList()` method - categories in JOIN query
- Line 319: `ajaxList()` method - categories in search

### 2. Restored Compatibility with Existing Views

The old `documents/edit.php` view expects:
- `$document` as an **object** (not array)
- Action URL: `/documents/update` (not `/documents/save`)
- ID parameter as integer (not UUID)

**Changes:**
- Simplified `edit()` method to use `$this->model->getRows($id)->getRow()`
- Added `update()` method for form submission
- Kept `save()` method for future UUID-based endpoints

### 3. Fixed S3/MinIO Integration

The `update()` method now properly:
- Uploads files to S3/MinIO using `amazon_s3_model`
- Stores S3/MinIO URL in database
- Handles upload errors gracefully
- Uses `insertOrUpdate()` for create/update logic

## Files Modified

1. **`ci4/app/Controllers/Documents.php`**
   - Fixed all `category_name` references
   - Restored `edit($id)` with integer ID
   - Added `update()` method for backward compatibility
   - Simplified controller logic

## Testing Checklist

- [x] `/documents` page loads without error
- [ ] Can view list of documents
- [ ] Can click "Edit" on a document
- [ ] Edit form shows categories and customers
- [ ] Can upload a new document file
- [ ] File uploads to S3/MinIO
- [ ] File URL stored in database
- [ ] Can download document
- [ ] Can delete document

## Configuration Required

Before documents will upload to S3/MinIO, configure in `.env`:

```bash
amazons3.access_key='YOUR_ACCESS_KEY'
amazons3.secret_key='YOUR_SECRET_KEY'
amazons3.bucket='workerra-ci-documents'
amazons3.region='us-east-1'
amazons3.s3_directory='dev'
amazons3.endpoint='http://minio:9000'      # For MinIO
amazons3.use_path_style='true'             # Required for MinIO
```

**For AWS S3 (instead of MinIO):**
```bash
amazons3.endpoint=''
amazons3.use_path_style='false'
```

## Next Steps

1. **Configure S3/MinIO**
   - Set credentials in `.env`
   - Create bucket `workerra-ci-documents`
   - Test connectivity

2. **Test File Upload**
   - Go to `/documents`
   - Click "Add New" or edit existing document
   - Upload a test file
   - Verify file appears in S3/MinIO bucket
   - Verify URL stored in database

3. **Test File Download**
   - Click download link
   - Should redirect to S3/MinIO URL
   - File should download successfully

## Error Resolution

### Before Fix
```
Database Error Occurred
Error Number: 1054
Unknown column 'category_name' in 'field list'

SELECT `id`, `category_name` FROM `categories`
WHERE `uuid_business_id` = 'd15c707f-bd87-574a-9237-a40286bfaaa9'
```

### After Fix
```sql
SELECT `id`, `name` as `category_name` FROM `categories`
WHERE `uuid_business_id` = 'd15c707f-bd87-574a-9237-a40286bfaaa9'
```

## Table Schema Reference

### categories table
```sql
CREATE TABLE categories (
    id INT(25) PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(36),
    name VARCHAR(124),           -- ✅ Correct column name
    notes TEXT,
    uuid_business_id VARCHAR(150),
    ...
);
```

### documents table
```sql
CREATE TABLE documents (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(150),
    file VARCHAR(150),           -- S3/MinIO URL stored here
    metadata TEXT,
    client_id INT,
    document_date INT,
    category_id INT,             -- FK to categories.id
    billing_status VARCHAR(150),
    uuid_business_id VARCHAR(150),
    ...
);
```

---

**Status:** ✅ Fixed
**Date:** 2025-01-09
**Issue:** Column name mismatch
**Solution:** Use `name as category_name` in SELECT statements
