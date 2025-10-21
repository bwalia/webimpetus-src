# Nginx Unified Gateway Setup

## Overview

This configuration sets up Nginx as a unified reverse proxy for all services in the Webimpetus stack, accessible through a **single domain** on port 80.

## Architecture

```
                    ┌─────────────────────┐
                    │                     │
                    │  Nginx Gateway      │
                    │  Port 80/443        │
                    │  172.178.0.10       │
                    │                     │
                    └──────────┬──────────┘
                               │
          ┌────────────────────┼────────────────────┐
          │                    │                    │
    ┌─────▼─────┐      ┌──────▼──────┐      ┌─────▼─────┐
    │ Webimpetus│      │  Keycloak   │      │   MinIO   │
    │  :80      │      │    :8080    │      │ API :9000 │
    │ Root /    │      │ /auth/      │      │ /minio/   │
    └───────────┘      └─────────────┘      └───────────┘
          │                    │                    │
    ┌─────▼─────┐              │             ┌──────▼──────┐
    │  Adminer  │              │             │MinIO Console│
    │   :8080   │              │             │   :9001     │
    │ /adminer/ │              │             │/minio-console/
    └───────────┘              │             └─────────────┘
                               │
                        ┌──────▼──────┐
                        │ MariaDB     │
                        │   :3306     │
                        │ (internal)  │
                        └─────────────┘
```

## Service URL Mapping

All services accessible via **http://localhost** (or your domain):

| Service | URL Path | Backend | Purpose |
|---------|----------|---------|---------|
| **Webimpetus** | `/` | webimpetus-dev:80 | Main application (CodeIgniter 4) |
| **Adminer** | `/adminer/` | webimpetus-admin:8080 | Database administration UI |
| **Keycloak** | `/auth/` | keycloak:8080 | SSO authentication server |
| **MinIO API** | `/minio/` | webimpetus-minio:9000 | S3-compatible storage API |
| **MinIO Console** | `/minio-console/` | webimpetus-minio:9001 | MinIO web interface |
| **Health Check** | `/health` | nginx | Gateway health status |

## Files Created

### 1. Nginx Configuration
**[nginx/nginx.conf](nginx/nginx.conf)**
- Main reverse proxy configuration
- Upstream definitions for all services
- Path-based routing
- WebSocket support
- Large file upload support (100MB max)

