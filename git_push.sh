#!/bin/bash

############ This bash script deploys WebImpetus CI4 project (mariadb, php_lamp, phpmyadmin)
############ as kubernetes deployment into dev,test or prod environment using k3s.

set -x

git checkout devops/prod_deployment_setup

git add devops
#git add ci4
git commit -m "Updated bwalia devops ${date} enhancements"
#git push
git push --set-upstream origin devops/prod_deployment_setup


