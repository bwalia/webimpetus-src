#!/usr/bin/bash

# Quit on error.
set -e
# Treat undefined variables as errors.
set -u

set -x

function start {

#/usr/local/openresty/bin/openresty
# -g daemon off 

# systemctl daemon-reload
# systemctl enable php-fpm-5.service
# systemctl start php-fpm-5.service
# systemctl status php-fpm-5.service

}

start "$@"
