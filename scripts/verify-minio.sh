#!/bin/bash

echo "🔍 MinIO Integration Verification"
echo "=================================="
echo ""

# Check if MinIO container is running
echo "1️⃣ Checking MinIO container status..."
if docker-compose ps minio | grep -q "Up"; then
    echo "   ✅ MinIO container is running"
    MINIO_RUNNING=true
else
    echo "   ❌ MinIO container is NOT running"
    echo "   Run: docker-compose up -d minio minio-init"
    MINIO_RUNNING=false
fi

echo ""

# Check MinIO health
if [ "$MINIO_RUNNING" = true ]; then
    echo "2️⃣ Checking MinIO health endpoint..."
    if curl -sf http://localhost:9000/minio/health/live > /dev/null 2>&1; then
        echo "   ✅ MinIO health check passed"
    else
        echo "   ⚠️  MinIO health check failed (may still be starting)"
    fi
fi

echo ""

# Check bucket existence
if [ "$MINIO_RUNNING" = true ]; then
    echo "3️⃣ Checking if 'workerra-ci' bucket exists..."
    BUCKET_CHECK=$(docker exec workerra-ci-minio mc ls local/workerra-ci 2>&1)
    if echo "$BUCKET_CHECK" | grep -q "Unable to list"; then
        echo "   ❌ Bucket 'workerra-ci' not found"
        echo "   Creating bucket..."
        docker exec workerra-ci-minio mc mb local/workerra-ci 2>/dev/null
        docker exec workerra-ci-minio mc anonymous set download local/workerra-ci 2>/dev/null
        echo "   ✅ Bucket created"
    else
        echo "   ✅ Bucket 'workerra-ci' exists"
    fi
fi

echo ""

# Check .env configuration
echo "4️⃣ Checking .env configuration..."
if grep -q "amazons3.access_key='minioadmin'" .env; then
    echo "   ✅ MinIO access key configured"
else
    echo "   ❌ MinIO access key not configured in .env"
fi

if grep -q "amazons3.bucket='workerra-ci'" .env; then
    echo "   ✅ MinIO bucket configured"
else
    echo "   ❌ MinIO bucket not configured in .env"
fi

if grep -q "amazons3.endpoint='http://minio:9000'" .env; then
    echo "   ✅ MinIO endpoint configured"
else
    echo "   ❌ MinIO endpoint not configured in .env"
fi

echo ""

# Check app container can reach MinIO
echo "5️⃣ Checking network connectivity (app → MinIO)..."
if docker exec workerra-ci-dev ping -c 1 minio > /dev/null 2>&1; then
    echo "   ✅ Application can reach MinIO"
else
    echo "   ❌ Application cannot reach MinIO"
    echo "   Try: docker-compose restart workerra-ci"
fi

echo ""

# Check if app has loaded MinIO config
echo "6️⃣ Checking if application loaded MinIO config..."
ACCESS_KEY=$(docker exec workerra-ci-dev php -r "echo getenv('amazons3.access_key');" 2>/dev/null)
if [ "$ACCESS_KEY" = "minioadmin" ]; then
    echo "   ✅ Application loaded MinIO credentials"
else
    echo "   ❌ Application hasn't loaded MinIO credentials"
    echo "   Run: docker-compose restart workerra-ci"
fi

echo ""
echo "=================================="
echo "📊 Summary"
echo "=================================="

if [ "$MINIO_RUNNING" = true ] && [ "$ACCESS_KEY" = "minioadmin" ]; then
    echo "✅ MinIO is ready to use!"
    echo ""
    echo "🌐 Access Points:"
    echo "   - MinIO Console: http://localhost:9001"
    echo "   - MinIO API: http://localhost:9000"
    echo "   - Credentials: minioadmin / minioadmin123"
    echo ""
    echo "🧪 Test Upload:"
    echo "   1. Go to http://localhost:5500"
    echo "   2. Navigate to Documents"
    echo "   3. Upload a test file"
    echo "   4. Check MinIO Console: http://localhost:9001"
else
    echo "⚠️  MinIO setup incomplete"
    echo ""
    echo "📝 Quick Fix:"
    echo "   1. Run: docker-compose up -d minio minio-init"
    echo "   2. Run: docker-compose restart workerra-ci"
    echo "   3. Run this script again: ./verify-minio.sh"
fi

echo ""
