# ✅ Loki Log Aggregation Setup Complete!

## 🎯 **What Was Accomplished:**

Successfully deployed **Loki stack** for log aggregation and configured **NGINX log collection** for the opsapi deployment in Kubernetes.

### 📊 **Components Deployed:**

#### 1. 🗄️ **Loki (Log Aggregation Server)**
- **Namespace**: `loki-stack`
- **Service**: `loki.loki-stack.svc.cluster.local:3100`
- **Storage**: Filesystem (local for demo/testing)
- **Status**: ✅ Running and receiving logs

#### 2. 📡 **Promtail (Log Collector)**
- **Type**: DaemonSet (runs on all nodes)
- **Namespace**: `loki-stack`
- **Function**: Collects Kubernetes pod logs and sends to Loki
- **Status**: ✅ Running on all nodes

#### 3. 📋 **Grafana Integration**
- **Data Source**: Loki added to Grafana
- **URL**: `http://loki.loki-stack.svc.cluster.local:3100`
- **Status**: ✅ Ready for log visualization

## 🔍 **NGINX Logs Being Collected:**

### opsapi NGINX Logs:
- **Deployment**: `opsapi` (namespace: `test`)
- **Container**: OpenResty/NGINX
- **Access Logs**: `/var/log/nginx/access.log` ✅
- **Error Logs**: `/var/log/nginx/error.log` ✅
- **Status**: Logs flowing to Loki

### Sample Log Data Available:
```
App: opsapi
Namespace: test
Log Types: nginx_access, nginx_error, container
Labels: method, status, level, remote_addr
```

## 🌐 **Access Information:**

### Grafana (Log Visualization):
```bash
# If not already running:
kubectl port-forward -n grafana svc/grafana 3000:80 &

# Access: http://localhost:3000
# Username: admin  
# Password: [Retrieved from K8s secret]
```

### Loki (Direct API Access):
```bash
kubectl port-forward -n loki-stack svc/loki 3100:3100 &
# API: http://localhost:3100
```

## 📊 **Log Queries in Grafana:**

### Basic Queries:

#### All opsapi logs:
```logql
{app="opsapi"}
```

#### NGINX access logs only:
```logql
{app="opsapi", log_type="nginx_access"}
```

#### NGINX error logs only:
```logql
{app="opsapi", log_type="nginx_error"}
```

#### Filter by HTTP status:
```logql
{app="opsapi", status="200"}
```

#### Filter by HTTP method:
```logql
{app="opsapi", method="GET"}
```

#### Recent health check requests:
```logql
{app="opsapi"} |= "health"
```

#### Recent error logs:
```logql
{app="opsapi", log_type="nginx_error"}
```

### Advanced Queries:

#### Rate of requests per minute:
```logql
rate({app="opsapi", log_type="nginx_access"}[1m])
```

#### Count of 4xx errors:
```logql
count_over_time({app="opsapi", status=~"4.."}[5m])
```

#### Count of requests by status:
```logql
sum by (status) (count_over_time({app="opsapi", log_type="nginx_access"}[5m]))
```

## 🎨 **Creating Dashboards:**

### In Grafana:
1. **Add Log Panel**:
   - Go to Dashboard → Add Panel
   - Select "Logs" visualization
   - Choose "Loki" data source
   - Enter query: `{app="opsapi"}`

2. **Create Metrics from Logs**:
   - Panel Type: "Stat" or "Time Series"
   - Query: `rate({app="opsapi", log_type="nginx_access"}[1m])`
   - Title: "NGINX Request Rate"

3. **Status Code Distribution**:
   - Panel Type: "Pie Chart"
   - Query: `sum by (status) (count_over_time({app="opsapi", status!=""}[5m]))`

## 📁 **Files Created:**

| File | Purpose |
|------|---------|
| `k8s-loki-stack.yaml` | Complete Loki + Promtail deployment |
| `k8s-promtail-enhanced.yaml` | Enhanced Promtail configuration |
| `add-loki-datasource.sh` | Script to add Loki to Grafana |
| `LOKI_SETUP_COMPLETE.md` | This documentation |

