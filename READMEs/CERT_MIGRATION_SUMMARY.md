# Certificate Migration Summary
## From Self-Signed to Production Certificate for dev000.workstation.co.uk

## Current Status: Ready for Certificate Transfer

### ✅ Completed

1. **Security Measures Implemented**
   - Added SSL certificate patterns to `.gitignore`
   - **CRITICAL**: Private keys (*.key, *.pem) will NOT be committed to Git
   - Created secure hidden directory at `~/.certs/` with 700 permissions

2. **Installation Scripts Created**
   - **[install-production-certs.sh](install-production-certs.sh)** - Automated installation
   - **[copy-certs-from-mac.sh](copy-certs-from-mac.sh)** - Transfer instructions

3. **Documentation Created**
   - **[PRODUCTION_CERT_SETUP.md](PRODUCTION_CERT_SETUP.md)** - Complete setup guide
   - **[CERT_MIGRATION_SUMMARY.md](CERT_MIGRATION_SUMMARY.md)** - This document

### 🔄 Next Steps

#### Step 1: Transfer Certificates from Mac to Linux

**Source Location (Mac):**
```
/Users/balinderwalia/Documents/Work/aws_keys/workstation-cert.*
```

**Destination (Linux):**
```
~/.certs/
```

**Transfer Methods (Choose One):**

**Option A: SCP (Recommended if on same network)**
```bash
# From your Mac terminal:
scp /Users/balinderwalia/Documents/Work/aws_keys/workstation-cert.* bwalia@172.20.0.1:~/.certs/
```

**Option B: Manual Copy-Paste**
1. On Mac: `cat /Users/balinderwalia/Documents/Work/aws_keys/workstation-cert.crt`
2. Copy the output
3. On Linux: Paste into `~/.certs/workstation-cert.crt`
4. Repeat for `.key` file

**Option C: USB Drive**
Copy files to USB → Transfer → Copy to `~/.certs/`

#### Step 2: Verify Transfer

```bash
# Check files exist
ls -lh ~/.certs/

# Should see:
# -rw-r--r-- workstation-cert.crt
# -rw------- workstation-cert.key
# (possibly workstation-cert-chain.crt)
```

#### Step 3: Install Certificates

**Automated Installation (Recommended):**
```bash
cd /home/bwalia/workerra-ci
./install-production-certs.sh ~/.certs
```

This will:
- ✓ Verify certificate and key match
- ✓ Display certificate information
- ✓ Backup existing self-signed certificate
- ✓ Install production certificates
- ✓ Update nginx.conf with new domain (dev000.workstation.co.uk)
- ✓ Update .env with new base URL
- ✓ Update /etc/hosts
- ✓ Restart nginx
- ✓ Test the setup

**Or Manual Installation:**
Follow steps in [PRODUCTION_CERT_SETUP.md](PRODUCTION_CERT_SETUP.md)

#### Step 4: Access Application

After installation, access at:
```
🔒 https://dev000.workstation.co.uk:8443/
```

**Services:**
```
Main App:       https://dev000.workstation.co.uk:8443/
Database Admin: https://dev000.workstation.co.uk:8443/adminer/
Keycloak SSO:   https://dev000.workstation.co.uk:8443/auth/
MinIO Storage:  https://dev000.workstation.co.uk:8443/minio/
Health Check:   https://dev000.workstation.co.uk:8443/health
```

## Configuration Changes

### What Will Change

| Configuration | Old Value | New Value |
|--------------|-----------|-----------|
| **Domain** | workstation.local | dev000.workstation.co.uk |
| **Certificate** | Self-signed | Production certificate |
| **SSL Warning** | Yes (self-signed) | No (valid certificate) |
| **Base URL** | https://workstation.local:8443/ | https://dev000.workstation.co.uk:8443/ |
| **/etc/hosts** | 127.0.0.1 workstation.local | 127.0.0.1 dev000.workstation.co.uk |

### Files That Will Be Modified

1. **nginx/ssl/dev000.workstation.co.uk.crt** - Production certificate (new)
2. **nginx/ssl/dev000.workstation.co.uk.key** - Private key (new)
3. **nginx/nginx.conf** - Domain and certificate paths updated
4. **.env** - Base URL updated
5. **/etc/hosts** - Domain entry added

### Files That Will Be Backed Up

All modified files will have backups created with timestamp suffixes:
- `nginx.conf.backup.1760082756`
- `.env.backup.1760082756`
- `workstation.local.crt.backup.1760082756`
- `workstation.local.key.backup.1760082756`

## Security Checklist

