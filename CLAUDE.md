# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Common Development Commands

### Docker Development Environment
- **Start development environment**: `sudo ./deploy-to-docker.sh` or `sudo ./deploy-to-docker.sh dev`
- **Docker Compose operations**: 
  - `docker-compose up -d --build` - Start all services
  - `docker-compose down` - Stop all services
  - `docker-compose ps` - Check service status
- **Container access**: `docker exec -it webimpetus-dev bash`

### CodeIgniter 4 (PHP Backend)
- **Install dependencies**: `composer install` (run inside container or ci4/ directory)
- **Update dependencies**: `composer update`
- **Run tests**: `composer test` or `phpunit` (from ci4/ directory)
- **Database migrations**: Use CodeIgniter CLI tools within the ci4/ directory

### Testing (Cypress E2E)
- **Run UI tests**: `cd qa/cypress_ui_test && npm test`
- **Individual test commands**:
  - `npm run clean` - Clean previous results
  - `npm run ui-test-allure` - Run Cypress tests with Allure reporting
  - `npm run allure-report` - Generate Allure reports
  - `npm run allure-report-open` - Open reports

### Kubernetes Deployment
- **Deploy to K8s**: `sudo ./deploy-to-kubernetes.sh`
- **Helm operations**: Located in `devops/webimpetus-chart/`

## Architecture Overview

### Project Structure
This is a **multi-tenant business management system** built with:
- **Backend**: CodeIgniter 4 (PHP) in `ci4/` directory
- **Database**: MariaDB 11.2.2
- **Authentication**: Keycloak integration for SSO
- **Frontend**: Server-side rendered views with CI4 templating
- **Testing**: Cypress for E2E testing
- **DevOps**: Docker Compose for development, Helm charts for K8s deployment

### Key Directories
- `ci4/` - Main CodeIgniter 4 application
  - `ci4/app/Controllers/` - API and web controllers
  - `ci4/app/Models/` - Database models and business logic
  - `ci4/app/Views/` - Templates for all modules
  - `ci4/app/Config/` - Application configuration
- `devops/` - Docker, Kubernetes, and deployment configurations
- `qa/cypress_ui_test/` - E2E testing setup
- `.env` - Environment configuration (copied to container)

### Core Business Modules
The system includes these main modules:
- **CRM**: Customer relationship management
- **Enquiries**: Lead management system
- **Timesheet & Time billing**: Time tracking and billing
- **Sales/Purchase Orders**: Order management
- **Invoicing**: Sales and purchase invoice management
- **HR & Employee management**: Staff management
- **E-commerce**: Online store functionality
- **Projects & Tasks**: Project management
- **Documents & Gallery**: File management

### Database Architecture
- **Primary DB**: MariaDB container named `webimpetus-db`
- **Models**: Located in `ci4/app/Models/` with Core models in `Models/Core/`
- **Migrations**: Database schema changes in `ci4/app/Database/Migrations/`
- **Connection**: Configured via `.env` file, connects to `webimpetus-db:3306`

### API Structure
- **Main API Controller**: `ci4/app/Controllers/Api_v2.php`
- **Endpoint**: `/api-docs` for Swagger documentation
- **Authentication**: JWT-based with configurable time-to-live
- **CORS**: Handled by `agungsugiarto/codeigniter4-cors` package

### Development Environment
- **Primary URL**: http://localhost:8080 (mapped to port 5500 in docker-compose)
- **Secure URL**: https://localhost:9093 (when TLS is configured)
- **Database Admin**: Adminer at http://localhost:5502
- **Keycloak**: http://localhost:3010 (admin/admin)

### Important Configuration Files
- **Main config**: `.env` in root (gets copied to container)
- **Docker**: `docker-compose.yml` defines all services
- **CI4 Config**: `ci4/app/Config/` directory
- **Composer**: `ci4/composer.json` for PHP dependencies

### Development Workflow
1. Configure `.env` file with database and application settings
2. Run `./deploy-to-docker.sh` to start development environment
3. Access application at http://localhost:8080
4. Use `docker exec -it webimpetus-dev bash` for container access
5. Run tests with Cypress in `qa/cypress_ui_test/`

### Key Dependencies
- **PHP**: 7.3+ or 8.0+
- **CodeIgniter**: 4.2.10+
- **JWT**: Firebase JWT for authentication
- **PDF Generation**: mPDF
- **Email**: PHPMailer
- **API Documentation**: Swagger PHP
- **Testing**: PHPUnit (PHP), Cypress (E2E)

## Environment-Specific Notes
- **Dev**: Uses `webimpetus-dev` container name, MariaDB on port 3309
- **Production environments**: Deployed via Kubernetes with environment-specific configurations
- **Database**: SQL dumps available in root directory for different environments
- **SSL**: TLS certificates managed through deployment scripts

## Container Network
- **Network**: `webimpetus-network` (172.178.0.0/16)
- **App Container**: 172.178.0.8
- **Keycloak**: 172.178.0.11
- **Database**: Accessible via service name `webimpetus-db`