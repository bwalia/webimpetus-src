# MariaDB High Availability Setup Summary

## Overview

Successfully deployed MariaDB in High Availability (HA) mode using the MariaDB Operator with the following architecture:

- **MariaDB Galera Cluster**: 3-node multi-master replication
- **MaxScale**: 2-replica load balancer and connection router
- **Automatic Failover**: Enabled for high availability
- **Storage**: 10Gi per replica using local-path storage class

## Deployment Architecture

### MariaDB Galera Cluster
- **Replicas**: 3 nodes (webimpetus-mariadb-ha-0, webimpetus-mariadb-ha-1, webimpetus-mariadb-ha-2)
- **Replication**: Galera multi-master with automatic failover
- **Primary Node**: webimpetus-mariadb-ha-0 (automatic failover enabled)
- **SST Method**: mariabackup (for state snapshot transfer)
- **Recovery**: Enabled with 50% minimum cluster size requirement

### MaxScale Load Balancer
- **Replicas**: 2 instances for HA
- **Services**:
  - **Read-Write Router** (port 3306): Splits read and write operations
  - **Read-Only Router** (port 3307): Routes read-only queries
- **Monitoring**: Galera monitor with auto-failover and auto-rejoin
- **Admin GUI**: Available on port 8989

## Service Endpoints

### Internal Cluster Access

1. **Primary Service** (Read-Write):
   - Service: `webimpetus-mariadb-ha-primary`
   - ClusterIP: 10.43.53.209
   - Port: 3306

2. **Secondary Service** (Read-Only):
   - Service: `webimpetus-mariadb-ha-secondary`
   - ClusterIP: 10.43.82.201
   - Port: 3306

3. **MaxScale Load Balancer**:
   - Service: `webimpetus-maxscale`
   - LoadBalancer IP: 192.168.1.101
   - Ports:
     - 3306: Read-Write split router
     - 3307: Read-only router
     - 8989: Admin GUI

### Connection Strings

```bash
# Via MaxScale (recommended for applications)
mysql -h 192.168.1.101 -P 3306 -u webimpetus -p testCi4

# Direct to primary (for admin tasks)
mysql -h webimpetus-mariadb-ha-primary.test.svc.cluster.local -P 3306 -u webimpetus -p testCi4

# Direct to secondary (for read-only queries)
mysql -h webimpetus-mariadb-ha-secondary.test.svc.cluster.local -P 3306 -u webimpetus -p testCi4
```

## Credentials

Credentials are stored in Kubernetes secrets:

```bash
# Root password
kubectl get secret -n test mariadb-root-password -o jsonpath='{.data.password}' | base64 -d

# Application user password
kubectl get secret -n test mariadb-user-password -o jsonpath='{.data.password}' | base64 -d

# MaxScale admin password
kubectl get secret -n test maxscale-admin-password -o jsonpath='{.data.password}' | base64 -d
```

## Verification Commands

### Check Cluster Status

```bash
# Check all pods
kubectl get pods -n test

# Check MariaDB status
kubectl get mariadb -n test

# Check MaxScale status
kubectl get maxscale -n test

# Check all services
kubectl get svc -n test
```

### Test Database Connectivity

```bash
# Create a test pod
kubectl run -n test mysql-client --image=mariadb:11.8 -it --rm --restart=Never -- bash

# Inside the pod, connect via MaxScale
mariadb -h webimpetus-maxscale -P 3306 -u webimpetus -p

# Test the database
SHOW DATABASES;
USE testCi4;
```

### Galera Cluster Health Check

```bash
# Check cluster size (should show 3)
kubectl exec -n test webimpetus-mariadb-ha-0 -c mariadb -- \
  mariadb -e "SHOW STATUS LIKE 'wsrep_cluster_size';"

# Check cluster status
kubectl exec -n test webimpetus-mariadb-ha-0 -c mariadb -- \
  mariadb -e "SHOW STATUS LIKE 'wsrep_%';"
```

### MaxScale Health Check

```bash
# Access MaxScale admin GUI
# Open browser to: http://192.168.1.101:8989

# Or use CLI to check servers
kubectl exec -n test webimpetus-maxscale-0 -- maxctrl list servers

# Check services
kubectl exec -n test webimpetus-maxscale-0 -- maxctrl list services

# Check monitors
kubectl exec -n test webimpetus-maxscale-0 -- maxctrl list monitors
```

## High Availability Features

### Automatic Failover
- If the primary node fails, Galera automatically elects a new primary
- MaxScale detects the change and routes traffic accordingly
- No manual intervention required

### Self-Healing
- Failed pods are automatically restarted by Kubernetes
- Galera cluster auto-recovers when nodes rejoin
- MaxScale auto-rejoins nodes to the cluster

### Rolling Updates
- Updates can be performed without downtime
- Rolling update strategy ensures at least one replica is always available
- Pod Disruption Budget prevents simultaneous pod failures

## Resource Allocation

### MariaDB Pods
- **Requests**: 500m CPU, 1Gi Memory
- **Limits**: 2000m CPU, 2Gi Memory
- **Storage**: 10Gi per replica

### MaxScale Pods
- **Requests**: 300m CPU, 512Mi Memory
- **Limits**: 1000m CPU, 1Gi Memory

## Configuration Files

- MariaDB HA Configuration: [mariadb-ha.yaml](mariadb-ha.yaml)
- MaxScale Configuration: [maxscale-ha.yaml](maxscale-ha.yaml)

## Monitoring and Metrics

- **Prometheus Metrics**: Enabled on port 9104 (MariaDB exporter)
- **MaxScale Metrics**: Enabled and available via MaxScale API

## Backup and Recovery

The MariaDB operator supports:
- Physical backups using mariabackup
- Scheduled backup CRDs
- Point-in-time recovery
- S3-compatible storage for backups

To create a backup:
```yaml
apiVersion: k8s.mariadb.com/v1alpha1
kind: Backup
metadata:
  name: mariadb-backup
  namespace: test
spec:
  mariaDbRef:
    name: webimpetus-mariadb-ha
  storage:
    s3:
      bucket: my-backups
      endpoint: s3.amazonaws.com
      region: us-east-1
```

## Scaling

To scale the cluster:
```bash
# Scale MariaDB cluster (requires careful consideration with Galera)
kubectl patch mariadb -n test webimpetus-mariadb-ha --type='merge' -p '{"spec":{"replicas":5}}'

# Scale MaxScale
kubectl patch maxscale -n test webimpetus-maxscale --type='merge' -p '{"spec":{"replicas":3}}'
```

Note: Galera scaling should be done carefully. Always scale up by adding nodes one at a time.

## Troubleshooting

### Check logs
```bash
# MariaDB logs
kubectl logs -n test webimpetus-mariadb-ha-0 -c mariadb

# Agent logs
kubectl logs -n test webimpetus-mariadb-ha-0 -c agent

# MaxScale logs
kubectl logs -n test webimpetus-maxscale-0
```

### Common Issues

1. **Pods not starting**: Check storage class availability
2. **Cluster not forming**: Check network policies and firewall rules
3. **MaxScale connection issues**: Verify MariaDB users are created
4. **Split-brain scenarios**: Check network connectivity between nodes

## Next Steps

1. Configure automated backups
2. Set up monitoring with Prometheus/Grafana
3. Configure connection pooling in your application
4. Set up SSL/TLS for secure connections
5. Implement backup retention policies
