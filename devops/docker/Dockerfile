ARG BASE_TAG=latest

#FROM registry.workstation.co.uk/openresty-php:$TAG

FROM bwalia/openresty-php:$BASE_TAG

WORKDIR /var/www/html

RUN apk update && apk upgrade \
     jq bash

ENV KUBESEAL_VERSION='0.24.4'
RUN wget "https://github.com/bitnami-labs/sealed-secrets/releases/download/v${KUBESEAL_VERSION:?}/kubeseal-${KUBESEAL_VERSION:?}-linux-amd64.tar.gz"
RUN tar -xvzf kubeseal-${KUBESEAL_VERSION:?}-linux-amd64.tar.gz kubeseal
RUN install -m 755 kubeseal /usr/local/bin/kubeseal

ENV BASE_URL="https://get.helm.sh"
ENV VERSION="3.7.2"
ENV ARCH="amd64"

RUN wget ${BASE_URL}/helm-v${VERSION}-linux-${ARCH}.tar.gz -O - | tar -xz && \
    mv linux-${ARCH}/helm /usr/bin/helm && \
    chmod +x /usr/bin/helm && \
    rm -rf linux-${ARCH}

RUN apk add mysql-client
RUN apk add curl
RUN wget https://dl.min.io/client/mc/release/linux-amd64/mc -O /usr/local/bin/mc \
    --tries=3 --timeout=30 && \
    chmod +x /usr/local/bin/mc
#   Install kubectl and helmchart binaries

RUN curl -LO https://storage.googleapis.com/kubernetes-release/release/v1.18.0/bin/linux/amd64/kubectl && chmod +x ./kubectl && mv ./kubectl /usr/bin/kubectl
RUN kubectl version --client

COPY ci4 /src

WORKDIR /src
RUN composer update
WORKDIR /var/www/html

#    updated ci4 to ci4.2.10 on 12 Dec 2022 BW/HS Testing 4.2.10 but issues with cors and s3 extension
#    COPY ci4.2.10-test /src 
#    COPY ./config/php/php.ini /usr/local/etc/php/php.ini

COPY ./config/vhosts/webimpetus.conf /etc/nginx/sites-enabled/webimpetus.conf
COPY ./config/vhosts/nginx.conf /usr/local/openresty/nginx/conf/nginx.conf

     #  RUN sed -i "s|listen = 127.0.0.1:9000|listen = 127.0.0.1:9000\nlisten = /var/run/php-fpm/www.sock|g" /usr/local/etc/php-fpm.d/www.conf
