# MariaDB HA Connection Guide

## Current Status

### Working Components
- ✅ MariaDB Galera Cluster: 3 nodes running successfully
- ✅ Database: `testCi4` created and accessible
- ✅ User: `workerra-ci` with ALL privileges on testCi4
- ⚠️ MaxScale: Running but not properly configured by the operator

### Known Issue
MaxScale is experiencing configuration issues with the operator. The operator is unable to properly configure the monitoring and routing services via the MaxScale REST API. This is a known compatibility issue between certain versions of the MariaDB operator and MaxScale.

## Working Connection Methods

### Method 1: Direct Connection to Primary (Recommended for Now)

**From within the Kubernetes cluster:**
```bash
mysql -h workerra-ci-mariadb-ha-primary.test.svc.cluster.local -P 3306 -u workerra-ci -pTestPassword123 testCi4
```

**Connection Details:**
- Host: `workerra-ci-mariadb-ha-primary.test.svc.cluster.local`
- Port: `3306`
- User: `workerra-ci`
- Password: `TestPassword123`
- Database: `testCi4`

### Method 2: Direct Connection to Secondary (Read-Only)

```bash
mysql -h workerra-ci-mariadb-ha-secondary.test.svc.cluster.local -P 3306 -u workerra-ci -pTestPassword123 testCi4
```

### Method 3: Connection via ClusterIP Service

```bash
mysql -h workerra-ci-mariadb-ha.test.svc.cluster.local -P 3306 -u workerra-ci -pTestPassword123 testCi4
```

## Testing Connection from a Pod

```bash
# Create a test pod
kubectl run -n test mysql-client --image=mariadb:11.8 --restart=Never -- \
  mariadb -h workerra-ci-mariadb-ha-primary -u workerra-ci -pTestPassword123 testCi4 -e "SELECT DATABASE(), USER();"

# Check logs
kubectl logs -n test mysql-client

# Clean up
kubectl delete pod -n test mysql-client
```

## Exposing Database Externally (Alternative to MaxScale)

Since MaxScale is having configuration issues, here are alternative approaches:

### Option 1: NodePort Service

Create a NodePort service for direct external access:

```yaml
apiVersion: v1
kind: Service
metadata:
  name: mariadb-external
  namespace: test
spec:
  type: NodePort
  selector:
    app.kubernetes.io/name: mariadb
    app.kubernetes.io/instance: workerra-ci-mariadb-ha
  ports:
    - port: 3306
      targetPort: 3306
      nodePort: 30306  # Choose a port between 30000-32767
```

Then connect using:
```bash
mysql -h <NODE_IP> -P 30306 -u workerra-ci -pTestPassword123 testCi4
```

### Option 2: LoadBalancer Service (if MetalLB is available)

```yaml
apiVersion: v1
kind: Service
metadata:
  name: mariadb-loadbalancer
  namespace: test
spec:
  type: LoadBalancer
  selector:
    app.kubernetes.io/name: mariadb
    app.kubernetes.io/instance: workerra-ci-mariadb-ha
    statefulset.kubernetes.io/pod-name: workerra-ci-mariadb-ha-0
  ports:
    - port: 3306
      targetPort: 3306
```

### Option 3: Ingress with TCP Proxy

If you have an Ingress controller that supports TCP proxying (like NGINX Ingress), you can configure TCP service exposure.

## Connection Strings for Applications

### PHP (CodeIgniter 4)
```php
'default' => [
    'DSN'      => '',
    'hostname' => 'workerra-ci-mariadb-ha-primary.test.svc.cluster.local',
    'username' => 'workerra-ci',
    'password' => 'TestPassword123',
    'database' => 'testCi4',
    'DBDriver' => 'MySQLi',
    'DBPrefix' => '',
    'pConnect' => false,
    'DBDebug'  => true,
    'charset'  => 'utf8',
    'DBCollat' => 'utf8_general_ci',
    'port'     => 3306,
],
```

