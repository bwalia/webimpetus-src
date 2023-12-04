#!/bin/bash

set -x

USERNAME="$1"
PASSWD="$2"

echo "" > .env.cypress
echo "CYPRESS_login_username='$USERNAME'" >> .env.cypress
echo "CYPRESS_login_passwd='$PASSWD'" >> .env.cypress
echo "" > .env.cypress
docker-compose -f docker-compose-cypress.yml up -d
