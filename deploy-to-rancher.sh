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
   echo "targetNs is empty, so setting it to default (dev)"
   targetNs="dev"
else
   echo "targetNs is provided, so setting it to $2"
   targetNs=$2
fi

if [ -z "$3" ]; then
   echo "IMAGE_TAG is empty, so setting it to default (latest)"
   IMAGE_TAG=""
else
   echo "IMAGE_TAG is provided, so setting it to $3"
   IMAGE_TAG=$3
fi

if [ -z "$4" ]; then
   echo "build_environment is empty, so setting it to default (empty)"
   build_environment="install"
else
   echo "build_environment is provided, so setting it to $4"
   build_environment=$4
fi

if [ $build_environment == "build" ] || [ $build_environment == "build_install" ]; then
./build.sh $targetEnv $targetEnv $deployment_tooling
echo "$build_environment is also requested"
fi

echo Target Environment: $targetEnv

if [ $targetEnv == "dev" ] || [ $targetEnv == "test" ] || [ $targetEnv == "int" ] || [ $targetEnv == "acc" ] || [ $targetEnv == "prod" ]; then
 bash helper_tools/helm_deploy_webimpetus.sh $targetEnv $targetEnv install $IMAGE_TAG
 echo "Helper tool helm deploy executed"
else
echo "Target env is not supported"
fi