### Python
```python
import mysql.connector

connection = mysql.connector.connect(
    host='workerra-ci-mariadb-ha-primary.test.svc.cluster.local',
    port=3306,
    user='workerra-ci',
    password='TestPassword123',
    database='testCi4'
)
```

### Node.js
```javascript
const mysql = require('mysql2');

const connection = mysql.createConnection({
  host: 'workerra-ci-mariadb-ha-primary.test.svc.cluster.local',
  port: 3306,
  user: 'workerra-ci',
  password: 'TestPassword123',
  database: 'testCi4'
});
```

### Java (JDBC)
```java
String url = "jdbc:mysql://workerra-ci-mariadb-ha-primary.test.svc.cluster.local:3306/testCi4";
String user = "workerra-ci";
String password = "TestPassword123";

Connection conn = DriverManager.getConnection(url, user, password);
```

## Credential Management

### Current Credentials

```bash
# Get root password
kubectl get secret -n test mariadb-root-password -o jsonpath='{.data.password}' | base64 -d
# Result: o6W=QL473YV&kdBJ

# Get application user password (original - not currently working)
kubectl get secret -n test mariadb-user-password -o jsonpath='{.data.password}' | base64 -d
# Result: e2dHDor0]MAf5V4!

# Current working password (manually set)
# User: workerra-ci
# Password: TestPassword123
```

### Update Password

To change the password:
```bash
kubectl run -n test mysql-client --image=mariadb:11.8 --restart=Never -- \
  mariadb -h workerra-ci-mariadb-ha-primary -uroot -p'o6W=QL473YV&kdBJ' \
  -e "ALTER USER 'workerra-ci'@'%' IDENTIFIED BY 'NewSecurePassword';"
```

## High Availability Features

### Current HA Setup
- **3-node Galera Cluster**: Multi-master replication
- **Automatic Failover**: If primary fails, another node automatically becomes primary
- **Load Distribution**: Read queries can be sent to secondary service

### Testing Failover

```bash
# Delete the primary pod to test failover
kubectl delete pod -n test workerra-ci-mariadb-ha-0

# Watch the failover happen
kubectl get mariadb -n test -w

# The cluster will automatically elect a new primary
```

### Monitoring Cluster Health

```bash
# Check cluster status
kubectl get mariadb -n test

# Check pod status
kubectl get pods -n test

# Check which node is primary
kubectl get mariadb -n test workerra-ci-mariadb-ha -o jsonpath='{.status.currentPrimary}'

# Check Galera cluster size (should be 3)
kubectl exec -n test workerra-ci-mariadb-ha-0 -c mariadb -- \
  mariadb -uroot -p'o6W=QL473YV&kdBJ' -e "SHOW STATUS LIKE 'wsrep_cluster_size';"
```

## Troubleshooting MaxScale

The MaxScale issue appears to be related to the operator's API configuration. To fix this, you would need to:

1. **Try a different MaxScale version** in the CRD
2. **Use ProxySQL instead** (alternative proxy/load balancer)
3. **Wait for operator updates** that fix the compatibility issue
4. **Manually configure MaxScale** (bypassing the operator)

For production use, I recommend using the direct MariaDB services with application-level load balancing, or implementing ProxySQL as an alternative.

## Next Steps

1. **For immediate use**: Connect applications using the direct primary service URL
2. **For external access**: Create a NodePort or LoadBalancer service
3. **For advanced routing**: Consider deploying ProxySQL manually
4. **For monitoring**: Set up Prometheus metrics scraping from MariaDB pods

## Security Recommendations

1. **Change default password**: Update from `TestPassword123` to a strong password
2. **Enable SSL/TLS**: Configure encrypted connections
3. **Network Policies**: Restrict pod-to-pod communication
4. **Secret Management**: Use external secret management (e.g., Vault)
5. **Regular Backups**: Configure automated backup CRDs
