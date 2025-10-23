#!/bin/bash
# This deploys CI4 project (mariadb, php_lamp, phpmyadmin) in docker container to test environment using docker compose.

set -x

# docker-compose down
# docker-compose build
# docker-compose up -d
# docker-compose ps
#sleep 30

mv .env dev.env

# docker cp /home/bwalia/env_workerra-ci_dev_ci4baseimagetest lamp-php74:/var/www/html/.env
# docker exec -it lamp-php74 chown -R www-data:www-data /var/www/html/writable/

# sleep 30

# #./reset_env.sh

# sudo -S rm -Rf ci4/
# sudo -S rm -Rf /home/bwalia/actions-runner-workerra-ci/_work/workerra-ci/workerra-ci/data
# sudo -S rm -Rf /home/bwalia/actions-runner-workerra-ci/_work/workerra-ci/workerra-ci/config
# sudo -S rm -Rf /home/bwalia/actions-runner-workerra-ci/_work/workerra-ci/workerra-ci/