#!/bin/bash

export KUBECONFIG=/var/www/html/writable/kube_config_auth
aws eks update-kubeconfig --name $KUBENETES_CLUSTER_NAME --region $AWS_DEFAULT_REGION

kubectl delete -f /var/www/html/writable/webimpetus_deployments/service-$SERVICE_ID.yaml

#kubectl get pods
#kubectl get pods -o json