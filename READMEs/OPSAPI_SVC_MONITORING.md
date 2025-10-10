# Internal NGINX Service Monitoring - opsapi-svc

## Overview
Added monitoring for the internal Kubernetes service `opsapi-svc.test.svc.cluster.local` which exposes NGINX metrics at `/metrics` endpoint.

## Service Details
- **Service Name**: `opsapi-svc`
- **Namespace**: `test`
- **Internal URL**: `http://opsapi-svc.test.svc.cluster.local/metrics`
- **Port**: 80
- **Metrics Format**: Prometheus (native format)

## ServiceMonitor Configuration

### Created Resources:
- **ServiceMonitor**: `opsapi-svc-nginx-metrics` (namespace: `test`)
- **File**: `k8s-opsapi-svc-metrics.yaml`

### Configuration Details:
```yaml
Name: opsapi-svc-nginx-metrics
Namespace: test
Scrape Interval: 30 seconds
Path: /metrics
Target Port: 80
Job Label: opsapi-svc-nginx
Instance Label: opsapi-svc.test.svc.cluster.local
```

## Available Metrics

The opsapi-svc exposes comprehensive NGINX metrics including:

| Metric | Type | Description |
|--------|------|-------------|
| `nginx_http_4xx_errors_total` | Counter | Number of 4xx client errors by host/status/endpoint |
| `nginx_http_connections` | Gauge | Current HTTP connections by state (reading, writing, waiting) |
| `nginx_http_errors_total` | Counter | Total HTTP errors by host/status/endpoint |
| `nginx_http_request_duration_seconds` | Histogram | HTTP request latency with buckets |
| `nginx_http_requests_total` | Counter | Total HTTP requests by host/method/endpoint/status |
| `nginx_http_response_size_bytes` | Histogram | HTTP response size distribution |

## Sample Metrics Output
```
nginx_http_4xx_errors_total{host="localhost",status="401",endpoint="/health"} 2364
nginx_http_connections{state="reading"} 0
nginx_http_connections{state="waiting"} 1
nginx_http_connections{state="writing"} 1
nginx_http_request_duration_seconds_bucket{host="localhost",method="GET",endpoint="/health",le="0.005"} 2364
nginx_http_requests_total{host="localhost",method="GET",endpoint="/health",status="401"} 2364
```

## Verification Commands

### Check ServiceMonitor:
```bash
kubectl get servicemonitor -n test opsapi-svc-nginx-metrics
```

### Test metrics endpoint:
```bash
kubectl run test-opsapi --image=curlimages/curl:latest --rm -i --restart=Never -- \
  curl -s http://opsapi-svc.test.svc.cluster.local/metrics | head -20
```

### Check service details:
```bash
kubectl get svc -n test opsapi-svc -o yaml
```

### Verify Prometheus discovery:
```bash
kubectl logs -n prometheus-operator-system prometheus-kube-prometheus-stack-prometheus-0 -c prometheus | grep opsapi
```

## Prometheus Queries

### Service health check:
```promql
up{job="opsapi-svc-nginx", instance="opsapi-svc.test.svc.cluster.local"}
```

### Request rate:
```promql
rate(nginx_http_requests_total{instance="opsapi-svc.test.svc.cluster.local"}[5m])
```

### Error rate (4xx and 5xx):
```promql
rate(nginx_http_4xx_errors_total{instance="opsapi-svc.test.svc.cluster.local"}[5m]) +
rate(nginx_http_5xx_errors_total{instance="opsapi-svc.test.svc.cluster.local"}[5m])
```

### Request latency (95th percentile):
```promql
histogram_quantile(0.95, 
  rate(nginx_http_request_duration_seconds_bucket{instance="opsapi-svc.test.svc.cluster.local"}[5m])
)
```

### Active connections:
```promql
nginx_http_connections{instance="opsapi-svc.test.svc.cluster.local"}
```

### Health endpoint errors:
```promql
nginx_http_4xx_errors_total{endpoint="/health", instance="opsapi-svc.test.svc.cluster.local"}
```

## Grafana Dashboards

