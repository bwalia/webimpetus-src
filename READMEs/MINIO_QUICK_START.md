# MinIO Quick Start Guide

## ✅ What Was Done

MinIO has been successfully integrated into your Docker Compose stack for S3-compatible object storage.

### Changes Made:

1. **docker-compose.yml** - Added two services:
   - `minio` - Object storage server (ports 9000, 9001)
   - `minio-init` - Automatic bucket creation on startup

2. **.env** - Updated MinIO configuration:
   ```bash
   amazons3.access_key='minioadmin'
   amazons3.secret_key='minioadmin123'
   amazons3.bucket='webimpetus'
   amazons3.endpoint='http://minio:9000'
   amazons3.use_path_style='true'
   ```

3. **Your application already supports MinIO** via:
   - AmazonS3.php config (reads from .env)
   - Amazon_s3_model.php (S3 operations)
   - Aws3.php library (AWS SDK)

## 🚀 Quick Start (2 Methods)

### Method 1: Use the Start Script
```bash
cd /home/bwalia/webimpetus-src
./start-minio.sh
```

### Method 2: Manual Start
```bash
cd /home/bwalia/webimpetus-src

# Start MinIO services
docker-compose up -d minio minio-init

# Restart application to load new config
docker-compose restart webimpetus
```

## 🔍 Verify It's Working

### 1. Check MinIO is Running
```bash
docker-compose ps minio
```
Should show: `Up` status on ports 9000, 9001

### 2. Access MinIO Console
- **URL**: http://localhost:9001
- **Username**: minioadmin
- **Password**: minioadmin123
- **What to check**: You should see a bucket named `webimpetus`

### 3. Test Document Upload
1. Go to your app: http://localhost:5500
2. Log in as admin@admin.com
3. Navigate to **Documents** module
4. Click **"Add New"** or **"Upload Document"**
5. Select a test file and upload
6. Go to MinIO Console (http://localhost:9001)
7. Navigate to: **Buckets** → **webimpetus** → **dev/**
8. You should see your uploaded file!

## 📦 Where Files Are Stored

### On Host (Your Machine)
```
/home/bwalia/webimpetus-src/minio-data/
└── webimpetus/          # bucket
    └── dev/             # directory from config
        └── [your files]
```

### MinIO URLs
- **Internal** (app → MinIO): `http://minio:9000/webimpetus/dev/filename.pdf`
- **External** (browser): `http://localhost:9000/webimpetus/dev/filename.pdf`

## 🛠️ Useful Commands

```bash
# View MinIO logs
docker-compose logs -f minio

# View bucket creation logs
docker-compose logs minio-init

# Stop MinIO
docker-compose stop minio

# Restart everything
docker-compose restart

# Access MinIO shell
docker exec -it webimpetus-minio sh
```

## ⚙️ Configuration Details

### MinIO Credentials
- **Access Key**: minioadmin
- **Secret Key**: minioadmin123
- **Bucket Name**: webimpetus
- **Directory**: dev

### Ports
- **9000**: MinIO API (S3-compatible endpoint)
- **9001**: MinIO Web Console

### Network
- **IP**: 172.178.0.12
- **Network**: webimpetus-network

## 🐛 Troubleshooting

### Problem: Upload fails with connection error
```bash
# Restart app to reload .env
docker-compose restart webimpetus

# Check MinIO is running
docker-compose ps minio

# Check app can reach MinIO
docker exec -it webimpetus-dev ping minio
```

### Problem: Bucket not found
```bash
# Check bucket creation logs
docker-compose logs minio-init

# Manually create bucket if needed
docker exec -it webimpetus-minio sh
mc alias set local http://localhost:9000 minioadmin minioadmin123
mc mb local/webimpetus
mc anonymous set download local/webimpetus
```

### Problem: Can't access uploaded files
```bash
# Make bucket publicly readable
docker exec -it webimpetus-minio sh
mc alias set local http://localhost:9000 minioadmin minioadmin123
mc anonymous set download local/webimpetus
```

## 📚 Full Documentation

For detailed information, see: [MINIO_SETUP.md](MINIO_SETUP.md)

## 🎯 Next Steps

1. ✅ Start MinIO: `./start-minio.sh`
2. ✅ Access console: http://localhost:9001
3. ✅ Upload a test document
4. ✅ Verify in MinIO console
5. 📝 Consider changing credentials for production (see MINIO_SETUP.md)

That's it! Your documents are now stored in MinIO! 🎉
