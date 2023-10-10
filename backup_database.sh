#!/bin/bash

# MySQL Database Information
DB_HOST="webimpetus-db"
DB_USER="wsl_dev"
DB_PASSWORD="CHANGE_ME"
DB_NAME="myworkstation_dev"

# Minio Information
MINIO_HOST="<HOST>"
MINIO_ACCESS_KEY="<ACCESS KEY>"
MINIO_SECRET_KEY="<SECRET_KEY"
MINIO_BUCKET="mysql-backup"

# Temporary Directory
TMP_DIR="/tmp"

# Timestamp for the dump file
TIMESTAMP=$(date +%Y%m%d%H%M%S)

# MySQL Dump File Name
DUMP_FILE="$TMP_DIR/db_dump_$TIMESTAMP.sql"
DUMP_FILE_TAR="$TMP_DIR/db_dump_$TIMESTAMP.tar.gz"

# Create MySQL Dump
mysqldump -h$DB_HOST -u$DB_USER -p$DB_PASSWORD $DB_NAME > $DUMP_FILE

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
