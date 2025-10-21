#!/bin/bash

# This bash script build workerra-ci CI4 docker image.
# as kubernetes deployment into dev,test or prod environment using k3s.

#  set -x
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[0;33m'
if ! docker info > /dev/null 2>&1; then
  echo -e "$RED This bash script uses docker, and it isn't running - please make sure docker is running and try again!!!"
  exit 1
fi

DATE_GEN_VERSION=$(date +"%Y%m%d%I%M%S")

IMAGE_NAME="workerra-ci"
IMAGE_TAG="dev"
IMAGE_REPO="registry.workstation.co.uk"
TARGET_NAMESPACE="dev"

HTTP_SERVER_TYPE="openresty"

build_process="enabled"
#build_process="disabled"

if [ -z "$1" ]; then
   echo "$YELLOW env is empty, so setting targetEnv to development (default)"
   targetEnv="dev"
else
   echo "env is NOT empty, so setting targetEnv to $1"
   targetEnv=$1
fi

if [ -z "$2" ]; then
   echo "$YELLOW TARGET_NAMESPACE is empty, so setting it to $TARGET_NAMESPACE (default value is: dev)"
else
   echo "TARGET_NAMESPACE is provided, so using it $TARGET_NAMESPACE"
   TARGET_NAMESPACE=$2
fi

if [ -z "$3" ]; then
   echo "$YELLOW IMAGE_TAG is empty, so setting it to $IMAGE_TAG (default value is: latest)"
else
   echo "IMAGE_TAG is provided, so using it $IMAGE_TAG"
   IMAGE_TAG=$3
fi

if [ -z "$4" ]; then
echo "$YELLOW Docker build cmd is set default (docker, nerdctl etc.)"
BUILD_IMAGE_TOOL="docker"
else
echo "BUILD_IMAGE_TOOL is provided, so setting BUILD_IMAGE_TOOL $4"
BUILD_IMAGE_TOOL=$4
fi

if [ -z "$4" ]; then
echo "$YELLOW next_step is empty, so setting action to default (install)"
next_step="install"
else
echo "next_step is provided, so setting action to $4"
next_step=$4
fi

if [ $targetEnv == "dev" ] || [ $targetEnv == "dev-bwalia" ]; then
   if [ $IMAGE_TAG == "" ]; then
   IMAGE_TAG=$targetEnv
      echo "$GREEN IMAGE_TAG is set to $targetEnv"
   else
      echo "$YELLOW IMAGE_TAG is $IMAGE_TAG"
   fi
fi

if [ $build_process == "disabled" ]; then
   echo "Temporary added to disable image build process"
else
   ${BUILD_IMAGE_TOOL} build -f devops/docker/Dockerfile --build-arg BASE_TAG=latest -t ${IMAGE_NAME} . --no-cache
   ${BUILD_IMAGE_TOOL} tag ${IMAGE_NAME} ${IMAGE_REPO}/${IMAGE_NAME}:${IMAGE_TAG}
   ${BUILD_IMAGE_TOOL} push  ${IMAGE_REPO}/${IMAGE_NAME}:${IMAGE_TAG}

# Push to docker public registry as well
${BUILD_IMAGE_TOOL} tag ${IMAGE_NAME} bwalia/workerra-ci:latest && ${BUILD_IMAGE_TOOL} push bwalia/workerra-ci:latest   
fi

if [ $next_step == "install" ]; then
   ./install.sh $targetEnv $TARGET_NAMESPACE $IMAGE_TAG
   echo "$GREEN The $targetEnv installation in $TARGET_NAMESPACE namespace is done!"
   exit 0
else
   echo "$GREEN Done!"
   exit 0
fi
