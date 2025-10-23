#!/bin/bash

# This bash script copies files into the workerra-ci CI4 docker container for docker based dev environment.

#  set -x

clear

CONTAINER_NAME=workerra-ci-dev

TARGET_DIR=/var/www/html/
NGINX_SERVER_CONFIG=/etc/nginx/sites-enabled/workerra-ci.conf

docker exec -it ${CONTAINER_NAME} truncate -s 0 ${TARGET_DIR}.env

docker cp config/vhosts/workerra-ci.conf ${CONTAINER_NAME}:${NGINX_SERVER_CONFIG}

BOOTSTRAP_FILE=bootstrap_openresty_dev.sh

#docker cp nginx-dev.conf ${CONTAINER_NAME}:/usr/local/openresty/nginx/conf/nginx.conf
docker cp ci4/. ${CONTAINER_NAME}:${TARGET_DIR}
docker cp ${BOOTSTRAP_FILE} ${CONTAINER_NAME}:${TARGET_DIR}

docker exec -it ${CONTAINER_NAME} chmod +x ${TARGET_DIR}${BOOTSTRAP_FILE}
docker exec -it ${CONTAINER_NAME} bash ${TARGET_DIR}${BOOTSTRAP_FILE}

BOOTSTRAP_FILE=bootstrap_mariadb_dev.sh

FILE=$HOME/.${BOOTSTRAP_FILE}
        
if [ -f "$FILE" ];then
    echo "File $FILE exists."
    cp ${FILE} .
    mv .${BOOTSTRAP_FILE} ${BOOTSTRAP_FILE}
    docker cp ${BOOTSTRAP_FILE} ${CONTAINER_NAME}:${TARGET_DIR}
    rm -Rf ${BOOTSTRAP_FILE}

    docker exec -it ${CONTAINER_NAME} chmod +x ${TARGET_DIR}${BOOTSTRAP_FILE}
    docker exec -it ${CONTAINER_NAME} bash ${TARGET_DIR}${BOOTSTRAP_FILE}
else
    echo "File $FILE does not exist."
    exit 1
fi

docker exec -it ${CONTAINER_NAME} openresty -t 
docker exec -it ${CONTAINER_NAME} openresty -s reload

