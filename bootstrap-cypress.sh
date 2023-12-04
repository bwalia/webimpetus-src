#!/bin/bash

set -x

USERNAME="$1"
PASSWD="$2"

echo "" > .env_cypress
echo "CYPRESS_login_username=$USERNAME" >> .env_cypress
echo "CYPRESS_login_passwd=$PASSWD" >> .env_cypress
echo "" >> .env_cypress
docker-compose -f docker-compose-cypress.yml up -d
