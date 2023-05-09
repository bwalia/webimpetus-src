#!/bin/bash

    CLUSTER_NAME=k3s3
    TARGET_ENV=test
    DOCKER_IMAGE=registry.workstation.co.uk/wsl-openresty_php
    IMAGE_TAG=1e8ed5

    # clear; kubectl get pods,ing,svc -A
    clear; ls -altr /helm-charts/

    #cd /helm-charts/

    #helm repo add stable https://charts.helm.sh/stable

    helm uninstall wsl-$TARGET_ENV -n $TARGET_ENV

    helm upgrade -i wsl-$TARGET_ENV \
    /helm-charts/webimpetus-chart \
    -f /helm-charts/webimpetus-chart/values-$TARGET_ENV-$CLUSTER_NAME.yaml \
    --set-string targetImage="$DOCKER_IMAGE" \
    --set-string targetImageTag="$IMAGE_TAG" \
    --namespace $TARGET_ENV \
    --create-namespace

    #echo $helm_cmd

    clear; helm ls -n $TARGET_ENV && kubectl get pods,ing,svc -n $TARGET_ENV
s