- [x] Private keys excluded from Git (.gitignore updated)
- [x] Secure directory created with 700 permissions (~/.certs/)
- [ ] Certificates transferred securely (pending your action)
- [ ] Certificate and key verified to match
- [ ] Old self-signed certificate backed up
- [ ] Production certificate installed
- [ ] Nginx restarted successfully
- [ ] HTTPS connection tested
- [ ] Certificate valid for dev000.workstation.co.uk

## Important Security Notes

### ⚠️ NEVER COMMIT PRIVATE KEYS TO GIT

The following patterns are now in `.gitignore`:
```
nginx/ssl/*.key
nginx/ssl/*.pem
nginx/ssl/*.crt
.certs/
*.key
*.pem
```

### Certificate File Permissions

**Correct Permissions:**
```bash
-rw-r--r-- (644)  workstation-cert.crt     # Public certificate
-rw------- (600)  workstation-cert.key     # Private key (restricted!)
```

**Set Permissions:**
```bash
chmod 644 ~/.certs/*.crt
chmod 600 ~/.certs/*.key
```

## Troubleshooting

### Issue: Certificate Not Found

**Check:**
```bash
ls -lh ~/.certs/
```

**Expected output:**
```
total 8.0K
-rw-r--r-- 1 bwalia bwalia 1.8K Oct 10 08:00 workstation-cert.crt
-rw------- 1 bwalia bwalia 1.7K Oct 10 08:00 workstation-cert.key
```

### Issue: Permission Denied

**Fix permissions:**
```bash
chmod 700 ~/.certs/
chmod 644 ~/.certs/*.crt
chmod 600 ~/.certs/*.key
```

### Issue: Certificate and Key Don't Match

**Verify they match:**
```bash
openssl x509 -noout -modulus -in ~/.certs/workstation-cert.crt | openssl md5
openssl rsa -noout -modulus -in ~/.certs/workstation-cert.key | openssl md5
```

Both should output the same MD5 hash.

### Issue: Installation Script Fails

**Check logs:**
```bash
docker logs workerra-ci-nginx
```

**Manual installation:**
Follow [PRODUCTION_CERT_SETUP.md](PRODUCTION_CERT_SETUP.md)

## Verification Commands

After installation, verify:

```bash
# 1. Certificate installed
ls -lh /home/bwalia/workerra-ci/nginx/ssl/

# 2. Nginx configuration updated
grep dev000.workstation.co.uk /home/bwalia/workerra-ci/nginx/nginx.conf

# 3. Hosts file updated
grep dev000.workstation.co.uk /etc/hosts

# 4. Nginx running
docker ps | grep workerra-ci-nginx

# 5. HTTPS working
curl -k https://dev000.workstation.co.uk:8443/health

# 6. Certificate valid
echo | openssl s_client -connect dev000.workstation.co.uk:8443 -servername dev000.workstation.co.uk 2>/dev/null | openssl x509 -noout -subject

# 7. No self-signed warning
# Open in browser: https://dev000.workstation.co.uk:8443/
```

## Quick Reference

### Your System Info
- **Hostname**: slworker00
- **IP Address**: 172.20.0.1
- **Username**: bwalia
- **Project Path**: /home/bwalia/workerra-ci
- **Cert Directory**: ~/.certs/

### Commands

```bash
# Transfer from Mac (run on Mac):
scp /Users/balinderwalia/Documents/Work/aws_keys/workstation-cert.* bwalia@172.20.0.1:~/.certs/

# Verify on Linux:
ls -lh ~/.certs/

# Set permissions:
chmod 644 ~/.certs/*.crt && chmod 600 ~/.certs/*.key

# Install:
cd /home/bwalia/workerra-ci
./install-production-certs.sh ~/.certs

# Test:
curl -k https://dev000.workstation.co.uk:8443/health

# Access in browser:
https://dev000.workstation.co.uk:8443/
```

## Timeline

1. ✅ **Completed**: Security measures and scripts created
2. **⏳ In Progress**: Waiting for certificate transfer
3. **📋 Next**: Install certificates with automated script
4. **📋 Next**: Verify and test production setup
5. **📋 Final**: Update any remaining documentation

## Support

If you encounter any issues:

1. Check this document first
2. Review [PRODUCTION_CERT_SETUP.md](PRODUCTION_CERT_SETUP.md)
3. Run verification commands above
4. Check docker logs: `docker logs workerra-ci-nginx`
5. Review nginx config: `cat nginx/nginx.conf | grep -A5 -B5 dev000`

## Status

- **Current Certificate**: Self-signed (workstation.local)
- **Target Certificate**: Production (dev000.workstation.co.uk)
- **Current Status**: ⏳ **Waiting for certificate transfer**
- **Next Action**: Transfer certificates from Mac to `~/.certs/`

---

**Ready to proceed!** Transfer your certificates and run the installation script.
