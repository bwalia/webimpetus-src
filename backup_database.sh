        #!/bin/bash
        if [ -z "$BACKUP_FREQUENCY" ]; then
            echo "BACKUP_FREQUENCY set to hourly."
            BACKUP_FREQUENCY="hourly"
        fi
        # Temporary Directory is readonly so use /var/www/html/writeable
        TMP_DIR="/var/www/html/writeable/backups"
        mkdir -p $TMP_DIR
        chmod 777 -R $TMP_DIR
        # Timestamp for the dump file
        TIMESTAMP=$(date +%Y%m%d%H%M%S)
        # MySQL Dump File Name
        DUMP_FILE="$TMP_DIR/wsl_{{ .Values.targetEnv }}_db_dump_$TIMESTAMP.sql"
        DUMP_FILE_TAR="$TMP_DIR/wsl_{{ .Values.targetEnv }}_db_dump_$TIMESTAMP.tar.gz"
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
        # dos2unix Removes CTRL-M (^M) blue carriage return characters from a file in Linux.
        dos2unix $FILE
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
        source $FILE
        fi
        # Create MySQL Dump
        mysqldump -h $DB_HOST "--user=$DB_USER" "--password=$DB_PASSWORD" $DB_NAME > $DUMP_FILE
        if [ $? -eq 0 ]; then
            echo "MySQL dump created successfully: $DUMP_FILE"
        else
            echo "Error creating MySQL dump."
            exit 1
        fi
        if [ -f $DUMP_FILE ]; then
        # Upload Dump File to Minio Bucket
        mc config host add myminio $MINIO_HOST $MINIO_ACCESS_KEY $MINIO_SECRET_KEY
        ls -alh $DUMP_FILE
        file $DUMP_FILE
        stat $DUMP_FILE
        sleep 10
        tar -czvf $DUMP_FILE_TAR $TMP_DIR || [[ $? -eq 1 ]]

        if [ $? -eq 0 ]; then
            echo "Tar file created successfully: $DUMP_FILE_TAR"

        if [ -f $DUMP_FILE_TAR ]; then
                echo "Dump file compressed successfully: $DUMP_FILE_TAR"
        else
                echo "Error compressing dump file."
                exit 0
        fi
        ls -alh $DUMP_FILE_TAR
        file $DUMP_FILE_TAR
        stat $DUMP_FILE_TAR
        echo "Uploading dump file to Minio bucket: $MINIO_BUCKET/"
        mc cp $DUMP_FILE_TAR myminio/$MINIO_BUCKET/$BACKUP_FREQUENCY/
        if [ $? -eq 0 ]; then
        echo "Dump file uploaded to Minio bucket: $MINIO_BUCKET"
        mv $DUMP_FILE_TAR $TMP_DIR/wsl_{{ .Values.targetEnv }}_db_dump_latest.tar.gz
        $DUMP_FILE_TAR = $TMP_DIR/wsl_{{ .Values.targetEnv }}_db_dump_latest.tar.gz
        if [ -f $DUMP_FILE_TAR ]; then
            echo "Dump file renamed to db_dump_latest.tar.gz"
            mc cp $DUMP_FILE_TAR myminio/$MINIO_BUCKET/hourly/                # Copy the latest dump to hourly folder
        else
            echo "Error renaming dump file db_dump_latest.tar.gz."
        fi

        else
            echo "Error uploading dump file to Minio bucket."
            exit 1
        fi
        # Clean up temporary dump file
        rm $DUMP_FILE
        rm $DUMP_FILE_TAR
        echo "DB backup $DUMP_FILE_TAR completed."
        else
            echo "Error creating tar file. $DUMP_FILE_TAR"
            exit 1
        fi
        else
                echo "Error creating mysql dump file. Backup failed."
                exit 0
        fi