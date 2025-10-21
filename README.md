# MariaDB Operator & LoadBalancer Setup

## Overview

This setup deploys MariaDB using the [MariaDB Operator](https://mariadb.com/docs/skysql-operators/kubernetes/) on a k3s cluster, with a LoadBalancer service for external access.

---

## Prerequisites

- Kubernetes cluster (k3s)
- Helm installed
- `kubectl` access

---

## Installation Steps

### 1. Install MariaDB Operator and CRDs

```bash
helm repo add mariadb-operator https://mariadb-operator.github.io/mariadb-operator
helm repo update
helm install mariadb-operator mariadb-operator/mariadb-operator \
  --namespace mariadb-operator --create-namespace
```

### 2. Create the Namespace

```bash
kubectl create namespace test
```

### 3. Deploy MariaDB Custom Resource

Edit and apply [`mariadb-cr.yaml`](mariadb-cr.yaml):

```bash
kubectl apply -f mariadb-cr.yaml
```

### 4. Expose MariaDB with a LoadBalancer Service

Create a file `mariadb-lb.yaml`:

```yaml
apiVersion: v1
kind: Service
metadata:
  name: mariadb-lb
  namespace: test
spec:
  type: LoadBalancer
  ports:
    - port: 3306
      targetPort: 3306
      protocol: TCP
      name: mysql
  selector:
    app.kubernetes.io/name: mariadb
```

Apply it:

```bash
kubectl apply -f mariadb-lb.yaml
```

### 5. Get the LoadBalancer IP/Port

```bash
kubectl get svc -n test mariadb-lb
```

---

## Connection Details

- **Host:** `<LoadBalancer IP or DNS>`
- **Port:** `3306`
- **User:** `root`
- **Password:** `testPassword`
- **Database:** `testCi4`

---

## Notes

- The storage class used is `local-path` (default for k3s).
- Update credentials and storage as needed for production.
- For local k3s, LoadBalancer may use `klipper-lb` and expose on a NodePort.

---

## References

- [MariaDB Operator Docs](https://mariadb.com/docs/skysql-operators/kubernetes/)
- [k3s Service LoadBalancer](https://rancher.com/docs/k3s/latest/en/networking/#service-loadbalancer)