#!/bin/bash

clear
cp devops/docker/Dockerfile .

echo "Running docker-compose up -d."

docker compose down
docker compose up -d --build

DOCKER_CONTAINER_NAME="webimpetus-dev"

docker exec -it ${DOCKER_CONTAINER_NAME} composer update
docker exec -it ${DOCKER_CONTAINER_NAME} composer install

rm Dockerfile