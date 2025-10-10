# Ingress-NGINX Prometheus Metrics - Quick Reference

## ✅ Configuration Complete

The following resources have been created to enable Prometheus metrics for ingress-nginx-controller:

### 1. Metrics Service
```yaml
Service: ingress-nginx-controller-metrics
Namespace: ingress-nginx
Port: 10254
Type: ClusterIP
```

### 2. ServiceMonitor
```yaml
Name: ingress-nginx-controller
Namespace: ingress-nginx
Label: release: kube-prometheus-stack
Scrape Interval: 30s
```

## 🔍 Quick Verification Commands

### Check if metrics are exposed:
```bash
kubectl exec -n ingress-nginx ingress-nginx-controller-6ff786c78b-5l9bs -- curl -s http://localhost:10254/metrics | head -20
```

### View the metrics service:
```bash
kubectl get svc -n ingress-nginx ingress-nginx-controller-metrics
```

### View the ServiceMonitor:
```bash
kubectl get servicemonitor -n ingress-nginx ingress-nginx-controller
```

## 📊 Access Prometheus UI

### Option 1: Port Forward
```bash
kubectl port-forward -n prometheus-operator-system svc/kube-prometheus-stack-prometheus 9090:9090
```
Then open: http://localhost:9090

### Option 2: Check from CLI
```bash
kubectl exec -n prometheus-operator-system prometheus-kube-prometheus-stack-prometheus-0 -c prometheus -- wget -qO- http://localhost:9090/api/v1/targets | jq '.data.activeTargets[] | select(.labels.job | contains("ingress"))'
```

## 🎯 Key Metrics to Monitor

| Metric | Description | Example Query |
|--------|-------------|---------------|
| `nginx_ingress_controller_requests` | Total requests | `rate(nginx_ingress_controller_requests[5m])` |
| `nginx_ingress_controller_request_duration_seconds` | Request latency | `histogram_quantile(0.95, rate(nginx_ingress_controller_request_duration_seconds_bucket[5m]))` |
| `nginx_ingress_controller_response_size` | Response size | `sum(rate(nginx_ingress_controller_response_size_sum[5m]))` |
| `nginx_ingress_controller_ssl_expire_time_seconds` | SSL expiry | `nginx_ingress_controller_ssl_expire_time_seconds` |

## 📈 Recommended Grafana Dashboards

Import these dashboard IDs in Grafana:
- **9614** - NGINX Ingress controller (Request Handling Performance)
- **11875** - NGINX Ingress Controller Metrics

## 🔧 Troubleshooting

If metrics don't appear in Prometheus:

1. **Check ServiceMonitor label:**
   ```bash
   kubectl get servicemonitor -n ingress-nginx ingress-nginx-controller -o jsonpath='{.metadata.labels.release}'
   ```
   Should return: `kube-prometheus-stack`

2. **Check Prometheus discovered the target:**
   ```bash
   kubectl logs -n prometheus-operator-system prometheus-kube-prometheus-stack-prometheus-0 -c prometheus | grep ingress-nginx
   ```

3. **Restart Prometheus if needed:**
   ```bash
   kubectl delete pod -n prometheus-operator-system prometheus-kube-prometheus-stack-prometheus-0
   ```

## 📝 Files Created

- `k8s-ingress-nginx-metrics.yaml` - Service and ServiceMonitor definitions
- `INGRESS_NGINX_METRICS.md` - Detailed documentation
- `INGRESS_NGINX_METRICS_SUMMARY.md` - This quick reference

## ✨ Next Steps

1. Wait 1-2 minutes for Prometheus to discover and start scraping the new target
2. Access Prometheus UI to verify the target is up: Status → Targets
3. Import Grafana dashboards for visualization
4. Set up alerts based on the metrics

## 🌐 Ingress Controller Details

- **Controller Pod:** ingress-nginx-controller-6ff786c78b-5l9bs
- **External IP:** 192.168.1.200
- **HTTP Port:** 80 (NodePort: 31389)
- **HTTPS Port:** 443 (NodePort: 32577)
- **Metrics Port:** 10254

---
**Created:** October 5, 2025
**Status:** ✅ Active and monitoring
