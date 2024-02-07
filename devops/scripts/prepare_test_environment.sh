#!/bin/bash

set -x

#aws eks

rm -Rf env
cp /home/bwalia/env_webimpetus_dev_ci4baseimagetest env

export KUBECONFIG=/home/bwalia/.kube/k3s-test.yml
#aws eks update-kubeconfig --name $KUBENETES_CLUSTER_NAME --region $AWS_DEFAULT_REGION

kubectl get all -A

#kubectl delete -f /var/www/html/writable/webimpetus_deployments/service-$SERVICE_ID.yaml

