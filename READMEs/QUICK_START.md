# Quick Start Guide - workerra-ci HTTPS

## 🚀 Access Your Application

### Main URL
```
🔒 https://workstation.local:8443/
```

**First Time?**
1. Open the URL in your browser
2. Click **"Advanced"** when you see the security warning
3. Click **"Proceed to workstation.local"**
4. ✅ You're in!

### All Services
```
Main App:       https://workstation.local:8443/
Database Admin: https://workstation.local:8443/adminer/
SSO/Auth:       https://workstation.local:8443/auth/
File Storage:   https://workstation.local:8443/minio/
Health Check:   https://workstation.local:8443/health
```

## ⚙️ Container Management

### Start Services
```bash
docker-compose up -d
```

### Stop Services
```bash
docker-compose down
```

### Restart Nginx
```bash
docker-compose restart nginx
```

### View Logs
```bash
# Nginx
docker logs -f workerra-ci-nginx

# Application
docker logs -f workerra-ci-dev

# All services
docker-compose logs -f
```

## 🔍 Quick Tests

### Test HTTPS
```bash
curl -k https://workstation.local:8443/health
# Expected: OK
```

### Run All Tests
```bash
./test-ssl-setup.sh
```

## 🐛 Common Issues

### "Can't connect"
```bash
# Check nginx is running
docker ps | grep nginx

# Restart if needed
docker-compose restart nginx
```

### "Security warning"
**Normal!** Self-signed certificate.
- Click "Advanced" → "Proceed anyway"

### "404 Not Found"
```bash
# Check application is running
docker ps | grep workerra-ci-dev

# Restart application
docker-compose restart workerra-ci
```

## 📁 Important Files

```
nginx/nginx.conf              - Nginx configuration
nginx/ssl/                    - SSL certificates
.env                          - Application config
docker-compose.yml            - Container setup
SSL_SETUP_COMPLETE.md         - Detailed docs
```

## 🔐 Credentials

Use your existing login credentials. SSL doesn't change authentication.

## 📚 Full Documentation

- **[FINAL_SETUP_SUMMARY.md](FINAL_SETUP_SUMMARY.md)** - Complete overview
- **[SSL_SETUP_COMPLETE.md](SSL_SETUP_COMPLETE.md)** - SSL details
- **[NGINX_UNIFIED_GATEWAY.md](NGINX_UNIFIED_GATEWAY.md)** - Nginx guide

## ✅ Status

- ✅ SSL Certificate: Valid (365 days)
- ✅ Domain: workstation.local
- ✅ Nginx: Running on ports 8888 (HTTP) & 8443 (HTTPS)
- ✅ Security Headers: Enabled
- ✅ HTTP/2: Enabled

**Ready to use!** 🎉
