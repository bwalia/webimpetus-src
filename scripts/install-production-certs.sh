#!/bin/bash

# Script to install production SSL certificates for dev000.workstation.co.uk
# Usage: ./install-production-certs.sh <path-to-cert-directory>

set -e

DOMAIN="dev000.workstation.co.uk"
NGINX_SSL_DIR="/home/bwalia/workerra-ci/nginx/ssl"

echo "=========================================="
echo "  SSL Certificate Installation"
echo "  Domain: $DOMAIN"
echo "=========================================="
echo ""

# Check if certificate directory is provided
if [ -z "$1" ]; then
    echo "Usage: $0 <path-to-cert-directory>"
    echo ""
    echo "Example: $0 /Users/balinderwalia/Documents/Work/aws_keys"
    echo ""
    echo "Expected files in the directory:"
    echo "  - workstation-cert.crt (or .pem)"
    echo "  - workstation-cert.key"
    echo "  - workstation-cert-chain.crt (optional)"
    exit 1
fi

CERT_DIR="$1"

# Check if directory exists
if [ ! -d "$CERT_DIR" ]; then
    echo "❌ Error: Directory $CERT_DIR does not exist"
    exit 1
fi

echo "Looking for certificates in: $CERT_DIR"
echo ""

# Find certificate files
CERT_FILE=""
KEY_FILE=""
CHAIN_FILE=""

# Look for certificate file
for ext in crt pem cert; do
    if [ -f "$CERT_DIR/workstation-cert.$ext" ]; then
        CERT_FILE="$CERT_DIR/workstation-cert.$ext"
        echo "✓ Found certificate: $CERT_FILE"
        break
    fi
done

# Look for key file (check both workstation-ca.key and workstation-cert.key)
for prefix in workstation-ca workstation-cert; do
    for ext in key pem; do
        if [ -f "$CERT_DIR/$prefix.$ext" ]; then
            KEY_FILE="$CERT_DIR/$prefix.$ext"
            echo "✓ Found private key: $KEY_FILE"
            break 2
        fi
    done
done

# Look for chain file (optional)
for ext in crt pem chain; do
    if [ -f "$CERT_DIR/workstation-cert-chain.$ext" ]; then
        CHAIN_FILE="$CERT_DIR/workstation-cert-chain.$ext"
        echo "✓ Found certificate chain: $CHAIN_FILE"
        break
    elif [ -f "$CERT_DIR/workstation-cert.chain.$ext" ]; then
        CHAIN_FILE="$CERT_DIR/workstation-cert.chain.$ext"
        echo "✓ Found certificate chain: $CHAIN_FILE"
        break
    fi
done

echo ""

# Verify required files found
if [ -z "$CERT_FILE" ]; then
    echo "❌ Error: Certificate file not found"
    echo "   Looking for: workstation-cert.{crt,pem,cert}"
    exit 1
fi

if [ -z "$KEY_FILE" ]; then
    echo "❌ Error: Private key file not found"
    echo "   Looking for: workstation-cert.{key,pem}"
    exit 1
fi

# Verify certificate and key match
echo "Verifying certificate and key..."
CERT_MD5=$(openssl x509 -noout -modulus -in "$CERT_FILE" 2>/dev/null | openssl md5 | cut -d' ' -f2)
KEY_MD5=$(openssl rsa -noout -modulus -in "$KEY_FILE" 2>/dev/null | openssl md5 | cut -d' ' -f2)

if [ "$CERT_MD5" != "$KEY_MD5" ]; then
    echo "❌ Error: Certificate and key do not match!"
    echo "   Certificate MD5: $CERT_MD5"
    echo "   Key MD5: $KEY_MD5"
    exit 1
fi

echo "✓ Certificate and key match"
echo ""

# Display certificate information
echo "Certificate Information:"
echo "------------------------"
openssl x509 -noout -subject -issuer -dates -in "$CERT_FILE" | sed 's/^/  /'
echo ""

# Check if certificate is valid for the domain
if openssl x509 -noout -text -in "$CERT_FILE" | grep -q "$DOMAIN"; then
    echo "✓ Certificate is valid for $DOMAIN"
else
    echo "⚠️  Warning: Certificate may not be valid for $DOMAIN"
    echo "   Continuing anyway..."
fi
echo ""

# Backup existing certificates
echo "Backing up existing certificates..."
if [ -f "$NGINX_SSL_DIR/workstation.local.crt" ]; then
    mv "$NGINX_SSL_DIR/workstation.local.crt" "$NGINX_SSL_DIR/workstation.local.crt.backup.$(date +%s)"
    mv "$NGINX_SSL_DIR/workstation.local.key" "$NGINX_SSL_DIR/workstation.local.key.backup.$(date +%s)"
    echo "✓ Backed up old certificates"
