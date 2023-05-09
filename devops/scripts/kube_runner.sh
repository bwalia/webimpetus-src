#!/bin/bash

#   set -x

#printenv | grep KUBECONFIG_BASE64
#printenv | grep RUN_BASH_BASE64

FILE=~/.kube

mkdir -p $FILE

if [ -d $FILE ]; then

 echo "Directory $FILE exists."
 
 ls -latr $FILE

 FILE=~/.kube/config

 echo $KUBECONFIG_BASE64 | base64 -d > $FILE

if [ -f $FILE ]; then
stat $FILE
    # SECURITY RISK cat $FILE

 export KUBECONFIG=$FILE
else
    echo "kubeconfig $FILE does not exist."
    mkdir $FILE
fi

else
    echo "Directory $FILE does not exist."
    mkdir $FILE
 FILE=~/.kube/config
 echo $KUBECONFIG > $FILE
fi

printf "Kubectl version:"
kubectl version

printf "Helm version:"
helm version

printf "Kubectl get pods, ingress and service objects:"
kubectl get pods,ing,svc -A

FILE=/src/scripts/kustom_kube_runner.sh

if [ -z $RUN_BASH_BASE64 ]; then
    echo "RUN_BASH_BASE64 is empty, so aborting process"
else
    echo "RUN_BASH_BASE64 is provided, so running $FILE inside the docker container"

echo $RUN_BASH_BASE64 | base64 -d > $FILE
chmod +x $FILE
/bin/bash $FILE
fi

