# ✅ Grafana Dashboards Successfully Imported!

## 📊 Available NGINX Monitoring Dashboards

### 1. NGINX Prometheus Exporter (External NGINX)
- **Dashboard ID**: 12708
- **UID**: MsjffzSZz
- **URL**: http://localhost:3000/d/MsjffzSZz/nginx-prometheus-exporter
- **Purpose**: Monitor external NGINX server at `test-opsapi.workstation.co.uk`
- **Key Metrics**: Connections, requests, server status

### 2. NGINX Ingress Controller (Request Handling Performance)
- **Dashboard ID**: 9614
- **UID**: nginx
- **URL**: http://localhost:3000/d/nginx
- **Purpose**: Monitor Kubernetes ingress-nginx-controller request performance
- **Key Metrics**: Request rates, latency, response codes

### 3. NGINX Ingress Controller Metrics
- **Dashboard ID**: 11875
- **UID**: oi2C0FFiz
- **URL**: http://localhost:3000/d/oi2C0FFiz
- **Purpose**: Comprehensive ingress-nginx-controller monitoring
- **Key Metrics**: SSL certificates, upstream health, configuration reload status

## 🔑 Grafana Access Information

- **URL**: http://localhost:3000
- **Username**: admin
- **Password**: `UZu1dD0yee4tmFJF2dYJrjHhcei6K4xfukguweW4`

## 🎯 Dashboard Usage Guide

### For External NGINX (test-opsapi.workstation.co.uk):
1. Open: http://localhost:3000/d/MsjffzSZz/nginx-prometheus-exporter
2. Set instance filter to: `test-opsapi.workstation.co.uk`
3. Monitor: connections, request rates, server status

### For Kubernetes Ingress-NGINX Controller:
1. **Performance Dashboard**: http://localhost:3000/d/nginx
   - Request handling performance
   - Latency percentiles
   - Error rates by ingress

2. **Comprehensive Dashboard**: http://localhost:3000/d/oi2C0FFiz
   - SSL certificate expiration
   - Upstream backend health
   - Configuration reload status
   - Resource utilization

## 📈 Key Metrics to Watch

### External NGINX (test-opsapi.workstation.co.uk):
```
nginx_up                          - Server status (1=up, 0=down)
nginx_connections_active          - Current active connections
nginx_http_requests_total         - Total HTTP requests
nginx_connections_waiting         - Idle keepalive connections
```

### Ingress-NGINX Controller:
```
nginx_ingress_controller_requests               - Request counts by ingress
nginx_ingress_controller_request_duration_seconds - Request latency
nginx_ingress_controller_ssl_expire_time_seconds  - SSL cert expiration
nginx_ingress_controller_config_last_reload_successful - Config status
```

## 🔧 Dashboard Customization Tips

### Time Range Settings:
- Default: Last 1 hour
- Recommended for troubleshooting: Last 15 minutes
- Recommended for capacity planning: Last 7 days

### Variables to Set:
1. **External NGINX Dashboard**:
   - Instance: `test-opsapi.workstation.co.uk`
   - Refresh: 30s-1m

2. **Ingress Controller Dashboards**:
   - Namespace: `ingress-nginx`
   - Controller: Select your controller
   - Ingress: Filter specific ingresses

### Panel Customization:
- Click on any panel title → Edit
- Modify queries, visualization type, thresholds
- Add alerts by clicking "Alert" tab

## 🚨 Recommended Alerts

### External NGINX Alerts:
```yaml
# Server Down
nginx_up{instance="test-opsapi.workstation.co.uk"} == 0

# High Connection Count
nginx_connections_active{instance="test-opsapi.workstation.co.uk"} > 1000

# Request Rate Spike
rate(nginx_http_requests_total{instance="test-opsapi.workstation.co.uk"}[5m]) > 100
```

### Ingress Controller Alerts:
```yaml
# High Error Rate
rate(nginx_ingress_controller_requests{status=~"5.."}[5m]) / 
rate(nginx_ingress_controller_requests[5m]) > 0.05

# SSL Certificate Expiring Soon
nginx_ingress_controller_ssl_expire_time_seconds < (time() + 7*24*3600)

# High Latency
histogram_quantile(0.95, 
  rate(nginx_ingress_controller_request_duration_seconds_bucket[5m])) > 1
```

## 📁 Created Files Summary

| File | Purpose |
|------|---------|
| `import-grafana-dashboard.sh` | Manual import instructions |
| `import-grafana-dashboard-api.sh` | Original API import script |
| `import-dashboard-simple.sh` | Working API import script |
| `import-all-nginx-dashboards.sh` | Import all NGINX dashboards |
| `GRAFANA_DASHBOARDS_SUMMARY.md` | This documentation |

## 🔄 Port Forward Management

### Start port-forward (if not running):
```bash
kubectl port-forward -n grafana svc/grafana 3000:80 &
```

### Stop port-forward:
```bash
pkill -f "port-forward.*grafana"
```

### Check if running:
```bash
curl -s http://localhost:3000/api/health
```

## 🎨 Dashboard Organization

### Recommended Folder Structure in Grafana:
```
📁 NGINX Monitoring/
├── 📊 External NGINX (test-opsapi)
├── 📊 Ingress Controller - Performance
└── 📊 Ingress Controller - Metrics

📁 Kubernetes/
├── 📊 Cluster Overview
├── 📊 Node Metrics
└── 📊 Pod Metrics
```

### To Create Folders:
1. Go to Dashboards → Browse
2. Click "New" → "Folder"
3. Name: "NGINX Monitoring"
4. Drag dashboards into folder

## 🔍 Troubleshooting

### Dashboard Not Loading Data:
1. Check if Prometheus is running:
   ```bash
   kubectl get pods -n prometheus-operator-system | grep prometheus
   ```

2. Verify data source connection in Grafana:
   - Go to Configuration → Data Sources
   - Test connection to Prometheus

3. Check if metrics are being collected:
   ```bash
   # External NGINX metrics
   kubectl run test -n monitoring --image=curlimages/curl:latest --rm -i --restart=Never -- \
     curl -s http://nginx-exporter-test-opsapi:9113/metrics | grep nginx_up
   
   # Ingress controller metrics
   kubectl exec -n ingress-nginx <ingress-pod> -- curl -s http://localhost:10254/metrics | grep nginx_ingress
   ```

### Dashboard Import Failed:
1. Check Grafana logs:
   ```bash
   kubectl logs -n grafana -l app.kubernetes.io/name=grafana
   ```

2. Re-import manually:
   - Go to Dashboards → Import
   - Use dashboard IDs: 12708, 9614, 11875

## 🚀 Next Steps

1. **Customize dashboards** for your specific needs
2. **Set up alerting** for critical metrics
3. **Create additional panels** for business-specific metrics
4. **Share dashboards** with your team
5. **Set up automated snapshots** for reporting

## 📊 Dashboard URLs Quick Reference

```bash
# External NGINX (test-opsapi.workstation.co.uk)
open http://localhost:3000/d/MsjffzSZz/nginx-prometheus-exporter

# Ingress Controller Performance
open http://localhost:3000/d/nginx

# Ingress Controller Comprehensive
open http://localhost:3000/d/oi2C0FFiz

# Grafana Home
open http://localhost:3000
```

---
**Status**: ✅ All dashboards imported and ready to use!  
**Created**: October 5, 2025  
**Grafana**: http://localhost:3000  
**Total Dashboards**: 3 NGINX monitoring dashboards