# Production Certificate Setup for dev000.workstation.co.uk

## Overview

This guide will help you install the proper SSL certificates for `dev000.workstation.co.uk` to replace the self-signed certificate.

## Certificate Files Needed

From your certificate directory at `/Users/balinderwalia/Documents/Work/aws_keys/`:

```
workstation-cert.crt    (or .pem) - SSL Certificate
workstation-cert.key              - Private Key
workstation-cert-chain.crt (optional) - Certificate Chain
```

## Installation Methods

### Method 1: Automated Installation (Recommended)

If you have the certificate files on this system:

```bash
# Copy certificates to this system first, then run:
./install-production-certs.sh /path/to/certificate/directory

# Example:
./install-production-certs.sh /home/bwalia/certs
```

The script will:
- ✓ Verify certificate and key match
- ✓ Display certificate information
- ✓ Backup existing certificates
- ✓ Install new certificates
- ✓ Update nginx configuration
- ✓ Update .env file
- ✓ Update /etc/hosts
- ✓ Restart nginx
- ✓ Test the setup

### Method 2: Manual Installation

If you prefer manual installation:

#### Step 1: Transfer Certificate Files

```bash
# From your Mac, copy the certificates to this Linux system
# Option A: Using scp
scp /Users/balinderwalia/Documents/Work/aws_keys/workstation-cert.* user@thisserver:/home/bwalia/webimpetus-src/nginx/ssl/

# Option B: Copy content manually (see Step 2)
```

#### Step 2: Install Certificates Manually

```bash
cd /home/bwalia/webimpetus-src

# Create destination directory if needed
mkdir -p nginx/ssl

# Copy your certificate files
cp /path/to/workstation-cert.crt nginx/ssl/dev000.workstation.co.uk.crt
cp /path/to/workstation-cert.key nginx/ssl/dev000.workstation.co.uk.key

# If you have a certificate chain, append it
cat /path/to/workstation-cert-chain.crt >> nginx/ssl/dev000.workstation.co.uk.crt

# Set proper permissions
chmod 644 nginx/ssl/dev000.workstation.co.uk.crt
chmod 600 nginx/ssl/dev000.workstation.co.uk.key
```

#### Step 3: Update Nginx Configuration

Edit `nginx/nginx.conf` and replace:

```nginx
# OLD:
server_name workstation.local *.workstation.local;
ssl_certificate /etc/nginx/ssl/workstation.local.crt;
ssl_certificate_key /etc/nginx/ssl/workstation.local.key;

# NEW:
server_name dev000.workstation.co.uk;
ssl_certificate /etc/nginx/ssl/dev000.workstation.co.uk.crt;
ssl_certificate_key /etc/nginx/ssl/dev000.workstation.co.uk.key;
```

You'll need to update this in TWO places:
- Line 66: HTTP redirect server block
- Line 76: HTTPS server block

#### Step 4: Update .env File

Edit `.env` and update the base URL:

```bash
# OLD:
app.baseURL='https://workstation.local:8443/'

# NEW:
app.baseURL='https://dev000.workstation.co.uk:8443/'
```

#### Step 5: Update Hosts File

Add the domain to your `/etc/hosts`:

```bash
sudo nano /etc/hosts

# Add this line:
127.0.0.1    dev000.workstation.co.uk
```

#### Step 6: Restart Nginx

```bash
docker-compose restart nginx
```

#### Step 7: Verify

```bash
# Test HTTPS
curl -k https://dev000.workstation.co.uk:8443/health

# Check certificate
echo | openssl s_client -connect dev000.workstation.co.uk:8443 -servername dev000.workstation.co.uk 2>/dev/null | openssl x509 -noout -text | grep -A2 "Subject:"
```

### Method 3: Quick Setup with Certificate Content

If you can't transfer files but have the certificate content:

1. **Copy Certificate Content:**

```bash
# Create certificate file
cat > nginx/ssl/dev000.workstation.co.uk.crt << 'EOF'
-----BEGIN CERTIFICATE-----
[Paste your certificate content here]
-----END CERTIFICATE-----
EOF

# Create key file
cat > nginx/ssl/dev000.workstation.co.uk.key << 'EOF'
-----BEGIN PRIVATE KEY-----
[Paste your private key content here]
-----END PRIVATE KEY-----
EOF

# Set permissions
chmod 644 nginx/ssl/dev000.workstation.co.uk.crt
chmod 600 nginx/ssl/dev000.workstation.co.uk.key
```

2. **Then follow Steps 3-7 from Method 2**

## Verification Checklist

After installation, verify:

