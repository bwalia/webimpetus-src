# Domain Migration Complete
## workstation.local → dev000.workstation.co.uk

## ✅ Migration Status: Complete (Using Temporary Certificates)

All system references have been updated from `workstation.local` to `dev000.workstation.co.uk`.

## What Was Changed

### 1. Nginx Configuration
**File**: [nginx/nginx.conf](nginx/nginx.conf)

**Changes**:
- Server name: `workstation.local` → `dev000.workstation.co.uk`
- Wildcard: `*.workstation.local` → `*.workstation.co.uk`
- Certificate paths updated to new domain names

**Lines Modified**:
- Line 66: HTTP server block
- Line 76: HTTPS server block
- Lines 79-80: SSL certificate paths

### 2. Application Configuration
**File**: [.env](.env)

**Changes**:
```bash
# OLD:
app.baseURL='https://workstation.local:8443/'

# NEW:
app.baseURL='https://dev000.workstation.co.uk:8443/'
```

### 3. System Configuration
**File**: `/etc/hosts`

**Changes**:
```bash
# REMOVED:
127.0.0.1    workstation.local
127.0.0.1    api.workstation.local
127.0.0.1    admin.workstation.local

# ADDED:
127.0.0.1    dev000.workstation.co.uk
```

### 4. SSL Certificates (Temporary)
**Location**: nginx/ssl/

**Files Created**:
- `dev000.workstation.co.uk.crt` (temporary self-signed)
- `dev000.workstation.co.uk.key` (temporary self-signed)

**Note**: These are TEMPORARY copies of the self-signed certificate. Replace with production certificates from:
```
/Users/balinderwalia/Documents/Work/aws_keys/workstation-cert.*
```

### 5. Setup Scripts
**Updated Scripts**:
- [setup-hosts.sh](setup-hosts.sh)
- [test-ssl-setup.sh](test-ssl-setup.sh)

All references to `workstation.local` replaced with `dev000.workstation.co.uk`.

### 6. Security Configuration
**File**: [.gitignore](.gitignore)

**Added Protection**:
```
# SSL Certificates and Private Keys - NEVER COMMIT THESE!
nginx/ssl/*.key
nginx/ssl/*.pem
nginx/ssl/*.crt
.certs/
*.key
*.pem
```

## ✅ Verification Results

All systems operational with new domain:

```
✓ Health Check:     OK
✓ HTTP Redirect:    301 → HTTPS
✓ HTTPS Connection: Working
✓ Hosts File:       Updated
✓ Nginx:            Restarted
✓ Domain:           dev000.workstation.co.uk
```

## 🌐 Access URLs (Updated)

### Primary HTTPS Access
```
Main Application:  https://dev000.workstation.co.uk:8443/
Database Admin:    https://dev000.workstation.co.uk:8443/adminer/
Keycloak SSO:      https://dev000.workstation.co.uk:8443/auth/
MinIO Storage:     https://dev000.workstation.co.uk:8443/minio/
MinIO Console:     https://dev000.workstation.co.uk:8443/minio-console/
Health Check:      https://dev000.workstation.co.uk:8443/health
```

### HTTP (Auto-redirects to HTTPS)
```
http://dev000.workstation.co.uk:8888/
```

### Direct Access (Bypassing Nginx)
```
http://localhost:5500/  (Webimpetus - no SSL)
http://localhost:5502/  (Adminer)
http://localhost:3010/  (Keycloak)
```

## ⚠️ Current Certificate Status

**Using**: Temporary self-signed certificate (copied from workstation.local)

**Certificate Info**:
```
Subject: C = US, ST = State, L = City, O = Webimpetus, CN = workstation.local
Issuer: Self-signed
Valid Until: Oct 10, 2026
```

**⚠️ Browser Warning**: You'll still see a security warning because:
1. Certificate is self-signed
2. Certificate CN (workstation.local) doesn't match domain (dev000.workstation.co.uk)

This will be resolved once production certificates are installed.

## 📋 Next Step: Install Production Certificates

### Step 1: Transfer Certificates

**From Mac** (`/Users/balinderwalia/Documents/Work/aws_keys/`):

**Option A - SCP (Recommended)**:
```bash
# From Mac terminal:
scp /Users/balinderwalia/Documents/Work/aws_keys/workstation-cert.* bwalia@172.20.0.1:~/.certs/
```

**Option B - Manual Copy**:
Follow instructions in [copy-certs-from-mac.sh](copy-certs-from-mac.sh)

### Step 2: Verify Transfer
```bash
ls -lh ~/.certs/
chmod 644 ~/.certs/*.crt
chmod 600 ~/.certs/*.key
```

### Step 3: Install Production Certificates
```bash
cd /home/bwalia/webimpetus-src
./install-production-certs.sh ~/.certs
```

This will:
- ✓ Verify certificate matches domain (dev000.workstation.co.uk)
- ✓ Backup temporary certificates
- ✓ Install production certificates
- ✓ Restart nginx
- ✓ Verify setup
- ✅ Remove browser security warnings!

## 🔧 Testing Commands

