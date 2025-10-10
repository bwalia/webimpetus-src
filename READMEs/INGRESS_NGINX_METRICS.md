# Ingress-NGINX Prometheus Metrics Configuration

## Overview
This configuration enables Prometheus metrics scraping for the ingress-nginx-controller running in your Kubernetes cluster.

## What Was Configured

### 1. Metrics Service
- **Name**: `ingress-nginx-controller-metrics`
- **Namespace**: `ingress-nginx`
- **Port**: 10254 (metrics endpoint)
- **Type**: ClusterIP

This service exposes the metrics endpoint from the ingress-nginx-controller pod.

### 2. ServiceMonitor
- **Name**: `ingress-nginx-controller`
- **Namespace**: `ingress-nginx`
- **Scrape Interval**: 30 seconds
- **Metrics Path**: `/metrics`

The ServiceMonitor is a custom resource that tells Prometheus Operator to scrape metrics from the service.

## Verification

### Check if the metrics service is running:
```bash
kubectl get svc -n ingress-nginx ingress-nginx-controller-metrics
```

### Check if the ServiceMonitor exists:
```bash
kubectl get servicemonitor -n ingress-nginx
```

### Test metrics endpoint directly:
```bash
kubectl exec -n ingress-nginx <ingress-nginx-pod-name> -- curl -s http://localhost:10254/metrics
```

### Access Prometheus UI:
```bash
# Port forward to access Prometheus UI locally
kubectl port-forward -n prometheus-operator-system svc/kube-prometheus-stack-prometheus 9090:9090
```

Then open http://localhost:9090 in your browser.

### Check Prometheus targets:
In Prometheus UI, go to Status → Targets and look for `ingress-nginx` targets.

## Available Metrics

The ingress-nginx-controller exposes many useful metrics including:

- `nginx_ingress_controller_requests` - Total number of requests
- `nginx_ingress_controller_request_duration_seconds` - Request latency
- `nginx_ingress_controller_response_size` - Response sizes
- `nginx_ingress_controller_upstream_latency_seconds` - Upstream latency
- `nginx_ingress_controller_config_last_reload_successful` - Config reload status
- `nginx_ingress_controller_ssl_expire_time_seconds` - SSL certificate expiration time

## Example PromQL Queries

### Request rate per ingress:
```promql
rate(nginx_ingress_controller_requests[5m])
```

### 95th percentile latency:
```promql
histogram_quantile(0.95, sum(rate(nginx_ingress_controller_request_duration_seconds_bucket[5m])) by (le))
```

### Error rate (4xx and 5xx):
```promql
sum(rate(nginx_ingress_controller_requests{status=~"[4-5].."}[5m])) by (ingress, status)
```

### Request rate by namespace:
```promql
sum(rate(nginx_ingress_controller_requests[5m])) by (namespace)
```

## Grafana Dashboard

You can import the official ingress-nginx Grafana dashboard:
- Dashboard ID: **9614** (Request Handling Performance)
- Dashboard ID: **11875** (Controller Metrics)

To import in Grafana:
1. Go to Dashboards → Import
2. Enter dashboard ID
3. Select your Prometheus data source
4. Click Import

## Troubleshooting

### Metrics not appearing in Prometheus:

1. Check if ServiceMonitor has the correct label:
```bash
kubectl get servicemonitor -n ingress-nginx ingress-nginx-controller -o yaml | grep release
```
Should show: `release: kube-prometheus-stack`

2. Check Prometheus logs:
```bash
kubectl logs -n prometheus-operator-system prometheus-kube-prometheus-stack-prometheus-0 -c prometheus
```

3. Verify the service endpoints:
```bash
kubectl get endpoints -n ingress-nginx ingress-nginx-controller-metrics
```

### Reload Prometheus configuration:
```bash
kubectl delete pod -n prometheus-operator-system prometheus-kube-prometheus-stack-prometheus-0
```

## Files Created

- `k8s-ingress-nginx-metrics.yaml` - Contains Service and ServiceMonitor definitions

## Additional Resources

- [Ingress-NGINX Metrics Documentation](https://kubernetes.github.io/ingress-nginx/user-guide/monitoring/)
- [Prometheus Operator ServiceMonitor](https://github.com/prometheus-operator/prometheus-operator/blob/main/Documentation/user-guides/getting-started.md)
