#!/bin/bash

#set -x

if [[ -z "$1" ]]; then
   echo "TARGET_ENV is empty, so setting TARGET_ENV to prod (default)"
   TARGET_ENV="prod"
else
   echo "TARGET_ENV is provided, so setting TARGET_ENV to $1"
   TARGET_ENV=$1
fi

if [[ -z "$2" ]]; then
   echo "CLUSTER_NAME is empty, so setting CLUSTER_NAME to k3s1 (default)"
   CLUSTER_NAME="k3s1"
else
   echo "CLUSTER_NAME is provided, so setting CLUSTER_NAME to $2"
   CLUSTER_NAME=$2
fi

if [[ -z "$3" ]]; then
   echo "IMAGE_REGISTRY is empty, so setting IMAGE_REGISTRY to docker.io (default)"
   IMAGE_REGISTRY="docker.io"
else
   echo "IMAGE_REGISTRY is provided, IMAGE_REGISTRY is set to $3"
   IMAGE_REGISTRY=$3
fi

if [[ -z "$4" ]]; then
   echo "DOCKER_IMAGE is empty, so setting DOCKER_IMAGE to workerra-ci (default)"
   DOCKER_IMAGE="workerra-ci"
else
   echo "DOCKER_IMAGE is provided, DOCKER_IMAGE is set to $4"
   DOCKER_IMAGE=$4
fi

if [[ -z "$5" ]]; then
   echo "TARGET_CLUSTER_KUBECONFIG is empty, so setting TARGET_CLUSTER_KUBECONFIG to empty (default)"
   TARGET_CLUSTER_KUBECONFIG=""
else
   echo "TARGET_CLUSTER_KUBECONFIG is provided, TARGET_CLUSTER_KUBECONFIG is set to $5"
   TARGET_CLUSTER_KUBECONFIG=$5
fi

BASH_FILE_TO_RUN=devops/scripts/kube_runner_deploy_env.sh

DOCKER_CONRAINER_NAME=kube-runner-workstation

echo $TARGET_ENV
echo $CLUSTER_NAME
echo $IMAGE_REGISTRY
echo $DOCKER_IMAGE
echo $TARGET_CLUSTER_KUBECONFIG
echo $BASH_FILE_TO_RUN


# if [[ $TRIGGER_ENV == "dev" ]]; then
# docker container stop $DOCKER_CONRAINER_NAME
# docker container rm $DOCKER_CONRAINER_NAME
# fi

# docker run --name $DOCKER_CONRAINER_NAME -v $(pwd)/devops/workerra-ci-chart:/helm-charts/workerra-ci-chart \
# --env TARGET_ENV=$TARGET_ENV \
# --env CLUSTER_NAME=$CLUSTER_NAME \
# --env IMAGE_REGISTRY=$IMAGE_REGISTRY \
# --env DOCKER_IMAGE=$DOCKER_IMAGE \
# --env KUBECONFIG_BASE64=$TARGET_CLUSTER_KUBECONFIG \
# --env RUN_BASH_BASE64=$(cat $BASH_FILE_TO_RUN | base64) \
# registry.workstation.co.uk/kube-runner:latest