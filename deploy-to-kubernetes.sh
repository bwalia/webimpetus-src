#!/bin/bash

# This bash script deploys the WebImpetus CI4 docker image to the target kubernetes cluster.
# as kubernetes deployment into dev,test or prod environment using k3s.

#  set -x

if [ -z "$1" ]; then
   echo "env is empty, so setting targetEnv to development (default)"
   targetEnv="dev"
else
   echo "env is NOT empty, so setting targetEnv to $1"
   targetEnv=$1
fi

if [ -z "$2" ]; then
   echo "cluster name is empty, so setting it to default (dev)"
   clusterName="k3s0"
else
   echo "cluster name is provided, so setting it to $2"
   clusterName=$2
fi

if [ -z "$3" ]; then
   echo "targetNs is empty, so setting it to default (dev)"
   targetNs="dev"
else
   echo "targetNs is provided, so setting it to $3"
   targetNs=$3
fi

IMAGE_NAME="webimpetus"
IMAGE_TAG="latest"
IMAGE_REPO="docker.workstation.co.uk"

echo Target Environment: $targetEnv

if [ $targetEnv == "dev" ] || [ $targetEnv == "test" ] || [ $targetEnv == "int" ] || [ $targetEnv == "acc" ] || [ $targetEnv == "prod" ]; then
if [ $clusterName == "k3s2" ] || $clusterName == "k3s3" ] || [ $clusterName == "k3s6" ] || [ $clusterName == "k3s10" ]; then
export KUBECONFIG=~/.kube/vpn-$clusterName.yaml
   helm upgrade -i wsl-$targetEnv ./devops/webimpetus-chart -f devops/webimpetus-chart/values-$targetEnv-$clusterName.yaml --set-string targetImage="bwalia/$IMAGE_NAME" --set-string targetImageTag="$IMAGE_TAG" --namespace $targetNs --create-namespace
   kubectl rollout restart deployment/wsl-$targetEnv -n $targetNs
   kubectl rollout history deployment/wsl-$targetEnv -n $targetNs
else
echo "Target cluster is not supported"
fi
else
echo "Target env is not supported"
fi