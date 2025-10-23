#!/bin/bash

# set -x

TARGET_CLUSTER_KUBECONFIG=~/.kube/k3s1.yaml
TARGET_CLUSTER_KUBECONFIG=~/.kube/k3s2.yaml
TARGET_CLUSTER_KUBECONFIG=~/.kube/k3s3.yaml

BASH_FILE_TO_RUN=/Users/balinderwalia/Documents/Work/Bash_Scripts/test-bash.sh

DOCKER_CONRAINER_NAME=kube-runner-workstation

docker container stop $DOCKER_CONRAINER_NAME
docker container rm $DOCKER_CONRAINER_NAME

docker run --name $DOCKER_CONRAINER_NAME -v $(pwd)/devops/workerra-ci-chart:/helm-charts/workerra-ci-chart \
--env KUBECONFIG_BASE64=$(cat $TARGET_CLUSTER_KUBECONFIG | base64) \
--env RUN_BASH_BASE64=$(cat $BASH_FILE_TO_RUN | base64) \
registry.workstation.co.uk/kube-runner:stable
