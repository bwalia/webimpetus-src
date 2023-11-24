#!/bin/bash

# Temporary Directory
TMP_DIR="/tmp"

# Timestamp for the dump file
TIMESTAMP=$(date +%Y%m%d%H%M%S)

# MySQL Dump File Name
DUMP_FILE="$TMP_DIR/db_dump_$TIMESTAMP.sql"
DUMP_FILE_TAR="$TMP_DIR/db_dump_$TIMESTAMP.tar.gz"
echo "$KUBE_CONFIG" | base64 -d > .kube/config/k3s2.yaml
export KUBECONFIG=.kube/config/k3s2.yaml

DB_HOST=`kubectl get secrets mariadb-secret-$TARGET_ENV -n $TARGET_ENV -o "jsonpath={.data.hostname}" | base64 -d`
DB_USER=`kubectl get secrets mariadb-secret-$TARGET_ENV -n $TARGET_ENV -o "jsonpath={.data.username}" | base64 -d`
DB_PASSWORD=`kubectl get secrets mariadb-secret-$TARGET_ENV -n $TARGET_ENV -o "jsonpath={.data.password}" | base64 -d`
DB_PORT=`kubectl get secrets mariadb-secret-$TARGET_ENV -n $TARGET_ENV -o "jsonpath={.data.port}" | base64 -d`

# Create MySQL Dump
mysqldump -h $DB_HOST -u $DB_USER -p $DB_PASSWORD $DB_NAME > $DUMP_FILE

if [ $? -eq 0 ]; then
    echo "MySQL dump created successfully: $DUMP_FILE"
else
    echo "Error creating MySQL dump."
    exit 1
fi

# Upload Dump File to Minio Bucket
mc config host add myminio $MINIO_HOST $MINIO_ACCESS_KEY $MINIO_SECRET_KEY
ls -alh $DUMP_FILE
file $DUMP_FILE
stat $DUMP_FILE
tar -czvf $DUMP_FILE_TAR $TMP_DIR
mc cp $DUMP_FILE_TAR myminio/$MINIO_BUCKET/

if [ $? -eq 0 ]; then
    echo "Dump file uploaded to Minio bucket: $MINIO_BUCKET"
else
    echo "Error uploading dump file to Minio bucket."
    exit 1
fi

# Clean up temporary dump file
rm $DUMP_FILE

echo "Script completed."
