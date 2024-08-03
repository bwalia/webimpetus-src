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

```configure your settings in the .env file
`see .env sample file in the root folder`
`this shell script assumes you have docker compose installed and working`
```


```shell or bash
`sudo sh ./deploy-to-docker.sh` or `sudo bash ./deploy-to-docker.sh`
```

# DEPLOY WEBIMPETUS TO KUBERNETES (SUITABLE FOR DEVELOPMENT PODS, TEST, INT, ACC and or PROD environments)

### Prerequisite k3s, k8s, EKS, AKS or GKE must be accessible from the target machine:

Use the helm package manager [helm](https://webimpetus.io/en/stable/) to install WebImpetus.

```shell or bash
`sudo sh ./deploy-to-kubernetes.sh` or `sudo bash ./deploy-to-kubernetes.sh`
`this shell script assumes you have a working kubernetes cluster and connection to kubernetes configured and working`
```
## List of the environments:-

| Environment | Link     | Credentials                |
| :-------- | :------- | :------------------------- |
| `dev` | `http://localhost:8080/` | `Ask administrator` |
| `dev with https:// tls cert` | `https://localhost:9093/` | `Ask administrator` | `see deploy-to-docker.sh`
| `int` | `https://int-my.workstation.co.uk/` | `Ask administrator` |
| `test` | `https://test-my.workstation.co.uk/` | `Ask administrator` |
| `acc` | `https://acc-my.workstation.co.uk/` | `Ask administrator` |


## USAGE

```http://localhost:8080```

## API DOCS
You can access API documentation here:
```/api-docs``` or ```https://test-my.workstation.co.uk/api-docs/```

![Landing Page](https://github.com/bwalia/webimpetus-src/blob/b16260a53f53b37d6036abbc91a2c3db6e8c07c8/webimpetus_login_page_v1_2022.png)

## CONTRIBUTING

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.

## RESTORE BACKUP FROM AUTOMATED HOURLY BACKUP BUCKET

- Step 1. Run Restore WebImpetus Backup Workflow
- Step 2. Enter the file path located in the hourly bucket for that environment, Example : mydump.tar.gz
- Step 3. Please note the file path must exist in the bucket and must be .tar.gz format, Example: mydump.tar.gz
- Step 4. Restore the backup and test the environment has database restored

Please make sure to update tests as appropriate.

## LICENCE

Checkout LICENCE
