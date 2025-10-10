# MinIO Object Storage Setup Guide

## Overview
MinIO has been integrated into your docker-compose stack as an S3-compatible object storage solution for document uploads.

## What's Configured

### 1. Docker Services Added
- **minio** (Port 9000: API, Port 9001: Console)
  - S3-compatible object storage server
  - Stores files in `./minio-data` directory
  - Access credentials: `minioadmin` / `minioadmin123`

- **minio-init**
  - Automatically creates the `webimpetus` bucket on startup
  - Sets bucket to public download (files are downloadable)

### 2. Environment Variables (.env)
```bash
amazons3.access_key='minioadmin'
amazons3.secret_key='minioadmin123'
amazons3.bucket='webimpetus'
amazons3.region='us-east-1'
amazons3.s3_directory='dev'
amazons3.endpoint='http://minio:9000'
amazons3.use_path_style='true'
```

### 3. Application Configuration
Your application already supports S3/MinIO through:
- `/ci4/app/Config/AmazonS3.php` - Configuration class
- `/ci4/app/Models/Amazon_s3_model.php` - S3 operations model
- `/ci4/app/Libraries/Aws3.php` - AWS SDK wrapper

## How to Start MinIO

### Step 1: Start the Services
```bash
cd /home/bwalia/webimpetus-src
docker-compose up -d minio minio-init
```

### Step 2: Verify Services are Running
```bash
docker-compose ps
```

You should see:
- `webimpetus-minio` - running on ports 9000, 9001
- `webimpetus-minio-init` - completed (creates bucket then exits)

### Step 3: Check MinIO Logs
```bash
# Check MinIO server logs
docker-compose logs minio

# Check bucket creation logs
docker-compose logs minio-init
```

Expected output from minio-init:
```
Alias 'myminio' successfully created
Bucket created successfully `myminio/webimpetus`
Access permission set to 'download' successfully
MinIO bucket webimpetus created successfully
```

## Access MinIO

### MinIO Web Console
1. Open browser: http://localhost:9001
2. Login:
   - Username: `minioadmin`
   - Password: `minioadmin123`
3. Navigate to "Buckets" → You should see `webimpetus` bucket

### MinIO API Endpoint
- Internal (from app): `http://minio:9000`
- External (from host): `http://localhost:9000`

## Testing Document Upload

### Method 1: Using the Application

1. **Restart your webimpetus container** to load new .env variables:
   ```bash
   docker-compose restart webimpetus
   ```

2. **Navigate to Documents module** in your application
   - Go to `/documents` in your browser
   - Click "Upload Document" or "Add New"

3. **Upload a test file**
   - Select any file (PDF, image, etc.)
   - Fill in required fields
   - Submit the form

4. **Verify upload in MinIO Console**
   - Go to http://localhost:9001
   - Login and navigate to `webimpetus` bucket
   - Look for files in the `dev/` directory

### Method 2: Check Database Records

After uploading a document, verify it was stored:

```sql
-- Check documents table for S3 URLs
SELECT id, name, file_path, created_at
FROM documents
ORDER BY created_at DESC
LIMIT 5;
```

The `file_path` should contain MinIO URLs like:
- `http://minio:9000/webimpetus/dev/filename.pdf`

### Method 3: Direct MinIO Test (Optional)

Test MinIO directly using MinIO Client (mc):

```bash
# Enter the minio container
docker exec -it webimpetus-minio sh

# Inside container, install mc client
mc alias set local http://localhost:9000 minioadmin minioadmin123

# List buckets
mc ls local

# Upload a test file
echo "test content" > test.txt
mc cp test.txt local/webimpetus/dev/test.txt

# List files in bucket
mc ls local/webimpetus/dev/

# Download test file
mc cp local/webimpetus/dev/test.txt downloaded.txt
cat downloaded.txt
```

## Troubleshooting

### Issue 1: Application Can't Connect to MinIO

**Symptoms**: Upload fails with connection error

**Solution**:
1. Verify MinIO is running: `docker-compose ps minio`
2. Check network connectivity:
   ```bash
   docker exec -it webimpetus-dev ping minio
   ```
3. Restart webimpetus to reload .env:
   ```bash
   docker-compose restart webimpetus
   ```

