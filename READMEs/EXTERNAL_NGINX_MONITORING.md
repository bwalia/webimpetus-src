# External NGINX Monitoring - test-opsapi.workstation.co.uk

## Overview
This configuration adds monitoring for the external NGINX endpoint at `https://test-opsapi.workstation.co.uk/nginx_status` to your Prometheus monitoring stack.

## Architecture

```
External NGINX (test-opsapi.workstation.co.uk)
    ↓ (nginx_status endpoint)
NGINX Prometheus Exporter (in K8s)
    ↓ (converts to Prometheus metrics)
Prometheus (scrapes metrics)
```

## Components Deployed

### 1. NGINX Prometheus Exporter
- **Deployment**: `nginx-exporter-test-opsapi`
- **Namespace**: `monitoring`
- **Image**: `nginx/nginx-prometheus-exporter:0.11.0`
- **Function**: Scrapes the nginx_status endpoint and converts to Prometheus format

### 2. Service
- **Name**: `nginx-exporter-test-opsapi`
- **Type**: ClusterIP
- **Port**: 9113 (metrics endpoint)

### 3. ServiceMonitor
- **Name**: `nginx-exporter-test-opsapi`
- **Scrape Interval**: 30 seconds
- **Labels**: Includes `release: kube-prometheus-stack` for Prometheus discovery

## Available Metrics

The following metrics are now available in Prometheus:

| Metric | Description | Type |
|--------|-------------|------|
| `nginx_up` | NGINX server status (1=up, 0=down) | Gauge |
| `nginx_connections_active` | Current active connections | Gauge |
| `nginx_connections_accepted` | Total accepted connections | Counter |
| `nginx_connections_handled` | Total handled connections | Counter |
| `nginx_connections_reading` | Connections reading request | Gauge |
| `nginx_connections_writing` | Connections writing response | Gauge |
| `nginx_connections_waiting` | Idle keepalive connections | Gauge |
| `nginx_http_requests_total` | Total HTTP requests | Counter |

## Verification Commands

### Check exporter pod status:
```bash
kubectl get pods -n monitoring -l app=nginx-exporter
```

### View exporter logs:
```bash
kubectl logs -n monitoring -l app=nginx-exporter
```

### Test metrics endpoint:
```bash
kubectl run -n monitoring test-curl --image=curlimages/curl:latest --rm -i --restart=Never -- \
  curl -s http://nginx-exporter-test-opsapi:9113/metrics | grep nginx_
```

### Check ServiceMonitor:
```bash
kubectl get servicemonitor -n monitoring nginx-exporter-test-opsapi
```

## Prometheus Queries

### Check if NGINX is up:
```promql
nginx_up{instance="test-opsapi.workstation.co.uk"}
```

### Active connections:
```promql
nginx_connections_active{instance="test-opsapi.workstation.co.uk"}
```

### Request rate (requests per second):
```promql
rate(nginx_http_requests_total{instance="test-opsapi.workstation.co.uk"}[5m])
```

### Connection acceptance rate:
```promql
rate(nginx_connections_accepted{instance="test-opsapi.workstation.co.uk"}[5m])
```

### Waiting connections (keepalive):
```promql
nginx_connections_waiting{instance="test-opsapi.workstation.co.uk"}
```

## Grafana Dashboard

You can create a custom dashboard or use the NGINX exporter dashboard:
- Dashboard ID: **12708** - NGINX Prometheus Exporter

Import in Grafana:
1. Go to Dashboards → Import
2. Enter dashboard ID: 12708
3. Select your Prometheus data source
4. Adjust the `instance` filter to: `test-opsapi.workstation.co.uk`

## Alerting Examples

### Alert when NGINX is down:
```yaml
- alert: NginxDown
  expr: nginx_up{instance="test-opsapi.workstation.co.uk"} == 0
  for: 1m
  labels:
    severity: critical
  annotations:
    summary: "NGINX is down (instance {{ $labels.instance }})"
    description: "NGINX at {{ $labels.instance }} has been down for more than 1 minute."
```

### Alert on high connection count:
```yaml
- alert: NginxHighConnections
  expr: nginx_connections_active{instance="test-opsapi.workstation.co.uk"} > 1000
  for: 5m
  labels:
    severity: warning
  annotations:
    summary: "High number of active connections"
    description: "NGINX at {{ $labels.instance }} has {{ $value }} active connections."
```

## Troubleshooting

### Exporter not starting:
```bash
# Check pod events
kubectl describe pod -n monitoring -l app=nginx-exporter

# Check logs
kubectl logs -n monitoring -l app=nginx-exporter
```

### Metrics not appearing in Prometheus:

1. Check if ServiceMonitor has correct label:
```bash
kubectl get servicemonitor -n monitoring nginx-exporter-test-opsapi -o yaml | grep release
```
Should show: `release: kube-prometheus-stack`

2. Verify the service endpoints:
```bash
kubectl get endpoints -n monitoring nginx-exporter-test-opsapi
```

3. Check Prometheus targets:
```bash
kubectl port-forward -n prometheus-operator-system svc/kube-prometheus-stack-prometheus 9090:9090
```
Then go to: http://localhost:9090/targets

### Test endpoint manually:
```bash
curl -k https://test-opsapi.workstation.co.uk/nginx_status
```

## Configuration Details

### Exporter Arguments:
- `-nginx.scrape-uri=https://test-opsapi.workstation.co.uk/nginx_status` - Target URL
- `-nginx.ssl-verify=false` - Skip SSL verification (use with caution in production)

### Resource Limits:
- **Requests**: 50m CPU, 32Mi memory
- **Limits**: 100m CPU, 64Mi memory

## Files Created

- `k8s-nginx-external-exporter.yaml` - Complete deployment configuration
- `EXTERNAL_NGINX_MONITORING.md` - This documentation

## Security Notes

⚠️ **Important**: The current configuration disables SSL verification (`-nginx.ssl-verify=false`). 

For production environments, consider:
1. Use valid SSL certificates
2. Enable SSL verification
3. Use secrets for authentication if required
4. Implement network policies to restrict access

## Maintenance

### Update exporter version:
```bash
kubectl set image deployment/nginx-exporter-test-opsapi \
  nginx-exporter=nginx/nginx-prometheus-exporter:0.11.0 \
  -n monitoring
```

### Scale replicas:
```bash
kubectl scale deployment nginx-exporter-test-opsapi --replicas=2 -n monitoring
```

### Delete the exporter:
```bash
kubectl delete -f k8s-nginx-external-exporter.yaml
```

---
**Created**: October 5, 2025  
**Target**: https://test-opsapi.workstation.co.uk/nginx_status  
**Status**: ✅ Active and monitoring
