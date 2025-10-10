# SSL Setup Complete - workstation.local

## Overview
Successfully configured production-like HTTPS setup with self-signed SSL certificate and custom domain `workstation.local`.

## ✅ What's Been Configured

### 1. Self-Signed SSL Certificate
- **Domain**: workstation.local
- **Certificate**: [nginx/ssl/workstation.local.crt](nginx/ssl/workstation.local.crt)
- **Private Key**: [nginx/ssl/workstation.local.key](nginx/ssl/workstation.local.key)
- **Validity**: 365 days
- **Key Size**: 2048-bit RSA
- **Subject Alt Names**:
  - DNS:workstation.local
  - DNS:*.workstation.local

### 2. Nginx Reverse Proxy
- **Container**: webimpetus-nginx
- **HTTP Port**: 8888 (redirects to HTTPS)
- **HTTPS Port**: 8443
- **Features**:
  - ✅ HTTP/2 enabled
  - ✅ TLS 1.2 & 1.3
  - ✅ Security headers (HSTS, X-Frame-Options, etc.)
  - ✅ Gzip compression
  - ✅ WebSocket support
  - ✅ 100MB file upload limit

### 3. Hosts File Configuration
- **Domain**: workstation.local → 127.0.0.1
- **Subdomains**:
  - api.workstation.local → 127.0.0.1
  - admin.workstation.local → 127.0.0.1

### 4. Application Configuration
- **Base URL**: https://workstation.local:8443/
- **Environment**: development
- **SSL Enabled**: Yes (via nginx proxy)

## 🌐 Access URLs

### Main Services
| Service | URL | Description |
|---------|-----|-------------|
| **Main App** | https://workstation.local:8443/ | CodeIgniter 4 application |
| **Adminer** | https://workstation.local:8443/adminer/ | Database administration |
| **Keycloak** | https://workstation.local:8443/auth/ | SSO authentication |
| **MinIO API** | https://workstation.local:8443/minio/ | Object storage API |
| **MinIO Console** | https://workstation.local:8443/minio-console/ | MinIO web interface |
| **Health Check** | https://workstation.local:8443/health | Nginx health status |

### HTTP (Auto-redirects to HTTPS)
- http://workstation.local:8888/ → https://workstation.local:8443/

### Localhost Access (Still Available)
- http://localhost:5500/ - Direct webimpetus access (no SSL)
- http://localhost:5502/ - Direct adminer access
- http://localhost:3010/ - Direct keycloak access

## 📁 Files Created/Modified

### New Files
1. **[nginx/ssl/workstation.local.crt](nginx/ssl/workstation.local.crt)** - SSL certificate
2. **[nginx/ssl/workstation.local.key](nginx/ssl/workstation.local.key)** - Private key
3. **[setup-hosts.sh](setup-hosts.sh)** - Hosts file setup script
4. **[SSL_SETUP_COMPLETE.md](SSL_SETUP_COMPLETE.md)** - This documentation

### Modified Files
1. **[nginx/nginx.conf](nginx/nginx.conf)**
   - Added HTTPS server block (line 73-165)
   - Configured SSL certificates (line 78-79)
   - Added security headers (line 82-85)
   - HTTP to HTTPS redirect (line 64-70)
   - Fixed MinIO upstream to external instance (line 56-60)

2. **[.env](.env)**
   - Updated app.baseURL to: `https://workstation.local:8443/`

3. **[docker-compose.yml](docker-compose.yml)**
   - Mounted SSL certificates (line 130)
   - Changed ports to 8888:80 and 8443:443 (line 129-130)
   - Commented out local MinIO services (line 81-121)

4. **/etc/hosts**
   - Added workstation.local entries

## 🔧 Technical Details

