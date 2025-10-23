#!/bin/bash

# Script to add dev000.workstation.co.uk to /etc/hosts
# Run with: sudo ./setup-hosts.sh

set -e

HOSTS_FILE="/etc/hosts"
DOMAIN="dev000.workstation.co.uk"
IP="127.0.0.1"

echo "=================================="
echo "  Hosts File Setup for workerra-ci"
echo "=================================="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo "❌ Error: This script must be run as root (use sudo)"
    exit 1
fi

# Check if entry already exists
if grep -q "$DOMAIN" "$HOSTS_FILE"; then
    echo "⚠️  Entry for $DOMAIN already exists in $HOSTS_FILE"
    echo ""
    echo "Current entry:"
    grep "$DOMAIN" "$HOSTS_FILE"
    echo ""
    read -p "Do you want to remove and re-add it? (y/n) " -n 1 -r
    echo ""

    if [[ $REPLY =~ ^[Yy]$ ]]; then
        # Remove existing entry
        sed -i.backup "/$DOMAIN/d" "$HOSTS_FILE"
        echo "✅ Removed existing entry"
    else
        echo "ℹ️  Keeping existing entry"
        exit 0
    fi
fi

# Add new entry
echo "$IP    $DOMAIN" >> "$HOSTS_FILE"
echo "✅ Added: $IP    $DOMAIN"

# Add wildcard subdomain support
echo "$IP    api.$DOMAIN" >> "$HOSTS_FILE"
echo "✅ Added: $IP    api.$DOMAIN"

echo "$IP    admin.$DOMAIN" >> "$HOSTS_FILE"
echo "✅ Added: $IP    admin.$DOMAIN"

echo ""
echo "=================================="
echo "  Setup Complete!"
echo "=================================="
echo ""
echo "You can now access:"
echo "  • https://dev000.workstation.co.uk        (Main App - HTTPS)"
echo "  • https://dev000.workstation.co.uk/adminer/ (Database Admin)"
echo "  • https://dev000.workstation.co.uk/auth/  (Keycloak SSO)"
echo "  • https://dev000.workstation.co.uk/minio/ (MinIO Storage)"
echo ""
echo "To verify, run: cat $HOSTS_FILE | grep $DOMAIN"
echo ""
