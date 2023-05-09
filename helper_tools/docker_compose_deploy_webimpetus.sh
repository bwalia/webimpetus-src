
#!/bin/bash
############ This bash script deploys mysql (mariadb, mysql, different versions)
############ as docker container into dev,test or prod environment using docker compose files.

set -x

###### Set some variables
WORKSPACE_DIR="/tmp/database/"

##### Set some variables

if [[ -z "$1" ]]; then
   echo "env is empty, so setting targetEnv to development (default)"
   targetEnv="dev"
else
   echo "env is NOT empty, so setting targetEnv to $1"
   targetEnv=$1
fi

mkdir -p ${workspace_dir}
chmod 777 ${workspace_dir}

rm -rf ${workspace_dir}*
cp -r ../webimpetus/* ${workspace_dir}

mv ${workspace_dir}$targetEnv.env ${workspace_dir}.env

cd ${workspace_dir}

docker-compose -f docker-compose-database.yml down
docker-compose -f docker-compose-database.yml up -d --build
docker-compose -f docker-compose-database.yml ps

sleep 2

echo "MySQL/MariaDB endpoint..."












# cp -r ../webimpetus/* /tmp/$workdirname_file
# mv /tmp/$workdirname_file/dev.env /tmp/$workdirname_file/.env
# docker-compose -f /tmp/$workdirname_file/docker-compose.yml down
# # docker-compose build
# docker-compose -f /tmp/$workdirname_file/docker-compose.yml up -d --build
# docker-compose -f /tmp/$workdirname_file/docker-compose.yml ps
# mv /tmp/$workdirname_file/prepare_workspace_env.sh .
#mv /tmp/prepare_workspace_env.sh .
# sleep 30
# #./reset_env.sh
# sudo -S rm -Rf ci4/
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/data
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/config
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/