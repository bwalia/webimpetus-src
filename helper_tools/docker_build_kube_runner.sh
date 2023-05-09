#!/bin/bash

# This build script is used to build the bootstrap_kubernetes_client.sh docker image
# which can be used to bootstrap the kubernetes client into github actions and various other kubernetes jobs

# set -x

if [[ -z "$1" ]]; then
   echo "BUILD_IMAGE_TAG is empty, so setting BUILD_IMAGE_TAG to latest (default)"
    BUILD_IMAGE_TAG=latest
else
   echo "BUILD_IMAGE_TAG is provided, so setting BUILD_IMAGE_TAG to $1"
    BUILD_IMAGE_TAG=$1
fi

DOCKER_REGISTRY=registry.workstation.co.uk
TARGET_STACK=kube-runner

BUILD_IMAGE_TOOL=docker

${BUILD_IMAGE_TOOL} build -f devops/docker/Dockerfile-kubectl-runner --build-arg ALPINE_VERSION=3.17 -t ${TARGET_STACK} . --no-cache
${BUILD_IMAGE_TOOL} tag ${TARGET_STACK} ${DOCKER_REGISTRY}/${TARGET_STACK}:${BUILD_IMAGE_TAG}
${BUILD_IMAGE_TOOL} push ${DOCKER_REGISTRY}/${TARGET_STACK}:${BUILD_IMAGE_TAG}
