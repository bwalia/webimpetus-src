# WebImpetus v1

WebImpetus is an open-source Business management Database solution for managing an entire Business using modern & best practices for software development and managing its entire life cycle.

The key modules available are:

- CRM
- Enquiries
- Timesheet
- Time billing
- Sales Orders
- Purchase Orders
- Easy-to-use Template System
- Sales Invoicing
- Purchase Invoicing
- Users
- Employees and HR management
- E-commerce

# DEPLOY WEBIMPETUS AS A DEV DOCKER CONTAINER

Use the helm package manager [helm](https://webimpetus.io/en/stable/) to install WebImpetus.

### Prerequisite docker must be installed on the target machine:

```shell or bash
`docker compose up` or `docker compose up -d`
```

```shell or bash
`docker compose ps` to view the webimpetus containers are running. The connection secrets must be supplied at runtime see `sync.sh` to inject mariadb creds into docker container after it is running
```

# DEPLOY WEBIMPETUS TO KUBERNETES (SUITABLE FOR DEVELOPMENT PODS, TEST, INT, ACC and or PROD environments)

### Prerequisite k3s, k8s, EKS, AKS or GKE must be accessible from the target machine:

Use the helm package manager [helm](https://webimpetus.io/en/stable/) to install WebImpetus.

```shell or bash
`sh ./build.sh`
```

```shell or bash
`sh ./install.sh`
```

## USAGE

```http://localhost:{your port number}```

![Landing Page](https://github.com/bwalia/webimpetus-src/blob/b16260a53f53b37d6036abbc91a2c3db6e8c07c8/webimpetus_login_page_v1_2022.png)

## CONTRIBUTING

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.

## LICENCE

Checkout LICENCE
