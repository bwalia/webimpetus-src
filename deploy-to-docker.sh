#!/bin/bash
# Date: 2024 03 05 7:50AM
# Docker dev env setup script
# Usage: ./deploy-to-docker.sh [env]
# Example: ./deploy-to-docker.sh dev
# Note: Do not run this against Kubernetes cluster

if [ -z "$1" ]; then
   echo "env is empty, so setting targetEnv to development (default)"
   targetEnv="dev"
else
   echo "env is NOT empty, so setting targetEnv to $1"
   targetEnv=$1
fi

if [ -z "$2" ]; then
   echo "dev machine name is empty, so setting machineName to default"
   machineName=$(whoami)
else
   echo "machineName is NOT empty, so setting machineName to $2"
   machineName=$2
fi

clear
DEBUG=false

cp devops/docker/Dockerfile .

echo "Running docker-compose up -d."

docker-compose down

if [ $targetEnv == "docker" ] || [ $targetEnv == "balinderwalia" ]; then
    sed -i -e 's/localhost:8080/dev-bsw-my.workstation.co.uk/g' .env
else
    rm -Rf .env
    cp .env.$machineName .env
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

        SRC_ENV_FILE=$(pwd)/.env
        if [ -f "$SRC_ENV_FILE" ];then
            FILE=$(pwd)/.env.dev
            cp $SRC_ENV_FILE $FILE
            
                if [ -f "$FILE" ];then
                    echo "$FILE exists."
                    awk '/----WEBIMPETUS-SYSTEM-INFO----/{exit} 1' $FILE > $SRC_ENV_FILE

                    echo "#----WEBIMPETUS-SYSTEM-INFO----" >> $SRC_ENV_FILE

                    echo "==========================="
                    echo "Workstation Bootstrap Script Copied"
                    echo "==========================="
                fi
                echo "Starting Workstation"
                echo "==========================="
            echo "==========================="
            #   sed '/"#----WEBIMPETUS-SYSTEM-INFO----"/q' $FILE
            echo "Workstation Src copy to /var/www/html Complete"
         fi

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


