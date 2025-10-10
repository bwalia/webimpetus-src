# Complete Prometheus Monitoring Overview

## 📊 All Configured Monitoring Targets

### 1. Ingress-NGINX Controller (Internal K8s)
- **Target**: In-cluster ingress-nginx-controller
- **Metrics Port**: 10254
- **Service**: `ingress-nginx-controller-metrics` (namespace: `ingress-nginx`)
- **ServiceMonitor**: `ingress-nginx-controller` 
- **Status**: ✅ Active
- **Metrics**: Request rates, latency, SSL certs, upstream health, etc.

### 2. External NGINX - test-opsapi.workstation.co.uk
- **Target**: `https://test-opsapi.workstation.co.uk/nginx_status`
- **Exporter**: `nginx-exporter-test-opsapi` (namespace: `monitoring`)
- **Metrics Port**: 9113
- **ServiceMonitor**: `nginx-exporter-test-opsapi`
- **Status**: ✅ Active
- **Metrics**: Connections, requests, server status

## 🔍 Quick Access Commands

### View all ServiceMonitors:
```bash
kubectl get servicemonitor -A | grep -E "NAME|nginx|ingress"
```

### View all monitoring services:
```bash
kubectl get svc -n ingress-nginx ingress-nginx-controller-metrics
kubectl get svc -n monitoring nginx-exporter-test-opsapi
```

### Check all monitoring pods:
```bash
kubectl get pods -n monitoring -l app=nginx-exporter
kubectl get pods -n ingress-nginx -l app.kubernetes.io/component=controller
```

## 📈 Access Prometheus UI

```bash
kubectl port-forward -n prometheus-operator-system svc/kube-prometheus-stack-prometheus 9090:9090
```

Then open: **http://localhost:9090**

### Check Targets Page:
- URL: http://localhost:9090/targets
- Look for:
  - `serviceMonitor/ingress-nginx/ingress-nginx-controller/0`
  - `serviceMonitor/monitoring/nginx-exporter-test-opsapi/0`

## 📊 Key Metrics to Monitor

### Ingress-NGINX Controller:
```promql
# Request rate
rate(nginx_ingress_controller_requests[5m])

# 95th percentile latency
histogram_quantile(0.95, rate(nginx_ingress_controller_request_duration_seconds_bucket[5m]))

# Error rate (4xx, 5xx)
sum(rate(nginx_ingress_controller_requests{status=~"[4-5].."}[5m])) by (status)
```

### External NGINX (test-opsapi):
```promql
# Server status
nginx_up{instance="test-opsapi.workstation.co.uk"}

# Request rate
rate(nginx_http_requests_total{instance="test-opsapi.workstation.co.uk"}[5m])

# Active connections
nginx_connections_active{instance="test-opsapi.workstation.co.uk"}
```

## 📁 Documentation Files

| File | Description |
|------|-------------|
| `k8s-ingress-nginx-metrics.yaml` | Ingress-NGINX ServiceMonitor & Service |
| `INGRESS_NGINX_METRICS.md` | Ingress-NGINX detailed docs |
| `INGRESS_NGINX_METRICS_SUMMARY.md` | Ingress-NGINX quick reference |
| `k8s-nginx-external-exporter.yaml` | External NGINX exporter deployment |
| `EXTERNAL_NGINX_MONITORING.md` | External NGINX detailed docs |
| `EXTERNAL_NGINX_MONITORING_SUMMARY.md` | External NGINX quick reference |
| `MONITORING_OVERVIEW.md` | This file - complete overview |

## 🎯 Grafana Dashboards to Import

### For Ingress-NGINX Controller:
- **Dashboard ID: 9614** - Request Handling Performance
- **Dashboard ID: 11875** - Controller Metrics

### For External NGINX:
- **Dashboard ID: 12708** - NGINX Prometheus Exporter

### How to Import:
1. Access Grafana
2. Go to Dashboards → Import
3. Enter dashboard ID
4. Select Prometheus data source
5. Click Import

## 🔔 Suggested Alerts

### Ingress-NGINX Down:
```yaml
- alert: IngressNginxDown
  expr: up{job="ingress-nginx/ingress-nginx-controller-metrics"} == 0
  for: 1m
  labels:
    severity: critical
```

### External NGINX Down:
```yaml
- alert: ExternalNginxDown
  expr: nginx_up{instance="test-opsapi.workstation.co.uk"} == 0
  for: 1m
  labels:
    severity: critical
```

### High Error Rate:
```yaml
- alert: HighErrorRate
  expr: |
    sum(rate(nginx_ingress_controller_requests{status=~"5.."}[5m])) 
    / sum(rate(nginx_ingress_controller_requests[5m])) > 0.05
  for: 5m
  labels:
    severity: warning
```

## 🛠 Troubleshooting

### Metrics not appearing in Prometheus?

1. **Check ServiceMonitor labels**:
```bash
kubectl get servicemonitor -n ingress-nginx ingress-nginx-controller -o yaml | grep release
kubectl get servicemonitor -n monitoring nginx-exporter-test-opsapi -o yaml | grep release
```
Both should have: `release: kube-prometheus-stack`

2. **Check Prometheus logs**:
```bash
kubectl logs -n prometheus-operator-system prometheus-kube-prometheus-stack-prometheus-0 -c prometheus | grep -E "ingress|nginx"
```

3. **Verify endpoints are active**:
```bash
kubectl get endpoints -n ingress-nginx ingress-nginx-controller-metrics
kubectl get endpoints -n monitoring nginx-exporter-test-opsapi
```

4. **Test metrics directly**:
```bash
# Ingress-NGINX
kubectl exec -n ingress-nginx <pod-name> -- curl -s http://localhost:10254/metrics | head

# External NGINX exporter
kubectl run test -n monitoring --image=curlimages/curl:latest --rm -i --restart=Never -- \
  curl -s http://nginx-exporter-test-opsapi:9113/metrics | head
```

## 📊 Current Configuration Summary

| Component | Namespace | Service | Port | Status |
|-----------|-----------|---------|------|--------|
| Ingress-NGINX Controller | ingress-nginx | ingress-nginx-controller-metrics | 10254 | ✅ Running |
| External NGINX Exporter | monitoring | nginx-exporter-test-opsapi | 9113 | ✅ Running |
| Prometheus | prometheus-operator-system | kube-prometheus-stack-prometheus | 9090 | ✅ Running |

## 🎉 Summary

You now have comprehensive NGINX monitoring configured:

1. ✅ **Internal Kubernetes ingress-nginx-controller** with detailed request/response metrics
2. ✅ **External NGINX server** (test-opsapi.workstation.co.uk) with connection/request metrics
3. ✅ Both integrated with **Prometheus** for scraping and storage
4. ✅ Ready for **Grafana dashboards** and **alerting**

All metrics are being collected and will be available in Prometheus within 1-2 minutes!

---
**Last Updated**: October 5, 2025  
**Total Monitoring Targets**: 2 NGINX instances  
**Status**: ✅ All Active