This service can be monitored using existing NGINX dashboards:

### Dashboard Compatibility:
- ✅ **Dashboard 12708** - NGINX Prometheus Exporter
- ✅ **Custom panels** - Create specific panels for this service

### Dashboard Filters:
- **Instance**: `opsapi-svc.test.svc.cluster.local`
- **Job**: `opsapi-svc-nginx`
- **Namespace**: `test`

## Alerting Examples

### Service Down Alert:
```yaml
- alert: OpsApiServiceDown
  expr: up{job="opsapi-svc-nginx", instance="opsapi-svc.test.svc.cluster.local"} == 0
  for: 1m
  labels:
    severity: critical
  annotations:
    summary: "OpsAPI service is down"
    description: "The opsapi-svc service has been down for more than 1 minute."
```

### High Error Rate Alert:
```yaml
- alert: OpsApiHighErrorRate
  expr: |
    rate(nginx_http_4xx_errors_total{instance="opsapi-svc.test.svc.cluster.local"}[5m]) > 10
  for: 3m
  labels:
    severity: warning
  annotations:
    summary: "High 4xx error rate on opsapi-svc"
    description: "opsapi-svc is experiencing {{ $value }} 4xx errors per second."
```

### Health Endpoint Issues:
```yaml
- alert: OpsApiHealthCheckFailing
  expr: |
    increase(nginx_http_4xx_errors_total{endpoint="/health", instance="opsapi-svc.test.svc.cluster.local"}[5m]) > 5
  for: 2m
  labels:
    severity: warning
  annotations:
    summary: "Health check failures on opsapi-svc"
    description: "opsapi-svc health endpoint is returning 4xx errors."
```

## Service Information

### Service Configuration:
```yaml
apiVersion: v1
kind: Service
metadata:
  name: opsapi-svc
  namespace: test
  labels:
    app.kubernetes.io/managed-by: Helm
spec:
  type: ClusterIP
  clusterIP: 10.96.104.240
  ports:
  - port: 80
    protocol: TCP
    targetPort: 80
  selector:
    app: opsapi
```

### Deployment Details:
- **Managed by**: Helm
- **Release**: opsapi
- **Namespace**: test
- **Created**: 3+ hours ago

## Troubleshooting

### ServiceMonitor not discovered:
1. Check ServiceMonitor labels:
```bash
kubectl get servicemonitor -n test opsapi-svc-nginx-metrics -o yaml | grep release
```
Should show: `release: kube-prometheus-stack`

2. Verify service selector matches:
```bash
kubectl get svc -n test opsapi-svc -o yaml | grep -A 10 "selector:"
```

3. Check if namespace is monitored by Prometheus:
```bash
kubectl get prometheus -n prometheus-operator-system -o yaml | grep -A 5 namespaceSelector
```

### Metrics not appearing:
1. Test endpoint directly:
```bash
kubectl run test -n test --image=curlimages/curl:latest --rm -i --restart=Never -- \
  curl -s http://opsapi-svc.test.svc.cluster.local/metrics
```

2. Check Prometheus logs:
```bash
kubectl logs -n prometheus-operator-system prometheus-kube-prometheus-stack-prometheus-0 -c prometheus | grep opsapi
```

3. Verify ServiceMonitor configuration:
```bash
kubectl describe servicemonitor -n test opsapi-svc-nginx-metrics
```

## Files Created

- `k8s-opsapi-svc-metrics.yaml` - ServiceMonitor definition
- `OPSAPI_SVC_MONITORING.md` - This documentation

## Integration with Existing Monitoring

This adds a third NGINX monitoring target to your existing setup:

1. **External NGINX**: test-opsapi.workstation.co.uk
2. **Ingress Controller**: ingress-nginx-controller
3. **Internal Service**: opsapi-svc.test.svc.cluster.local ← **NEW**

All targets are now feeding into the same Prometheus instance and can be visualized together in Grafana dashboards.

---
**Status**: ✅ Active and monitoring  
**Created**: October 5, 2025  
**Target**: http://opsapi-svc.test.svc.cluster.local/metrics  
**Namespace**: test