## 🔧 **Management Commands:**

### Check Loki status:
```bash
kubectl get pods -n loki-stack
```

### View Loki logs:
```bash
kubectl logs -n loki-stack deployment/loki
```

### Check Promtail status:
```bash
kubectl get pods -n loki-stack -l app=promtail
```

### View Promtail logs:
```bash
kubectl logs -n loki-stack daemonset/promtail
```

### Test opsapi logs:
```bash
# Generate some requests to create logs
kubectl run test-nginx --image=curlimages/curl:latest --rm -i --restart=Never -- \
  curl -s http://opsapi-svc.test.svc.cluster.local/health

# Check they appear in Loki
curl -s "http://localhost:3100/loki/api/v1/query?query={app=\"opsapi\"}&limit=5"
```

## 🚨 **Alerting on Logs:**

### Sample Alerting Rules:
```yaml
# High error rate from logs
- alert: HighNginxErrorRate
  expr: |
    rate({app="opsapi", status=~"5.."}[5m]) > 0.1
  for: 2m
  annotations:
    summary: "High error rate in opsapi NGINX logs"

# No logs received (service down)
- alert: OpsapiNoLogs
  expr: |
    absent_over_time({app="opsapi"}[5m])
  for: 2m
  annotations:
    summary: "No logs from opsapi service"
```

## 🔍 **Troubleshooting:**

### Logs not appearing in Loki:
1. **Check Promtail status**:
   ```bash
   kubectl get pods -n loki-stack -l app=promtail
   kubectl logs -n loki-stack daemonset/promtail
   ```

2. **Check Loki connectivity**:
   ```bash
   kubectl exec -n loki-stack deployment/loki -- wget -qO- http://localhost:3100/ready
   ```

3. **Test log generation**:
   ```bash
   kubectl exec -n test opsapi-76b7b44cc6-78l9x -- tail -f /var/log/nginx/access.log
   ```

### Grafana not showing logs:
1. **Check Loki data source**:
   - Go to Configuration → Data Sources → Loki
   - Test connection

2. **Verify queries**:
   - Start with simple query: `{app="opsapi"}`
   - Check time range (last 15 minutes)

## 🎯 **Current Monitoring Stack:**

### **Metrics** (Prometheus + Grafana):
- ✅ Ingress-NGINX Controller metrics
- ✅ External NGINX metrics (test-opsapi.workstation.co.uk)  
- ✅ Internal opsapi-svc metrics

### **Logs** (Loki + Grafana): ← **NEW**
- ✅ opsapi NGINX access logs
- ✅ opsapi NGINX error logs
- ✅ Kubernetes container logs
- ✅ Structured log parsing

### **Visualization** (Grafana):
- ✅ 4 NGINX metric dashboards
- ✅ Loki data source configured
- ✅ Ready for log dashboards

## 🚀 **Next Steps:**

1. **✅ Completed**: Loki stack deployed and collecting logs
2. **🎨 Create**: Custom log dashboards in Grafana
3. **🚨 Set up**: Log-based alerting rules
4. **📊 Analyze**: Historical log data for insights
5. **🔧 Scale**: Adjust log retention and storage as needed

## 🏆 **Achievement Summary:**

You now have **complete observability** for your NGINX infrastructure:

- **📊 Metrics**: Real-time performance monitoring
- **📋 Logs**: Detailed request/error analysis  
- **🎨 Visualization**: Unified dashboard view
- **🚨 Alerting**: Proactive issue detection

**Total Log Sources**: opsapi NGINX (access + error logs)  
**Log Aggregation**: ✅ Loki collecting and storing  
**Visualization**: ✅ Grafana ready for log analysis  
**Integration**: ✅ Full observability stack operational

---
**Status**: 🎉 **Complete and operational!**  
**Created**: October 5, 2025  
**Loki**: http://loki.loki-stack.svc.cluster.local:3100  
**Grafana**: http://localhost:3000