### 2. Docker Compose Update
**[docker-compose.yml](docker-compose.yml#L120-L138)**
- Added nginx service (#6)
- Exposed ports 80 and 443
- Mounted nginx.conf as read-only
- Log persistence in `./nginx/logs`

## Key Features

### 1. Single Entry Point
All services accessible through one domain/IP:
```bash
# Before (multiple ports):
http://localhost:5500      # Webimpetus
http://localhost:5502      # Adminer
http://localhost:3010      # Keycloak
http://localhost:9000      # MinIO API
http://localhost:9001      # MinIO Console

# After (single port):
http://localhost           # Webimpetus
http://localhost/adminer/  # Adminer
http://localhost/auth/     # Keycloak
http://localhost/minio/    # MinIO API
http://localhost/minio-console/  # MinIO Console
```

### 2. WebSocket Support
Enabled for:
- Main application (CodeIgniter)
- MinIO Console (real-time updates)

### 3. Large File Uploads
- Client max body size: **100MB**
- Buffering disabled for MinIO uploads
- Request buffering disabled

### 4. Compression
Gzip enabled for:
- Text files (HTML, CSS, JS, JSON)
- Application data (JSON, XML)
- Fonts (TrueType, OpenType, SVG)

## Deployment Steps

### Step 1: Stop Existing Services
```bash
cd /home/bwalia/workstation-ci4
docker-compose down
```

### Step 2: Update Port Conflicts (Optional)
Since nginx will use port 80, you may want to remove direct port exposure from individual services (or keep them for direct access during development):

**Current ports** (can keep for direct access):
- 5500 → Webimpetus (still accessible)
- 5502 → Adminer (still accessible)
- 3010 → Keycloak (still accessible)
- 9000/9001 → MinIO (still accessible)

**Or remove** from docker-compose.yml to force all traffic through nginx.

### Step 3: Start All Services
```bash
docker-compose up -d
```

### Step 4: Verify Nginx Started
```bash
docker ps | grep nginx
docker logs webimpetus-nginx
```

### Step 5: Test All Endpoints

```bash
# Main app
curl http://localhost/

# Adminer
curl http://localhost/adminer/

# Keycloak
curl http://localhost/auth/

# MinIO API (list buckets)
curl http://localhost/minio/

# Health check
curl http://localhost/health
```

## Configuration Details

### Upstream Definitions
```nginx
upstream webimpetus_backend {
    server webimpetus-dev:80;
}

upstream adminer_backend {
    server webimpetus-admin:8080;
}

upstream keycloak_backend {
    server keycloak:8080;
}

upstream minio_api {
    server webimpetus-minio:9000;
}

upstream minio_console {
    server webimpetus-minio:9001;
}
```

### Proxy Headers
All locations include standard headers:
```nginx
proxy_set_header Host $host;
proxy_set_header X-Real-IP $remote_addr;
proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
proxy_set_header X-Forwarded-Proto $scheme;
```

### Path Rewriting
MinIO paths are rewritten to remove the prefix:
```nginx
# Request: http://localhost/minio/webimpetus/file.png
# Rewrites to: http://webimpetus-minio:9000/webimpetus/file.png

location /minio/ {
    rewrite ^/minio/(.*) /$1 break;
    proxy_pass http://minio_api;
}
```

## Updating Application URLs

### 1. Update .env Base URL
```bash
# .env
app.baseURL = 'http://localhost/'
```

### 2. Update MinIO Endpoint (if using proxy)
If you want to access MinIO through nginx instead of direct:
```bash
# .env (option 1 - direct access, current)
amazons3.endpoint='http://172.178.0.1:9000'

# .env (option 2 - through nginx proxy)
amazons3.endpoint='http://localhost/minio'
```

**Note**: Direct access (option 1) is recommended for internal container communication.

## SSL/HTTPS Setup (Future)

To add HTTPS support:

1. **Generate certificates** (Let's Encrypt or self-signed)
2. **Update nginx.conf**:
```nginx
server {
    listen 443 ssl http2;
    ssl_certificate /etc/nginx/ssl/cert.pem;
    ssl_certificate_key /etc/nginx/ssl/key.pem;
    # ... rest of config
}
```

3. **Mount certificate volume**:
```yaml
volumes:
  - ./nginx/ssl:/etc/nginx/ssl:ro
```

## Monitoring

### Access Logs
```bash
tail -f nginx/logs/access.log
```

### Error Logs
```bash
tail -f nginx/logs/error.log
```

### Container Logs
```bash
docker logs -f webimpetus-nginx
```

## Troubleshooting

### Issue: Port 80 Already in Use
```bash
# Check what's using port 80
sudo lsof -i :80

# Stop conflicting service
sudo systemctl stop apache2  # or nginx, etc.
```

### Issue: 502 Bad Gateway
**Cause**: Backend service not running

**Solution**:
```bash
# Check backend status
docker ps

# Check nginx logs
docker logs webimpetus-nginx

# Restart specific service
docker-compose restart webimpetus
```

### Issue: Large File Upload Fails
**Cause**: Client body size limit

**Solution**: Already set to 100MB in nginx.conf
```nginx
client_max_body_size 100M;
```

To increase further, edit [nginx/nginx.conf](nginx/nginx.conf#L27).

## Network Configuration

**Network**: `webimpetus-network` (172.178.0.0/16)

**Service IPs**:
- webimpetus-dev: 172.178.0.8
- nginx: 172.178.0.10
- keycloak: 172.178.0.11
- minio: 172.178.0.12

## Benefits

✅ **Single domain** - No need to remember multiple ports
✅ **Simplified deployment** - One entry point for all services
✅ **Load balancing ready** - Easy to add multiple backend instances
✅ **SSL termination** - Add HTTPS at gateway level
✅ **Centralized logging** - All requests logged in one place
✅ **Security** - Hide internal service ports from external access
✅ **Path-based routing** - Clean URLs for different services

## Status
✅ **READY** - Configuration created, ready to deploy with `docker-compose up -d`
