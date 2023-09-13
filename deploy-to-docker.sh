#!/bin/bash

clear
DEBUG=false

cp devops/docker/Dockerfile .

echo "Running docker-compose up -d."

docker-compose down
docker-compose up -d --build
docker-compose ps

DOCKER_CONTAINER_NAME="webimpetus-dev"

docker cp bootstrap-openresty-dev.sh ${DOCKER_CONTAINER_NAME}:/usr/local/bin/bootstrap-openresty.sh
docker exec -it ${DOCKER_CONTAINER_NAME} chmod +x /usr/local/bin/bootstrap-openresty.sh
docker exec -it ${DOCKER_CONTAINER_NAME} bash /usr/local/bin/bootstrap-openresty.sh
# docker exec -it ${DOCKER_CONTAINER_NAME} composer update
# docker exec -it ${DOCKER_CONTAINER_NAME} composer install

rm Dockerfile

HOST_ENDPOINT_UNSECURE_URL="http://localhost:8080"
curl -IL $HOST_ENDPOINT_UNSECURE_URL
os_type=$(uname -s)

if [ "$os_type" = "Darwin" ]; then
open $HOST_ENDPOINT_UNSECURE_URL
fi

if [ "$os_type" = "Linux" ]; then
xdg-open $HOST_ENDPOINT_UNSECURE_URL
fi

if [ $DEBUG==true ]; then
    docker ps -a
    echo "DEBUG is true, so running docker exec -it ${DOCKER_CONTAINER_NAME} bash"
    docker exec -it ${DOCKER_CONTAINER_NAME} bash
fi
