#!/bin/bash

############ This bash script deploys WebImpetus CI4 project (mariadb, php_lamp, phpmyadmin)
############ as docker container into dev,test or prod environment using docker compose files.

#set -x

if [[ -z "$1" ]]; then
   echo "env is empty, so setting targetEnv to development (default)"
   targetEnv="dev"
else
   echo "env is NOT empty, so setting targetEnv to $1"
   targetEnv=$1
fi

if [[ -z "$2" ]]; then
   echo "action is empty, so setting action to start (default)"
   deployment_stage="start"
else
   echo "action is NOT empty, so setting action to start (default)"
   deployment_stage=$2
fi

if [[ "$targetEnv" == "dev" || "$targetEnv" == "test" || "$targetEnv" == "prod" ]]; then
echo "The targetEnv is $targetEnv supported by this script"
else
echo "Oops! The targetEnv is $targetEnv is not supported by this script, check the README.md and try again! (Hint: Try default value is dev)"
exit 1
fi

###### Set some variables
HOST_ENDPOINT_UNSECURE_URL="http://localhost:8078"

##### Set some variables
if [[ "$targetEnv" == "dev" ]]; then
WORKSPACE_DIR=$(pwd)
fi

if [[ "$targetEnv" == "test" || "$targetEnv" == "prod" ]]; then
WORKSPACE_DIR="/tmp/webimpetus/${targetEnv}"
mkdir -p ${WORKSPACE_DIR}
chmod 777 ${WORKSPACE_DIR}
rm -rf ${WORKSPACE_DIR}/*
cp -r ../webimpetus/* ${WORKSPACE_DIR}/
fi

if [[ "$targetEnv" == "dev" ]]; then
echo "No need to move dev env files"
else
mv ${WORKSPACE_DIR}/${targetEnv}.env ${WORKSPACE_DIR}/.env
fi
cd ${WORKSPACE_DIR}/

if [[ "$deployment_stage" == "stop" ]]; then
docker-compose -f "${WORKSPACE_DIR}/docker-compose.yml" down
fi

if [[ "$deployment_stage" == "start" ]]; then
docker-compose -f "${WORKSPACE_DIR}/docker-compose.yml" down
docker-compose -f "${WORKSPACE_DIR}/docker-compose.yml" up -d --build
docker-compose -f "${WORKSPACE_DIR}/docker-compose.yml" ps
fi

if [[ "$deployment_stage" == "start" ]]; then
chmod +x reset_containers.sh
/bin/bash reset_containers.sh $targetEnv
fi

if [[ "$targetEnv" == "dev" && "$deployment_stage" == "start" ]]; then

sleep 2

curl -IL $HOST_ENDPOINT_UNSECURE_URL
echo "Open Host endpoint..."

os_type=$(uname -s)

if [[ "$os_type" == "Darwin" ]]; then
open $HOST_ENDPOINT_UNSECURE_URL
fi

if [[ "$os_type" == "Linux" ]]; then
xdg-open $HOST_ENDPOINT_UNSECURE_URL
fi

fi











# cp -r ../webimpetus/* /tmp/$workdirname_file
# mv /tmp/$workdirname_file/dev.env /tmp/$workdirname_file/.env
# docker-compose -f /tmp/$workdirname_file/docker-compose.yml down
# # docker-compose build
# docker-compose -f /tmp/$workdirname_file/docker-compose.yml up -d --build
# docker-compose -f /tmp/$workdirname_file/docker-compose.yml ps
# mv /tmp/$workdirname_file/prepare_workspace_env.sh .
#mv /tmp/prepare_workspace_env.sh .
# sleep 30
# #./reset_env.sh
# sudo -S rm -Rf ci4/
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/data
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/config
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/