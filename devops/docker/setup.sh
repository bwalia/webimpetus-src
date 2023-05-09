#!/usr/bin/bash

# Quit on error.
set -e
# Treat undefined variables as errors.
set -u

function main {

# Create dir for pid file.
THE_DIR="/opt/scripts/"
mkdir -p ${THE_DIR}
chown www-data:root ${THE_DIR}
chmod +x ${THE_DIR}
chmod 755 -R ${THE_DIR}

# Create dir for php unix sockets.
THE_DIR="/var/run/php-fpm/"
mkdir -p ${THE_DIR}
chown www-data:root ${THE_DIR}
chmod +x ${THE_DIR}
chmod 777 -R ${THE_DIR}

# Create dir for nginx.
THE_DIR="/var/nginx/"
mkdir -p ${THE_DIR}
chown www-data:root ${THE_DIR}
chmod +x ${THE_DIR}
chmod 775 -R ${THE_DIR}

# Create dir for nginx logs.
THE_DIR="/var/log/nginx/"
mkdir -p ${THE_DIR}
chown www-data:root ${THE_DIR}
chmod +x ${THE_DIR}
chmod 755 -R ${THE_DIR}

# Create dir for nginx tenant tizohub code.
THE_DIR="/var/www/html/"
mkdir -p ${THE_DIR}
chown www-data:root ${THE_DIR}
chmod +x ${THE_DIR}
chmod 755 -R ${THE_DIR}

# systemctl daemon-reload
# systemctl enable php-fpm-5.service
# systemctl start php-fpm-5.service
# systemctl status php-fpm-5.service

}

main "$@"
