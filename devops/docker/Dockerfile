ARG BASE_TAG=latest

#FROM registry.workstation.co.uk/openresty-php:$TAG
FROM bwalia/openresty-php:$BASE_TAG

WORKDIR /var/www/html

RUN apk update && apk upgrade \
     jq

COPY ci4 /src

#updated ci4 to ci4.2.10 on 12 Dec 2022 BW/HS Testing 4.2.10 but issues with cors and s3 extension
#COPY ci4.2.10-test /src 

#COPY ./config/php/php.ini /usr/local/etc/php/php.ini
COPY ./config/vhosts/webimpetus.conf /etc/nginx/sites-enabled/webimpetus.conf
COPY ./config/vhosts/nginx.conf /usr/local/openresty/nginx/conf/nginx.conf
     #RUN sed -i "s|listen = 127.0.0.1:9000|listen = 127.0.0.1:9000\nlisten = /var/run/php-fpm/www.sock|g" /usr/local/etc/php-fpm.d/www.conf