- [ ] Certificate and key files exist in `nginx/ssl/`
- [ ] Nginx configuration updated with new domain
- [ ] .env file updated with new base URL
- [ ] /etc/hosts has dev000.workstation.co.uk entry
- [ ] Nginx container restarted successfully
- [ ] HTTPS connection works: `curl -k https://dev000.workstation.co.uk:8443/health`
- [ ] Browser can access: https://dev000.workstation.co.uk:8443/
- [ ] Certificate is valid (no self-signed warning)

## Certificate Information Check

To view certificate details:

```bash
# View certificate info
openssl x509 -in nginx/ssl/dev000.workstation.co.uk.crt -noout -text

# Check validity dates
openssl x509 -in nginx/ssl/dev000.workstation.co.uk.crt -noout -dates

# Verify certificate and key match
openssl x509 -noout -modulus -in nginx/ssl/dev000.workstation.co.uk.crt | openssl md5
openssl rsa -noout -modulus -in nginx/ssl/dev000.workstation.co.uk.key | openssl md5
# These MD5 hashes should match
```

## Troubleshooting

### Issue: Certificate and key don't match

**Error**: `key values mismatch` or different MD5 hashes

**Solution**: Ensure you're using the correct certificate and key pair

```bash
# Verify they match
openssl x509 -noout -modulus -in nginx/ssl/dev000.workstation.co.uk.crt | openssl md5
openssl rsa -noout -modulus -in nginx/ssl/dev000.workstation.co.uk.key | openssl md5
```

### Issue: Nginx won't start

**Check logs**:
```bash
docker logs webimpetus-nginx
```

**Common causes**:
- Certificate file path wrong
- Certificate file permissions wrong
- Certificate format invalid

### Issue: Browser still shows self-signed warning

**Possible causes**:
1. Wrong certificate installed
2. Certificate not valid for the domain
3. Browser cached old certificate (clear browser cache)
4. Using wrong domain in URL

**Verify certificate**:
```bash
echo | openssl s_client -connect dev000.workstation.co.uk:8443 -servername dev000.workstation.co.uk 2>/dev/null | openssl x509 -noout -subject -issuer
```

### Issue: Can't connect to domain

**Check**:
```bash
# Is it in hosts file?
grep dev000.workstation.co.uk /etc/hosts

# Is nginx running?
docker ps | grep nginx

# Can you ping it?
ping dev000.workstation.co.uk
```

## Post-Installation

After successful installation:

1. **Test all services**:
   - Main app: https://dev000.workstation.co.uk:8443/
   - Adminer: https://dev000.workstation.co.uk:8443/adminer/
   - Keycloak: https://dev000.workstation.co.uk:8443/auth/

2. **Update documentation**:
   - Update any README files with new domain
   - Update team documentation
   - Update bookmarks

3. **Test document features**:
   - Upload a document
   - Preview a document
   - Download a document

## Production Deployment

For actual production (not dev):

1. **Update ports** in `docker-compose.yml`:
   ```yaml
   ports:
     - "80:80"      # Standard HTTP
     - "443:443"    # Standard HTTPS
   ```

2. **Update .env**:
   ```bash
   app.baseURL='https://dev000.workstation.co.uk/'  # No port
   ```

3. **Configure DNS** properly (not /etc/hosts):
   - Point dev000.workstation.co.uk to server IP in real DNS

4. **Set up certificate auto-renewal** if using Let's Encrypt

## Support

If you encounter issues:

1. Check logs: `docker logs webimpetus-nginx`
2. Verify certificate: Run verification commands above
3. Test connectivity: `curl -k https://dev000.workstation.co.uk:8443/health`

## Files Modified

This setup modifies:
- `nginx/ssl/dev000.workstation.co.uk.crt` (new)
- `nginx/ssl/dev000.workstation.co.uk.key` (new)
- `nginx/nginx.conf` (domain and cert paths)
- `.env` (base URL)
- `/etc/hosts` (domain resolution)

All original files are backed up with timestamp suffixes.

## Quick Commands Reference

```bash
# Install with script
./install-production-certs.sh /path/to/certs

# Restart nginx
docker-compose restart nginx

# Test HTTPS
curl -k https://dev000.workstation.co.uk:8443/health

# View certificate
openssl x509 -in nginx/ssl/dev000.workstation.co.uk.crt -noout -text

# Check nginx logs
docker logs webimpetus-nginx

# Verify certificate in use
echo | openssl s_client -connect dev000.workstation.co.uk:8443 2>/dev/null | openssl x509 -noout -subject
```

## Status

- Domain: **dev000.workstation.co.uk**
- Port: **8443** (HTTPS)
- HTTP Port: **8888** (redirects to HTTPS)
- Certificate: **Production certificate (to be installed)**
- Environment: **Development**

Ready to install your production certificates!
