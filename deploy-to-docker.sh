#!/bin/bash

if [ -z "$1" ]; then
   echo "env is empty, so setting targetEnv to development (default)"
   targetEnv="dev"
else
   echo "env is NOT empty, so setting targetEnv to $1"
   targetEnv=$1
fi

clear
DEBUG=false

cp devops/docker/Dockerfile .

echo "Running docker-compose up -d."

docker-compose down

if [ $targetEnv == "docker" ] || [ $targetEnv == "int2" ]; then
    sed -i -e 's/localhost:8080/int2-my.workstation.co.uk/g' .env
fi

docker-compose up -d --build
docker-compose ps

DOCKER_CONTAINER_NAME="webimpetus-dev"
DOCKER_QA_CONTAINER_NAME="webimpetus-cypress"

docker cp bootstrap-openresty-dev.sh ${DOCKER_CONTAINER_NAME}:/usr/local/bin/bootstrap-openresty.sh
docker exec -it ${DOCKER_CONTAINER_NAME} chmod +x /usr/local/bin/bootstrap-openresty.sh
docker exec -it ${DOCKER_CONTAINER_NAME} bash /usr/local/bin/bootstrap-openresty.sh
# docker exec -it ${DOCKER_CONTAINER_NAME} composer update
# docker exec -it ${DOCKER_CONTAINER_NAME} composer install

rm Dockerfile

HOST_ENDPOINT_UNSECURE_URL="http://localhost:8080/dashboard"
HOST_ENDPOINT_SECURE_URL="https://localhost:9093/dashboard"

if [ $targetEnv == "int2" ]; then
HOST_ENDPOINT_SECURE_URL="https://int2-my.workstation.co.uk"
fi

curl -I $HOST_ENDPOINT_UNSECURE_URL
curl -I $HOST_ENDPOINT_SECURE_URL

os_type=$(uname -s)

if [ "$os_type" = "Darwin" ]; then
open $HOST_ENDPOINT_UNSECURE_URL
open $HOST_ENDPOINT_SECURE_URL
fi

if [ "$os_type" = "Linux" ]; then
xdg-open $HOST_ENDPOINT_UNSECURE_URL
fi

if [ $DEBUG==true ]; then
    docker ps -a
    echo "DEBUG is true, so running docker exec -it ${DOCKER_CONTAINER_NAME} bash"
if [ $targetEnv == "dev" ]; then
    docker exec -it ${DOCKER_CONTAINER_NAME} bash
fi

fi

# docker container restart ${DOCKER_QA_CONTAINER_NAME}
# docker exec -it ${DOCKER_QA_CONTAINER_NAME} 'sleep 300'
sleep 120
# docker exec -it ${DOCKER_QA_CONTAINER_NAME} 'npx cypress run'
# --browser chrome --headless --spec "cypress/integration/opsapi-login-jwt-token.spec.js"'
# docker container restart ${DOCKER_QA_CONTAINER_NAME} && docker exec -it ${DOCKER_QA_CONTAINER_NAME} npx cypress run


