#!/bin/bash

# This bash script deploys WebImpetus CI4 project (mariadb, php_lamp, phpmyadmin)
# as kubernetes deployment into dev,test or prod environment using k3s.

#   set -x

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[0;33m'

SVC_HOST=localhost
SVC_NODEPORT=31178

DATE_GEN_VERSION=$(date +"%Y%m%d%I%M%S")

HTTP_SERVER_TYPE="openresty"
IMAGE_NAME="webimpetus"
TARGET_CLUSTER="k3s0"
IMAGE_TAG="latest"
IMAGE_REPO="bwalia"
TARGET_NAMESPACE="dev"

if [ -z "$1" ]; then
   echo -e "$GREEN env is empty, so setting targetEnv to development (default)"
   targetEnv="dev"
else
   echo -e "$RED env is provided, so setting targetEnv to $1"
   targetEnv=$1
fi

if [ -z "$2" ]; then
   echo -e "$RED namespace is empty, so setting namespace to dev (default)"
   TARGET_NAMESPACE=""
else
   echo "namespace is provided, so setting namespace to $2"
   TARGET_NAMESPACE=$2
fi

if [ -z "$3" ]; then
   echo -e "$RED action is empty, so setting action to install (default)"
   deployment_stage="install"
else
   echo "action is provided, action is set to $3"
   deployment_stage=$3
fi

if [ -z "$4" ]; then
   echo -e "$RED k3s IMAGE_TAG is empty, so setting IMAGE_TAG to latest (default)"
else
   echo "IMAGE_TAG is provided, IMAGE_TAG is set to $4"
   IMAGE_TAG=$4
fi

if [ $targetEnv = "dev" ] || [ $targetEnv = "dev-bwalia" ] || [ $targetEnv = "int" ] || [ $targetEnv = "test" ] || [ $targetEnv = "acc" ] || [ $targetEnv = "prod" ]; then
#if [ $targetEnv = "int" ]; then
echo -e "$RED The targetEnv is $targetEnv supported by this script"
else
echo "Oops! The targetEnv is $targetEnv is not supported by this script, check the README.md and try again! (Hint: Try default value is dev)"
exit 1
fi

###### Set some variables
if [ $TARGET_NAMESPACE = "int" ]; then
SVC_HOST=popos
fi

HOST_ENDPOINT_UNSECURE_URL="http://${SVC_HOST}:${SVC_NODEPORT}"

if [ $targetEnv = "dev" ]; then
APP_RELEASE_NOTES_DOC_URL="https://webimpetus.dev/docs/app_release_notes"
fi

if [ $targetEnv = "test" ]; then
APP_RELEASE_NOTES_DOC_URL="https://test.webimpetus.dev/docs/app_release_notes"
fi

if [ $targetEnv = "prod" ]; then
APP_RELEASE_NOTES_DOC_URL="https://webaimpetus.com/docs/"
fi

export APP_RELEASE_NOTES_DOC_URL=$APP_RELEASE_NOTES_DOC_URL

##### Set some variables
if [ $targetEnv = "dev" ] || [ $targetEnv = "dev-bwalia" ] || [ $TARGET_NAMESPACE = "int" ];
then
WORKSPACE_DIR=$(pwd)
fi

if [ $targetEnv = "test" ] || [ $targetEnv = "prod" ];
then
WORKSPACE_DIR="/tmp/webimpetus/${targetEnv}"
mkdir -p ${WORKSPACE_DIR}
chmod 777 ${WORKSPACE_DIR}
rm -rf ${WORKSPACE_DIR}/*
cp -r ../webimpetus/* ${WORKSPACE_DIR}/
fi

if [ $targetEnv = "dev" ];
then
echo -e "$RED No need to load kubeconfig use default rancher config"
fi

if [ $targetEnv = "int" ]; then
echo -e "$RED Load int env kubeconfig"
export KUBECONFIG=~/.kube/k3s8.yaml
fi

if [ $targetEnv = "acc" ]; then
echo -e "$RED Load acc env kubeconfig"
export KUBECONFIG=~/.kube/k3s3.yaml
fi

if [ $targetEnv = "test" ]; then
echo -e "$RED Load test env kubeconfig"
export KUBECONFIG=~/.kube/k3s2.yaml
fi

if [ $targetEnv = "prod" ]; then
echo -e "$RED Load prod env kubeconfig"
export KUBECONFIG=~/.kube/k3s1.yaml
fi

if [ $targetEnv = "dev" ]; then
echo -e "$RED No need to load kubeconfig use default var KUBE_CONFIG"
elif [ -z "$5" ]; then
echo "KUBECONFIG is empty, but mapped KUBECONFIG to $KUBECONFIG"
else
echo -e "$RED KUBECONFIG is provided, so setting KUBECONFIG $5"
export KUBECONFIG=$5
fi

if [ -z "$6" ]; then
echo "VIRTUAL_HOST is empty, so leaving default set VIRTUAL_HOST to whatever it may be (default ${SVC_HOST})"
export VIRTUAL_HOST=${SVC_HOST}
else
echo -e "$RED VIRTUAL_HOST is provided, so setting VIRTUAL_HOST $6"
export VIRTUAL_HOST=$6
fi

if [ -z "$7" ]; then
   echo "docker base image is empty, so setting docker base image to dev-wsl-webserver (default)"
   docker_base_image="${targetEnv}-wsl-webserver"
   
else
   echo -e "$RED docker base image type is provided, docker base image is set to $7"
   docker_base_image=$7
   IMAGE_NAME=$7
fi

if [ $targetEnv = "dev" ]; then
echo "No need to move env files in case local dev env"
else
cp "${WORKSPACE_DIR}/${targetEnv}.env" "${WORKSPACE_DIR}/.env"
fi

if [ -z "$8" ]; then
echo -e "$GREEN TARGET_CLUSTER is default, so leaving default set TARGET_CLUSTER to whatever it may be (default ${TARGET_CLUSTER})"
export TARGET_CLUSTER=${TARGET_CLUSTER}
else
echo -e "$GREEN TARGET_CLUSTER is provided, so setting TARGET_CLUSTER $8"
export TARGET_CLUSTER=$8
TARGET_CLUSTER=$8
fi

if [ -z "$9" ]; then
echo "Docker build cmd is default, so leaving default set BUILD_IMAGE_TOOL to whatever it may be (nerdctl)"
export BUILD_IMAGE_TOOL="docker"
else
echo -e "$RED BUILD_IMAGE_TOOL is provided, so setting BUILD_IMAGE_TOOL $9"
export BUILD_IMAGE_TOOL=$9
fi

VALUES_FILE_PATH=values-${TARGET_NAMESPACE}.yaml

if [ $targetEnv = "dev" ] || [ $targetEnv = "dev-bwalia" ]; then
TARGET_CLUSTER="k3s0"
echo -e "$GREEN TARGET_CLUSTER: $TARGET_CLUSTER"
elif [ $targetEnv = "test" ]; then
TARGET_CLUSTER="k3s2"
echo -e "$GREEN TARGET_CLUSTER: $TARGET_CLUSTER"
elif [ $targetEnv = "int" ]; then
TARGET_CLUSTER="k3s2"
else
echo -e "$GREEN TARGET_CLUSTER: $TARGET_CLUSTER"
fi

echo -e "$GREEN VALUES_FILE_PATH is not local dev, so setting VALUES_FILE_PATH to values-${TARGET_NAMESPACE}-${TARGET_CLUSTER}.yaml"
VALUES_FILE_PATH=values-${TARGET_NAMESPACE}-${TARGET_CLUSTER}.yaml
echo -e "$GREEN VALUES_FILE_PATH: $VALUES_FILE_PATH"

cd ${WORKSPACE_DIR}/

if [ $deployment_stage = "delete" ] || [ $targetEnv = "dev" ]
then
#kubectl delete -f devops/kubernetes/wsldeployment.yaml
helm_cmd=$(echo uninstall wsl-${targetEnv} -n ${TARGET_NAMESPACE})
echo "helm $helm_cmd"
helm $helm_cmd
fi

if [ $deployment_stage = "install" ]; then
         #helm upgrade --install workstation --set image.tag=${IMAGE_TAG} --set image.repository=${IMAGE_REPO}/workstation --set ingress.hosts[0].host=${HOST_ENDPOINT_UNSECURE_URL} --set ingress.hosts[0].paths[0]=/ --set ingress.hosts[0].paths[1]=/docs --set ingress.hosts[0].paths[2]=/docs/app_release_notes --set ingress.hosts[0].paths[3]=/docs/app_release_notes/${IMAGE_TAG} --set ingress.hosts[0].paths[4]=/docs/app_release_notes/${IMAGE_TAG}/webimpetus --set ingress.hosts[0].paths[5]=/docs/app_release_notes/${IMAGE_TAG}/webimpetus/${targetEnv} --set ingress.hosts[0].paths[6]=/docs/app_release_notes/${IMAGE_TAG}/webimpetus/${targetEnv}/webimpetus --set ingress.hosts[0].paths[7]=/docs/app_release_notes/${IMAGE_TAG}/webimpetus/${targetEnv}/webimpetus/${targetEnv} --set ingress.hosts[0].paths[8]=/docs/app_release_notes/${IMAGE_TAG}/webimpetus/${targetEnv}/webimpetus/${targetEnv}/webimpetus --set ingress.hosts[0].paths[9]=/docs/app_release_notes/${IMAGE_TAG}/webimpetus/${targetEnv}/webimpetus/${targetEnv}/webimpetus/${targetEnv} --set ingress.hosts[0].paths[10]=/docs/app_release_notes/${IMAGE_TAG}/webimpetus/${targetEnv}/webimpetus/${targetEnv}/webimpetus/${targetEnv}/webimpetus --set ingress.hosts[0].paths[11]=/docs/app_release_notes/${IMAGE_TAG}/webimpetus/${targetEnv}/webimpetus/${targetEnv}/webimpetus/${targetEnv}/webimpetus/${targetEnv} --set ingress.hosts[0].paths[12]=/docs/app_release_notes/${IMAGE_TAG}/webimpetus/${targetEnv}/webimpetus/${targetEnv}/webimpetus/${targetEnv}/webimpetus/${targetEnv}/webimpetus --set ingress.hosts[0].paths[13]=/docs/app_release_notes/${IMAGE_TAG}/webimpetus/${targetEnv}/webimpetus/${targetEnv}/web
         #helm uninstall wsl-${targetEnv} -n ${targetEnv}
         ###helm upgrade --install -f devops/webimpetus-chart/values-${targetEnv}.yaml wsl-${targetEnv} ./devops/webimpetus-chart --set image=${IMAGE_REPO}/workstation:${IMAGE_TAG} --namespace ${targetEnv}
   helm_cmd=$(echo upgrade --install -f devops/webimpetus-chart/${VALUES_FILE_PATH} wsl-${TARGET_NAMESPACE} devops/webimpetus-chart/ --set-string targetImage="${IMAGE_REPO}/${IMAGE_NAME}" --set-string targetImageTag="${IMAGE_TAG}" --namespace ${TARGET_NAMESPACE} --create-namespace)
   echo -e "$GREEN helm $helm_cmd"
   helm $helm_cmd

sleep 60 # wait for 60 seconds for the k3s deployment to be ready
kubectl get pods -A
fi

if [ $targetEnv = "dev" ] || [ $targetEnv = "dev-bwalia" ] && [ "$deployment_stage" = "install" ]; then
echo "$targetEnv env installation completed..."

sleep 10 # wait for 10 seconds for the dev deployment to be ready

echo "$YELLLOW Waiting for services to install..."

curl -IL $HOST_ENDPOINT_UNSECURE_URL -H "Host: ${VIRTUAL_HOST}"
os_type=$(uname -s)

if [ "$os_type" = "Darwin" ]; then
open $HOST_ENDPOINT_UNSECURE_URL
fi

if [ "$os_type" = "Linux" ]; then
xdg-open $HOST_ENDPOINT_UNSECURE_URL
fi

fi
