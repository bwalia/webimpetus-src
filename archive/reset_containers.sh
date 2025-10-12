#!/bin/bash
# This deploys CI4 project (mariadb, php_lamp, phpmyadmin) in docker container to test environment using docker compose.

set -x

if [[ -z "$1" ]]; then
   echo "env is empty, so setting targetEnv to development (default)"
   targetEnv="dev"
else
   echo "env is NOT empty, so setting targetEnv to $1"
   targetEnv=$1
fi

sleep 1

docker exec ${targetEnv}-wslphp74 composer update
docker exec ${targetEnv}-wslphp74 chown -R www-data:www-data /var/www/html/writable/

DATE_GEN_VERSION=$(date +"%Y%m%d%I%M%S")
export DATE_GEN_VERSION=$(date +"%Y%m%d%I%M%S")

if [[ "$targetEnv" == "dev" ]]; then

APP_RELEASE_NOTES_DOC_URL="https://webimpetus.dev/docs/app_release_notes"

fi

if [[ "$targetEnv" == "test" ]]; then

APP_RELEASE_NOTES_DOC_URL="https://test.webimpetus.dev/docs/app_release_notes"

fi

if [[ "$targetEnv" == "prod" ]]; then

APP_RELEASE_NOTES_DOC_URL="https://webaimpetus.com/docs/"

fi

export APP_RELEASE_NOTES_DOC_URL=$APP_RELEASE_NOTES_DOC_URL

echo "App environment: $targetEnv"
echo "Date generated version: $DATE_GEN_VERSION"
echo "App release notes doc url is $APP_RELEASE_NOTES_DOC_URL"

mkdir -p /tmp/${targetEnv}
chmod 777 -R /tmp/${targetEnv}
touch /tmp/${targetEnv}.env
truncate -s 0 /tmp/${targetEnv}.env

#  DEV   
if [[ "$targetEnv" == "dev" ]]; then
cp ${HOME}/env_webimpetus_myworkstation /tmp/${targetEnv}.env
else   
cp /home/bwalia/env_webimpetus_${targetEnv}_myworkstation /tmp/${targetEnv}.env
fi

echo APP_DEPLOYED_AT=$DATE_GEN_VERSION >> /tmp/${targetEnv}.env
echo APP_ENVIRONMENT=$targetEnv >> /tmp/${targetEnv}.env
echo APP_RELEASE_NOTES_DOC_URL=$APP_RELEASE_NOTES_DOC_URL >> /tmp/${targetEnv}.env
echo DYNAMIC_SCRIPTS_PATH=/tmp >> /tmp/${targetEnv}.env
docker cp /tmp/${targetEnv}.env ${targetEnv}-wslphp74:/var/www/html/.env

if [[ "$targetEnv" == "dev" ]]; then
# What OS are you using?
docker exec ${targetEnv}-wslphp74 cat /etc/os-release
docker exec ${targetEnv}-wslphp74 apt update 
docker exec ${targetEnv}-wslphp74 apt upgrade
docker exec ${targetEnv}-wslphp74 apt install git vim -y
fi