```bash
# Test HTTPS
curl -k https://dev000.workstation.co.uk:8443/health

# Test HTTP redirect
curl -I http://dev000.workstation.co.uk:8888/

# Check certificate
echo | openssl s_client -connect dev000.workstation.co.uk:8443 -servername dev000.workstation.co.uk 2>/dev/null | openssl x509 -noout -subject

# View nginx logs
docker logs webimpetus-nginx

# Restart nginx if needed
docker-compose restart nginx

# Check hosts file
grep dev000 /etc/hosts
```

## 📊 Configuration Summary

| Item | Value |
|------|-------|
| **Domain** | dev000.workstation.co.uk |
| **HTTP Port** | 8888 (redirects to HTTPS) |
| **HTTPS Port** | 8443 |
| **Certificate** | Temporary (self-signed) |
| **Base URL** | https://dev000.workstation.co.uk:8443/ |
| **Environment** | development |
| **Nginx Status** | ✓ Running |
| **Git Protection** | ✓ Certificates excluded |

## 🔐 Security Status

- ✅ **Domain Updated**: All references changed to dev000.workstation.co.uk
- ✅ **Git Protection**: Private keys excluded from commits
- ✅ **HTTPS Enforced**: HTTP auto-redirects to HTTPS
- ✅ **Security Headers**: HSTS, X-Frame-Options, etc.
- ⏳ **Production Certs**: Waiting for installation
- ✅ **Secure Storage**: ~/.certs/ directory ready (700 permissions)

## 📁 Modified Files

```
✓ nginx/nginx.conf                     - Domain and cert paths
✓ .env                                 - Base URL
✓ /etc/hosts                           - Domain resolution
✓ setup-hosts.sh                       - Setup script
✓ test-ssl-setup.sh                    - Test script
✓ .gitignore                           - Certificate protection
✓ nginx/ssl/dev000.workstation.co.uk.crt  - Temporary cert
✓ nginx/ssl/dev000.workstation.co.uk.key  - Temporary key
```

## 🚨 Important Notes

### Browser Security Warning
You'll still see a security warning until production certificates are installed:

**Warning Reasons**:
1. Certificate is self-signed (not from trusted CA)
2. Certificate CN doesn't match domain

**To Access Anyway**:
1. Click "Advanced"
2. Click "Proceed to dev000.workstation.co.uk (unsafe)"

**To Remove Warning**:
Install production certificates (see Step 3 above)

### Git Safety
Private keys are now excluded from Git. Verify:
```bash
git status
# Should NOT show *.key files
```

If you see certificate files in git status:
```bash
git rm --cached nginx/ssl/*.key
git rm --cached nginx/ssl/*.crt
```

## 📚 Documentation Files

- **[DOMAIN_MIGRATION_COMPLETE.md](DOMAIN_MIGRATION_COMPLETE.md)** - This document
- **[CERT_MIGRATION_SUMMARY.md](CERT_MIGRATION_SUMMARY.md)** - Certificate migration guide
- **[PRODUCTION_CERT_SETUP.md](PRODUCTION_CERT_SETUP.md)** - Detailed setup instructions
- **[copy-certs-from-mac.sh](copy-certs-from-mac.sh)** - Certificate transfer help
- **[install-production-certs.sh](install-production-certs.sh)** - Automated installer
- **[SSL_SETUP_COMPLETE.md](SSL_SETUP_COMPLETE.md)** - Original SSL setup docs
- **[FINAL_SETUP_SUMMARY.md](FINAL_SETUP_SUMMARY.md)** - Complete setup overview

## 🎯 Quick Start

1. **Access Application Now** (with self-signed cert warning):
   ```
   https://dev000.workstation.co.uk:8443/
   ```

2. **Install Production Certificates** (when ready):
   ```bash
   # Transfer certs, then:
   ./install-production-certs.sh ~/.certs
   ```

3. **Verify Everything Works**:
   ```bash
   curl -k https://dev000.workstation.co.uk:8443/health
   ```

## ✅ Success Checklist

Migration Complete:
- [x] Domain changed to dev000.workstation.co.uk
- [x] Nginx configuration updated
- [x] Application configuration updated
- [x] Hosts file updated
- [x] Temporary certificates created
- [x] Nginx restarted successfully
- [x] All services accessible
- [x] Git protection configured
- [ ] Production certificates installed (pending)
- [ ] Browser security warning resolved (pending prod certs)

## 🔄 Rollback (If Needed)

If you need to revert to workstation.local:

```bash
# Backups were created during migration
# Look for files with .backup suffix

# Quick rollback:
cd /home/bwalia/webimpetus-src

# Restore nginx config
cp nginx/nginx.conf.backup.[timestamp] nginx/nginx.conf

# Restore .env
cp .env.backup.[timestamp] .env

# Restore hosts
sudo cp /etc/hosts.backup /etc/hosts

# Restart nginx
docker-compose restart nginx
```

## 🎉 Status

**Migration**: ✅ **COMPLETE**
**Domain**: **dev000.workstation.co.uk**
**Status**: ✓ Operational (temporary certificates)
**Next**: Install production SSL certificates

Your application is now accessible at:
🔒 **https://dev000.workstation.co.uk:8443/**

---

**Ready for production certificate installation!**
