#!/bin/bash
# This deploys CI4 project (mariadb, php_lamp, phpmyadmin) in docker container to test environment using docker compose.

set -x

# docker-compose down
# docker-compose build
# docker-compose up -d
# docker-compose ps
#sleep 30

mv .env dev.env

# docker cp /home/bwalia/env_webimpetus_dev_ci4baseimagetest lamp-php74:/var/www/html/.env
# docker exec -it lamp-php74 chown -R www-data:www-data /var/www/html/writable/

# sleep 30

# #./reset_env.sh

# sudo -S rm -Rf ci4/
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/data
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/config
# sudo -S rm -Rf /home/bwalia/actions-runner-webimpetus/_work/webimpetus/webimpetus/