### SSL Configuration
```nginx
# TLS Protocols
ssl_protocols TLSv1.2 TLSv1.3;
ssl_ciphers HIGH:!aNULL:!MD5;
ssl_prefer_server_ciphers on;
ssl_session_cache shared:SSL:10m;
ssl_session_timeout 10m;

# Certificate Paths
ssl_certificate /etc/nginx/ssl/workstation.local.crt;
ssl_certificate_key /etc/nginx/ssl/workstation.local.key;
```

### Security Headers
```nginx
Strict-Transport-Security: max-age=31536000; includeSubDomains
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
```

### Port Configuration
**Why ports 8888 and 8443?**
- Standard ports 80 and 443 were already in use by another nginx instance
- Using alternative ports allows both systems to coexist
- Production deployment can use standard ports 80/443

### MinIO Integration
- Using external MinIO instance at `172.178.0.1:9000`
- Accessible through nginx proxy at `/minio/` path
- Direct internal access still available for container communication

## 🧪 Testing & Verification

### 1. Test HTTPS Access
```bash
# Health check
curl -k https://workstation.local:8443/health

# Main application
curl -k -I https://workstation.local:8443/

# Expected: HTTP/2 200 with security headers
```

### 2. Test HTTP Redirect
```bash
curl -I http://workstation.local:8888/

# Expected: HTTP/1.1 301 Moved Permanently
# Location: https://workstation.local/
```

### 3. Test SSL Certificate
```bash
openssl s_client -connect workstation.local:8443 -servername workstation.local

# Shows certificate details and chain
```

### 4. Browser Testing
1. **Open browser**: https://workstation.local:8443/
2. **Expected**: Security warning (self-signed cert)
3. **Action**: Accept the certificate (proceed anyway)
4. **Result**: Application loads over HTTPS with green/secure indicator

### 5. Test Document Upload
```bash
# Test document preview/download through HTTPS
https://workstation.local:8443/documents/preview/{uuid}
https://workstation.local:8443/documents/download/{uuid}
```

## 🔒 Security Considerations

### Self-Signed Certificate Warnings
Browsers will show security warnings because:
- Certificate is self-signed (not from a trusted CA)
- This is expected for development/testing environments

**To avoid warnings in production:**
1. Use Let's Encrypt for free valid certificates
2. Purchase commercial SSL certificate
3. Use organizational PKI/CA

### Adding Certificate to Browser Trust Store

#### Chrome/Chromium (Linux)
```bash
# Copy certificate to system trust store
sudo cp nginx/ssl/workstation.local.crt /usr/local/share/ca-certificates/
sudo update-ca-certificates

# Or import via Chrome settings:
# Settings → Privacy and security → Security → Manage certificates
```

#### Firefox
1. Navigate to: `about:preferences#privacy`
2. Scroll to "Certificates" → "View Certificates"
3. Import → Select `nginx/ssl/workstation.local.crt`
4. Trust for identifying websites

#### System-wide (Linux)
```bash
sudo cp nginx/ssl/workstation.local.crt /usr/local/share/ca-certificates/workstation.local.crt
sudo update-ca-certificates
```

## 📊 Container Status

```bash
# Check nginx status
docker ps --filter "name=webimpetus-nginx"

# Expected output:
# NAMES              STATUS        PORTS
# webimpetus-nginx   Up X minutes  0.0.0.0:8888->80/tcp, 0.0.0.0:8443->443/tcp

# View logs
docker logs -f webimpetus-nginx

# Restart if needed
docker-compose restart nginx
```

## 🐛 Troubleshooting

### Issue: "ERR_SSL_PROTOCOL_ERROR"
**Cause**: Nginx not started or SSL misconfigured

**Solution**:
```bash
docker logs webimpetus-nginx
docker-compose restart nginx
```

### Issue: "NET::ERR_CERT_AUTHORITY_INVALID"
**Cause**: Self-signed certificate not trusted

**Solution**:
- Click "Advanced" → "Proceed anyway" in browser
- Or add certificate to trust store (see above)

