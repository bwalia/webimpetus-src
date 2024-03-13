#!/bin/bash

# Temporary Directory
TMP_DIR="/tmp"

# Timestamp for the dump file
TIMESTAMP=$(date +%Y%m%d%H%M%S)

# MySQL Dump File Name
DUMP_FILE="$TMP_DIR/db_dump_$TIMESTAMP.sql"
DUMP_FILE_TAR="$TMP_DIR/db_dump_$TIMESTAMP.tar.gz"
echo "$KUBE_CONFIG" | base64 -d > k3s2.yaml
ls -al
SRC_ENV_FILE=/tmp/secrets/.env
    if [ -f "$SRC_ENV_FILE" ];then
    FILE=/var/www/html/.env
    cp $SRC_ENV_FILE $FILE
fi

FILE=/var/www/html/.env
if [ ! -f $FILE ]
then
    echo "$FILE not found."
    exit 1
    else
#   export $(cat $FILE | xargs)
#   load all environment variables into current session
TEMP_FILE=/tmp/.env
sed -e's/amazons3.use_ssl/S3_USE_SSL/g' $FILE > $TEMP_FILE
sed -e's/amazons3.bucket/S3_BUCKET/g' $TEMP_FILE > $FILE
sed -e's/amazons3.region/S3_REGION/g' $FILE > $TEMP_FILE
sed -e's/amazons3.s3_directory/S3_DIRECTORY/g' $TEMP_FILE > $FILE
sed -e's/amazons3.verify_peer/S3_VERIFY_PEER/g' $FILE > $TEMP_FILE
sed -e's/amazons3.access_key/S3_ACCESS_KEY/g' $TEMP_FILE > $FILE
sed -e's/amazons3.secret_key/S3_SECRET/g' $FILE > $TEMP_FILE
sed -e's/amazons3.get_from_enviroment/S3_GET_FROM_ENV/g' $TEMP_FILE > $FILE
sed -e's/app.baseURL/BASE_URL/g' $FILE > $TEMP_FILE
sed -e's/database.default.hostname/DB_HOST/g' $TEMP_FILE > $FILE
sed -e's/database.default.username/DB_USER/g' $FILE > $TEMP_FILE
sed -e's/database.default.database/DB_NAME/g' $TEMP_FILE > $FILE
sed -e's/database.default.password/DB_PASSWORD/g' $FILE > $TEMP_FILE
sed -e's/database.default.DBDriver/DB_DRIVER/g' $TEMP_FILE > $FILE
source $FILE set
fi

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

echo "$DUMP_FILE completed."
