        #!/bin/bash
        set -x
        # Set Timezone
        #FILE=/usr/local/etc/php-fpm.d/www.conf
        #echo "date.timezone = '{{ .Values.envPreferences.timeZone }}'" >> $FILE
        FILE=/etc/resolv.conf
        echo "Workstation On Starting Pod Bootstrap Script"
            # Set DNS to cloudflare Name servers for faster DNS resolution
        echo "nameserver 1.1.1.1" >> $FILE
        echo "nameserver 1.0.0.1" >> $FILE
        CLOUDFLARE_DNS_1="1.1.1.1"
        if grep -rnw $FILE -e $CLOUDFLARE_DNS_1; then
            echo "Cloudflare DNS already set"
        else
            echo "nameserver $CLOUDFLARE_DNS_1" >> $FILE
            echo "nameserver $CLOUDFLARE_DNS_2" >> $FILE
        fi

        echo "Workstation Nginx Bootstrap Script"
        export COMPOSER_ALLOW_SUPERUSER=1
        yes | composer update
        echo "==========================="
        FILE=/src/
        if [ -d "$FILE" ]; then
        FILE=/var/www/html/
        cp -r /src/* $FILE
        if [ -d "$FILE" ]; then
        chmod 755 -R $FILE
        chown www-data:root -R $FILE

        SUB_DIR=$FILE"writable/"
        if [ ! -d "$SUB_DIR" ];then
        mkdir -p $SUB_DIR
        chmod 777 -R ${SUB_DIR}
        fi
        SUB_DIR=$FILE"writable/"
        SUB_DIR=$SUB_DIR"cache/"
        if [ ! -d "$SUB_DIR" ];then
        mkdir -p $SUB_DIR
        chmod 777 -R ${SUB_DIR}
        fi
        SUB_DIR=$FILE"writable/"
        SUB_DIR=$SUB_DIR"session/"
        if [ ! -d "$SUB_DIR" ];then
        mkdir -p $SUB_DIR
        chmod 777 -R ${SUB_DIR}
        fi
        SUB_DIR=$FILE"writable/"
        SUB_DIR=$SUB_DIR"session/"
        if [ ! -d "$SUB_DIR" ];then
        mkdir -p $SUB_DIR
        chmod 777 -R ${SUB_DIR}/
        fi
        
        cp /tmp/secrets/.env $FILE.env
        FILE=/var/www/html/.env
        
        if [ -f "$FILE" ];then
        if grep -rnw $FILE -e "#----WEBIMPETUS-SYSTEM-INFO----"
           then
            echo "#----WEBIMPETUS-SYSTEM-INFO---- Found"
        else
            echo "#----WEBIMPETUS-SYSTEM-INFO---- Not Found, Adding"
            echo "#----WEBIMPETUS-SYSTEM-INFO----" >> $FILE
        fi
            echo "$FILE exists."
            echo "==========================="
            echo "Workstation Bootstrap Script Copied"
            echo "==========================="
        fi
            echo "Starting Workstation"
            echo "==========================="
            php -v
        echo "==========================="
        #   sed '/"#----WEBIMPETUS-SYSTEM-INFO----"/q' $FILE
        echo "Workstation Src copy to /var/www/html Complete"
        fi
        fi
        
        FILE=/var/www/html/writable/
        if [ -d "$FILE" ]; then
            echo "$FILE exists."
            chmod 777 -R $FILE
        fi
        FILE=/var/www/html/writable/cache/
        if [ -d "$FILE" ]; then
            echo "$FILE exists."
            chmod 777 -R $FILE
        fi
        FILE=/var/www/html/writable/session/
        if [ -d "$FILE" ]; then
            echo "$FILE exists."
            chmod 777 -R $FILE
        fi
        declare -a webimpetus_array # declare the array                                                                                                                                                                  
        # Read each line and use regex parsing (with Bash's `=~` operator)
        # to extract the value.
        while read -r line; do
        # Extract the value from between the double quotes
        # and add it to the array.
        [[ $line =~ :[[:blank:]]+\"(.*)\" ]] && webimpetus_array+=( "${BASH_REMATCH[1]}" )
        done </var/www/html/webimpetus.json                                                                                                                                 

        #declare -p webimpetus_array # print the array
        for i in ${!webimpetus_array[@]}; do
        if [ $i -eq "1" ]; then
        APP_FULL_VERSION_NO="${webimpetus_array[$i]}"
        export APP_FULL_VERSION_NO="${webimpetus_array[$i]}"
        fi
        if [ $i -eq "2" ]; then
        APP_FULL_BUILD_NO="${webimpetus_array[$i]}"
        export APP_FULL_BUILD_NO="${webimpetus_array[$i]}"
        fi
        done
        echo $APP_FULL_VERSION_NO
        echo $APP_FULL_BUILD_NO
        FILE=/var/www/html/.env
        echo "APP_FULL_VERSION_NO='$APP_FULL_VERSION_NO'" >> $FILE
        echo "APP_FULL_BUILD_NO='$APP_FULL_BUILD_NO'" >> $FILE
        export APP_FULL_VERSION_NO=$APP_FULL_VERSION_NO
        export APP_FULL_BUILD_NO=$APP_FULL_BUILD_NO
        APP_RELEASE_NOTES_DOC_URL="https://webimpetus.cloud/docs/"
        export APP_RELEASE_NOTES_DOC_URL=$APP_RELEASE_NOTES_DOC_URL
        DATE_GEN_VERSION=$(date +"%Y%m%d%I%M%S")
        export DATE_GEN_VERSION=$(date +"%Y%m%d%I%M%S")
        export APP_DEPLOYED_AT=$DATE_GEN_VERSION
        echo "APP_DEPLOYED_AT='$DATE_GEN_VERSION'" >> $FILE
        echo "APP_ENVIRONMENT='dev'" >> $FILE
        export APP_ENVIRONMENT="'dev'"
        echo "APP_TARGET_CLUSTER='docker'" >> $FILE
        export APP_TARGET_CLUSTER="docker'"
        echo "APP_RELEASE_NOTES_DOC_URL='$APP_RELEASE_NOTES_DOC_URL'" >> $FILE
        echo DYNAMIC_SCRIPTS_PATH=/tmp >> $FILE
        echo "==========================="
        echo "Copy openresty config file for Workstation"
        cp /tmp/configmap/workstation.conf /etc/nginx/sites-enabled/workstation.conf
        echo "==========================="
        echo "Restart openresty nginx"
        openresty -s reload
        php spark migrate
        echo "==========================="
        echo "Workstation Nginx Bootstrap Script Completed"      
