#!/bin/bash

set -x

docker cp /home/bwalia/env_webimpetus_dev_ci4baseimagetest lamp-php74:/var/www/html/.env
docker exec lamp-php74 chown -R www-data:www-data /var/www/html/writable/
docker exec lamp-php74 composer update

