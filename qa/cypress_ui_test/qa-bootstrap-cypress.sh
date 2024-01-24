#!/bin/bash

set -x

USERNAME="$1"
PASSWD="$2"
TARGET_ENV="$3"

rm -Rf .env
echo "" > env_cypress
echo "CYPRESS_login_username=$USERNAME" >> env_cypress
echo "CYPRESS_login_password=$PASSWD" >> env_cypress
echo "CYPRESS_TARGET_ENV=$TARGET_ENV" >> env_cypress
echo "" >> env_cypress
mv env_cypress /tmp/.env_cypress
mv qa-docker-compose-cypress.yml /tmp/qa-docker-compose-cypress.yml
mv cypress /tmp/cypress
mv /tmp/.env_cypress .env
mv /tmp/qa-docker-compose-cypress.yml qa-docker-compose-cypress.yml
mv /tmp/cypress cypress
ls -al
docker compose -f qa-docker-compose-cypress.yml up cypress-chrome
docker compose -f qa-docker-compose-cypress.yml up cypress-firefox
docker compose -f qa-docker-compose-cypress.yml up cypress-electron