fi

# Copy new certificates
echo "Installing new certificates..."
cp "$CERT_FILE" "$NGINX_SSL_DIR/$DOMAIN.crt"
cp "$KEY_FILE" "$NGINX_SSL_DIR/$DOMAIN.key"

# If chain file exists, append it to the certificate
if [ -n "$CHAIN_FILE" ]; then
    echo "✓ Appending certificate chain..."
    cat "$CHAIN_FILE" >> "$NGINX_SSL_DIR/$DOMAIN.crt"
fi

# Set proper permissions
chmod 644 "$NGINX_SSL_DIR/$DOMAIN.crt"
chmod 600 "$NGINX_SSL_DIR/$DOMAIN.key"

echo "✓ Certificates installed"
echo ""

# Update nginx configuration
echo "Updating nginx configuration..."
NGINX_CONF="/home/bwalia/workerra-ci/nginx/nginx.conf"

# Backup nginx config
cp "$NGINX_CONF" "$NGINX_CONF.backup.$(date +%s)"

# Update certificate paths and domain
sed -i "s|server_name workstation.local|server_name $DOMAIN|g" "$NGINX_CONF"
sed -i "s|ssl_certificate /etc/nginx/ssl/workstation.local.crt|ssl_certificate /etc/nginx/ssl/$DOMAIN.crt|g" "$NGINX_CONF"
sed -i "s|ssl_certificate_key /etc/nginx/ssl/workstation.local.key|ssl_certificate_key /etc/nginx/ssl/$DOMAIN.key|g" "$NGINX_CONF"

echo "✓ Nginx configuration updated"
echo ""

# Update .env file
echo "Updating .env file..."
ENV_FILE="/home/bwalia/workerra-ci/.env"
cp "$ENV_FILE" "$ENV_FILE.backup.$(date +%s)"

# Update base URL
sed -i "s|app.baseURL='https://.*'|app.baseURL='https://$DOMAIN:8443/'|g" "$ENV_FILE"

echo "✓ .env updated"
echo ""

# Update hosts file
echo "Updating /etc/hosts..."
echo ""
echo "You need to add the following line to your /etc/hosts file:"
echo ""
echo "  127.0.0.1    $DOMAIN"
echo ""
read -p "Add this entry now? (requires sudo) [y/N] " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    # Check if entry already exists
    if grep -q "$DOMAIN" /etc/hosts; then
        echo "⚠️  Entry already exists in /etc/hosts"
    else
        echo "127.0.0.1    $DOMAIN" | sudo tee -a /etc/hosts > /dev/null
        echo "✓ Added to /etc/hosts"
    fi
fi
echo ""

# Restart nginx
echo "Restarting nginx container..."
cd /home/bwalia/workerra-ci
docker-compose restart nginx

echo ""
echo "Waiting for nginx to start..."
sleep 3

# Test the setup
echo ""
echo "=========================================="
echo "  Testing Configuration"
echo "=========================================="
echo ""

# Test HTTPS
echo -n "Testing HTTPS connection... "
if curl -k -s --connect-timeout 5 "https://$DOMAIN:8443/health" | grep -q "OK"; then
    echo "✓ PASS"
else
    echo "✗ FAIL"
fi

# Check certificate
echo -n "Verifying SSL certificate... "
if echo | openssl s_client -connect "$DOMAIN:8443" -servername "$DOMAIN" 2>/dev/null | openssl x509 -noout -subject | grep -q "$DOMAIN"; then
    echo "✓ PASS"
else
    echo "⚠️  Warning: Certificate verification failed"
fi

echo ""
echo "=========================================="
echo "  Installation Complete!"
echo "=========================================="
echo ""
echo "Your application is now accessible at:"
echo ""
echo "  🔒 https://$DOMAIN:8443/"
echo ""
echo "Services:"
echo "  • Main App:       https://$DOMAIN:8443/"
echo "  • Database Admin: https://$DOMAIN:8443/adminer/"
echo "  • Keycloak SSO:   https://$DOMAIN:8443/auth/"
echo "  • MinIO Storage:  https://$DOMAIN:8443/minio/"
echo "  • Health Check:   https://$DOMAIN:8443/health"
echo ""
echo "Documentation updated in:"
echo "  - nginx/nginx.conf"
echo "  - .env"
echo "  - /etc/hosts (if you approved)"
echo ""
echo "Backups created with timestamp suffix in same directories."
echo ""
