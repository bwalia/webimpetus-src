#!/bin/bash

set -x

USERNAME="$1"
PASSWD="$2"

echo "" > env_cypress
echo "CYPRESS_login_username=$USERNAME" >> env_cypress
echo "CYPRESS_login_passwd=$PASSWD" >> env_cypress
echo "" >> env_cypress
mv env_cypress .env_cypress
ls -al
docker compose -f docker-compose-cypress.yml up
