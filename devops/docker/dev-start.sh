#!/usr/bin/env bash
# -- Author: Balinder Walia --
# -- Pushes the docker image : webimpetus into AWS ECR / or optionally into a given Docker registry --
# -- $1 is mandatory image version, for example : latest --

set -x

cp ~/webimpetus.sql devops/init/01.sql
cp ~/.webimpetus.env env

docker build -t localuser/webimpetus-ci4 -f devops/docker/Dockerfile .

docker-compose -f devops/docker-compose.yaml down
docker-compose -f devops/docker-compose.yaml up -d --build --remove-orphans

#sleep 600
#rm -Rf devops/init/01.sql
#rm -Rf /Users/jack/www/webimpetus/env