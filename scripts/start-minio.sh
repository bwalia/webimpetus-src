#!/bin/bash

echo "🚀 Starting MinIO Object Storage..."
echo ""

# Start MinIO services
echo "1️⃣ Starting MinIO server and initializing bucket..."
docker-compose up -d minio minio-init

# Wait for MinIO to be ready
echo ""
echo "⏳ Waiting for MinIO to be ready..."
sleep 8

# Check if MinIO is running
if docker-compose ps minio | grep -q "Up"; then
    echo "✅ MinIO server is running"
else
    echo "❌ MinIO server failed to start"
    exit 1
fi

# Check bucket creation
echo ""
echo "2️⃣ Checking bucket creation..."
docker-compose logs minio-init | grep -q "successfully" && echo "✅ Bucket 'workerra-ci' created successfully" || echo "⚠️  Check bucket creation logs"

# Restart application
echo ""
echo "3️⃣ Restarting application to load MinIO configuration..."
docker-compose restart workerra-ci

echo ""
echo "✅ MinIO Setup Complete!"
echo ""
echo "📊 Access Points:"
echo "   - MinIO Console: http://localhost:9001"
echo "   - MinIO API: http://localhost:9000"
echo "   - Credentials: minioadmin / minioadmin123"
echo ""
echo "🧪 Test document upload:"
echo "   1. Go to your app: http://localhost:5500"
echo "   2. Navigate to Documents module"
echo "   3. Upload a test file"
echo "   4. Verify in MinIO Console: http://localhost:9001"
echo ""
echo "📝 View logs: docker-compose logs -f minio"
echo ""