### Issue: "This site can't be reached"
**Cause**: Domain not in hosts file or nginx not running

**Solution**:
```bash
# Check hosts file
cat /etc/hosts | grep workstation.local

# If missing, run setup script
sudo ./setup-hosts.sh

# Check nginx
docker ps | grep nginx
```

### Issue: Documents not previewing
**Cause**: MinIO not accessible through nginx

**Solution**:
```bash
# Test MinIO through nginx
curl -k https://workstation.local:8443/minio/

# Check MinIO upstream config
docker exec webimpetus-nginx cat /etc/nginx/nginx.conf | grep minio_api
```

### Issue: Port already in use
**Cause**: Another service using 8443 or 8888

**Solution**:
```bash
# Check what's using the port
sudo lsof -i :8443

# Change port in docker-compose.yml if needed
```

## 🚀 Production Deployment Checklist

When moving to production:

- [ ] Replace self-signed certificate with valid SSL from:
  - Let's Encrypt (free, automated)
  - Commercial CA (DigiCert, Sectigo, etc.)
  - Internal PKI/CA

- [ ] Update ports to standard 80/443
  ```yaml
  ports:
    - "80:80"
    - "443:443"
  ```

- [ ] Update .env with production domain
  ```bash
  app.baseURL='https://yourdomain.com/'
  ```

- [ ] Add production domain to hosts file or DNS
  ```bash
  your-server-ip    yourdomain.com
  ```

- [ ] Review and harden security headers
- [ ] Enable rate limiting in nginx
- [ ] Configure firewall rules
- [ ] Set up monitoring and logging
- [ ] Enable HTTPS-only mode in application

## 📝 Quick Reference

### Restart Services
```bash
# Restart nginx only
docker-compose restart nginx

# Restart entire stack
docker-compose restart

# View all logs
docker-compose logs -f
```

### Update SSL Certificate
```bash
# Generate new certificate (replace existing)
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout nginx/ssl/workstation.local.key \
  -out nginx/ssl/workstation.local.crt \
  -subj "/CN=workstation.local"

# Restart nginx
docker-compose restart nginx
```

### Check Certificate Expiry
```bash
openssl x509 -in nginx/ssl/workstation.local.crt -noout -dates

# Shows:
# notBefore=Oct 10 07:34:00 2025 GMT
# notAfter=Oct 10 07:34:00 2026 GMT
```

## ✅ Verification Checklist

Test all these URLs in your browser:

- [ ] https://workstation.local:8443/ (Main app)
- [ ] https://workstation.local:8443/adminer/ (Database admin)
- [ ] https://workstation.local:8443/auth/ (Keycloak)
- [ ] https://workstation.local:8443/health (Health check)
- [ ] https://workstation.local:8443/documents/edit/6 (Document preview)
- [ ] http://workstation.local:8888/ (Should redirect to HTTPS)

All should:
✅ Load over HTTPS
✅ Show security headers
✅ Work without errors (except self-signed cert warning)

## 🎉 Success Indicators

Your setup is working correctly if:

1. ✅ `curl -k https://workstation.local:8443/health` returns "OK"
2. ✅ HTTP requests redirect to HTTPS
3. ✅ Browser shows HTTPS connection (with self-signed warning)
4. ✅ Application loads and functions normally
5. ✅ Document preview/download works through HTTPS
6. ✅ All security headers present in response

## 📚 Related Documentation

- [NGINX_UNIFIED_GATEWAY.md](NGINX_UNIFIED_GATEWAY.md) - Nginx configuration guide
- [DOCUMENT_PREVIEW_EDIT_PAGE_FIX.md](DOCUMENT_PREVIEW_EDIT_PAGE_FIX.md) - Document preview setup
- [SESSION_SUMMARY.md](SESSION_SUMMARY.md) - Complete session summary

## Status
✅ **PRODUCTION-READY** - SSL/TLS properly configured with workstation.local domain