### Issue 2: Bucket Not Found Error

**Symptoms**: Error "Bucket 'webimpetus' does not exist"

**Solution**:
1. Check if bucket was created:
   ```bash
   docker-compose logs minio-init
   ```
2. Manually create bucket:
   ```bash
   docker exec -it webimpetus-minio sh
   mc alias set local http://localhost:9000 minioadmin minioadmin123
   mc mb local/webimpetus
   mc anonymous set download local/webimpetus
   ```

### Issue 3: Permission Denied

**Symptoms**: Upload succeeds but can't access files

**Solution**:
1. Set bucket policy to public:
   ```bash
   docker exec -it webimpetus-minio sh
   mc alias set local http://localhost:9000 minioadmin minioadmin123
   mc anonymous set download local/webimpetus
   ```

### Issue 4: .env Changes Not Applied

**Symptoms**: Still using old S3 settings

**Solution**:
```bash
# Restart the application container
docker-compose restart webimpetus

# Or rebuild if needed
docker-compose up -d --build webimpetus
```

## Verify Integration

### Check if S3 is Enabled

1. Check the Amazon_s3_model is being used:
   ```bash
   docker exec -it webimpetus-dev grep -r "Amazon_s3_model" /var/www/html/app/Controllers/
   ```

2. Look for S3 configuration loading:
   ```bash
   docker exec -it webimpetus-dev php -r "
   require '/var/www/html/vendor/autoload.php';
   echo getenv('amazons3.access_key') . PHP_EOL;
   echo getenv('amazons3.endpoint') . PHP_EOL;
   echo getenv('amazons3.bucket') . PHP_EOL;
   "
   ```

Expected output:
```
minioadmin
http://minio:9000
webimpetus
```

## File Structure

After successful upload, your MinIO structure will look like:

```
webimpetus/                    # Bucket name
└── dev/                       # Directory from s3_directory config
    ├── documents/             # Uploaded documents
    │   ├── file1.pdf
    │   └── file2.docx
    ├── images/                # Uploaded images
    │   ├── photo1.jpg
    │   └── photo2.png
    └── media/                 # Other media files
        └── video1.mp4
```

## Accessing Files

### Public URL Format
Files can be accessed via:
```
http://localhost:9000/webimpetus/dev/documents/filename.pdf
```

### From Application
The application uses internal Docker network:
```
http://minio:9000/webimpetus/dev/documents/filename.pdf
```

## Production Considerations

### Security (For Production)

1. **Change default credentials**:
   ```yaml
   environment:
     MINIO_ROOT_USER: your-secure-username
     MINIO_ROOT_PASSWORD: your-secure-password-min-8-chars
   ```

2. **Update .env** with new credentials:
   ```bash
   amazons3.access_key='your-secure-username'
   amazons3.secret_key='your-secure-password'
   ```

3. **Enable HTTPS**: Use nginx/traefik as reverse proxy with SSL

4. **Set proper bucket policies**: Don't use anonymous download for sensitive files

### Backup Strategy

1. **Volume backup**:
   ```bash
   # Backup MinIO data
   tar -czf minio-backup-$(date +%Y%m%d).tar.gz ./minio-data
   ```

2. **MinIO mirror** (to another MinIO or S3):
   ```bash
   mc mirror local/webimpetus s3-backup/webimpetus
   ```

## Quick Commands Reference

```bash
# Start MinIO
docker-compose up -d minio minio-init

# Stop MinIO
docker-compose stop minio

# View MinIO logs
docker-compose logs -f minio

# Restart application to reload env
docker-compose restart webimpetus

# Access MinIO CLI
docker exec -it webimpetus-minio sh

# Check bucket contents
docker exec -it webimpetus-minio mc ls local/webimpetus/dev/
```

## Summary

✅ **MinIO is configured and ready to use!**

**Next Steps:**
1. Start MinIO: `docker-compose up -d minio minio-init`
2. Restart app: `docker-compose restart webimpetus`
3. Access console: http://localhost:9001 (minioadmin/minioadmin123)
4. Upload a document in your app
5. Verify in MinIO console

Your documents will now be stored in MinIO instead of local filesystem! 🚀
