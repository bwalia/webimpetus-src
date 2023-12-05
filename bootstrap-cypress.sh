#!/bin/bash

set -x

USERNAME="$1"
PASSWD="$2"

echo "" > env_cypress
echo "CYPRESS_login_username=$USERNAME" >> env_cypress
echo "CYPRESS_login_passwd=$PASSWD" >> env_cypress
echo "" >> env_cypress
mv env_cypress /tmp/.env_cypress
mv docker-compose-cypress.yml /tmp/docker-compose-cypress.yml
mv cypress /tmp/cypress
rm -Rf *env*
mv /tmp/.env_cypress .env_cypress
mv /tmp/docker-compose-cypress.yml docker-compose-cypress.yml
mv /tmp/cypress cypress
ls -al
docker compose -f docker-compose-cypress.yml up
