#!/usr/bin/env bash
# -- Author: Balinder Walia --
# -- Pushes the docker image : webimpetus into AWS ECR / or optionally into a given Docker registry --
# -- $1 is mandatory image version, for example : latest --

set -x

VERSION=$1
AWS_PROFILE_NAME=$2
REGION=$3

if [ -z ${VERSION} ];
then
  echo "webimpetus image version not set"
  exit 1
fi
if [ -z ${AWS_PROFILE_NAME} ];
then
  AWS_PROFILE_NAME=default
fi
if [ -z ${REGION} ];
then
  REGION=eu-west-2
fi

DOCKER_REG_HUB_USER=tenthmatrix # change repostory, can be changed to match customer repo domain name
DOCKER_IMAGE_NAME=${DOCKER_REG_HUB_USER}/webimpetus-ci4
#DOCKER_IMAGE_NAME=webimpetus

    #TAG=$(git log -1 --pretty=%H) # Tag image same as git commit by default
    #DOCKER_IMAGE_NAME=${DOCKER_IMAGE_NAME}:${TAG}

# LATEST=${DOCKER_IMAGE_NAME}:${VERSION}
echo "Building image... $DOCKER_IMAGE_NAME"
    # Do not use public docker repo for this project but in case of your own docker private repo uncomment code below and comment ECR repo down below
    # docker build -t ${DOCKER_IMAGE_NAME} .
    # echo "Docker image tag...$LATEST"
    # docker tag ${DOCKER_IMAGE_NAME} ${LATEST}
    # docker push ${DOCKER_IMAGE_NAME}

aws_cli_major_version=$(aws --version | awk '{print $1}' | awk -F/ '{print $2}' | awk -F\. '{print $1}')
ACCOUNTID=$(AWS_PROFILE=${AWS_PROFILE_NAME} aws sts get-caller-identity | jq ".Account" -r)

if [ "$aws_cli_major_version" -eq "2" ]; then
  AWS_PROFILE=${AWS_PROFILE_NAME} aws ecr get-login-password --region ${REGION} --profile ${AWS_PROFILE_NAME} | docker login --username AWS --password-stdin ${ACCOUNTID}.dkr.ecr.${REGION}.amazonaws.com
else
  $(AWS_PROFILE=${AWS_PROFILE_NAME} aws ecr get-login --region ${REGION} --no-include-email)
fi

if [ "$aws_cli_major_version" -eq "2" ]; then
aws_ecr_img_repository=$(aws ecr --region ${REGION} describe-repositories --repository-names ${DOCKER_IMAGE_NAME} --profile ${AWS_PROFILE_NAME} | jq -r '.repositories[0].repositoryArn')

  # echo $aws_ecr_img_repository;
if [ -z ${aws_ecr_img_repository} ];
then
  # repository does not exist then add before pushing the image
AWS_PROFILE=${AWS_PROFILE_NAME} aws ecr --region ${REGION} create-repository --repository-name ${DOCKER_IMAGE_NAME}
fi

fi

docker build -t ${ACCOUNTID}.dkr.ecr.${REGION}.amazonaws.com/${DOCKER_IMAGE_NAME}:${VERSION} -f devops/docker/Dockerfile .
docker push ${ACCOUNTID}.dkr.ecr.${REGION}.amazonaws.com/${DOCKER_IMAGE_NAME}:${VERSION}