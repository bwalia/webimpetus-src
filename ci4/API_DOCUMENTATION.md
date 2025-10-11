# MyWorkstation API Documentation

## Overview

This application provides a comprehensive RESTful API (v2) with full CRUD operations for all major resources. The API is documented using OpenAPI 3.0 specifications and includes an interactive Swagger UI for testing.

## API Endpoints

### Base URL
```
/api/v2
```

### Available Resources

All resources support standard RESTful operations:

| Resource | Endpoint | Operations |
|----------|----------|------------|
| **Users** | `/api/v2/users` | GET, POST, PUT, DELETE |
| **Customers** | `/api/v2/customers` | GET, POST, PUT, DELETE |
| **Contacts** | `/api/v2/contacts` | GET, POST, PUT, DELETE |
| **Projects** | `/api/v2/projects` | GET, POST, PUT, DELETE |
| **Tasks** | `/api/v2/tasks` | GET, POST, PUT, DELETE |
| **Timeslips** | `/api/v2/timeslips` | GET, POST, PUT, DELETE |
| **Employees** | `/api/v2/employees` | GET, POST, PUT, DELETE |
| **Documents** | `/api/v2/documents` | GET, POST, PUT, DELETE |
| **Work Orders** | `/api/v2/work_orders` | GET, POST, PUT, DELETE |
| **Sales Invoices** | `/api/v2/sales_invoices` | GET, POST, PUT, DELETE |
| **Purchase Invoices** | `/api/v2/purchase_invoices` | GET, POST, PUT, DELETE |
| **Purchase Orders** | `/api/v2/purchase_orders` | GET, POST, PUT, DELETE |
| **Businesses** | `/api/v2/businesses` | GET, POST, PUT, DELETE |
| **Companies** | `/api/v2/companies` | GET, POST, PUT, DELETE |
| **Categories** | `/api/v2/categories` | GET, POST, PUT, DELETE |
| **Tags** | `/api/v2/tags` | GET, POST, PUT, DELETE |
| **Sprints** | `/api/v2/sprints` | GET, POST, PUT, DELETE |
| **Enquiries** | `/api/v2/enquiries` | GET, POST, PUT, DELETE |
| **Incidents** | `/api/v2/incidents` | GET, POST, PUT, DELETE |
| **Services** | `/api/v2/services` | GET, POST, PUT, DELETE |
| **Secrets** | `/api/v2/secrets` | GET, POST, PUT, DELETE |
| **Deployments** | `/api/v2/deployments` | GET, POST, PUT, DELETE |
| **Webpages** | `/api/v2/webpages` | GET, POST, PUT, DELETE |
| **Blocks** | `/api/v2/blocks` | GET, POST, PUT, DELETE |
| **Media** | `/api/v2/media` | GET, POST, PUT, DELETE |
| **Menu** | `/api/v2/menu` | GET, POST, PUT, DELETE |
| **Taxes** | `/api/v2/taxes` | GET, POST, PUT, DELETE |
| **Roles** | `/api/v2/roles` | GET, POST, PUT, DELETE |
| **VAT Returns** | `/api/v2/vat-returns` | GET, POST, PUT, DELETE |
| **Email Campaigns** | `/api/v2/email-campaigns` | GET, POST, PUT, DELETE |
| **Knowledge Base** | `/api/v2/knowledge-base` | GET, POST, PUT, DELETE |
| **Virtual Machines** | `/api/v2/vm` | GET, POST, PUT, DELETE |
| **Launchpad** | `/api/v2/launchpad` | GET, POST, PUT, DELETE |

## Authentication

The API uses **Bearer Token Authentication**. Include your JWT token in the Authorization header:

```bash
Authorization: Bearer YOUR_JWT_TOKEN_HERE
```

### Example Request

```bash
curl -X GET "http://your-domain.com/api/v2/projects" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json"
```

## Standard CRUD Operations

### 1. List All Records (GET)
```
GET /api/v2/{resource}
```

**Query Parameters:**
- `uuid_business_id` (required) - Filter by business UUID
- `page` - Page number for pagination (default: 1)
- `perPage` - Records per page (default: 10)
- `q` - Search query
- `field` - Sort field
- `order` - Sort order (asc/desc)

**Example:**
```bash
GET /api/v2/projects?uuid_business_id=xxx-xxx-xxx&page=1&perPage=20
```

### 2. Get Single Record (GET)
```
GET /api/v2/{resource}/{uuid or id}
```

**Example:**
```bash
GET /api/v2/projects/39e2f8e3-e076-545a-b101-e888ea4c0bd1
```

### 3. Create Record (POST)
```
POST /api/v2/{resource}
```

**Required Parameter:**
- `uuid_business_id` - Business UUID (in request body or query)

**Example:**
```bash
POST /api/v2/projects
Content-Type: application/json

{
  "name": "New Project",
  "customers_id": 3,
  "uuid_business_id": "xxx-xxx-xxx",
  "budget": 50000
}
```

### 4. Update Record (PUT)
```
PUT /api/v2/{resource}/{uuid}
```

**Example:**
```bash
PUT /api/v2/projects/39e2f8e3-e076-545a-b101-e888ea4c0bd1
Content-Type: application/json

{
  "name": "Updated Project Name",
  "budget": 75000
}
```

### 5. Delete Record (DELETE)
```
DELETE /api/v2/{resource}/{uuid or id}
```

**Example:**
```bash
DELETE /api/v2/projects/39e2f8e3-e076-545a-b101-e888ea4c0bd1
```

## Special Endpoints

### Projects
- `GET /api/v2/business/{business_uuid}/projects` - List projects by business

### Tasks
- `GET /api/v2/business/{business_uuid}/projects/{project_uuid}/employee/{employee_uuid}/tasks` - List tasks by project and employee
- `GET /api/v2/business/{business_uuid}/employee/{employee_uuid}/tasks-status` - Get task status by employee
- `PUT /api/v2/business/{business_uuid}/projects/{project_uuid}/tasks/update-status` - Update task status

### Timeslips
- `GET /api/v2/business/{business_uuid}/employee/{employee_uuid}/tasks/{task_uuid}/timeslip` - Get timeslip by task

### Webpages
- `GET /api/v2/business/{business_uuid}/contact/{contact_uuid}/webpages` - List webpages by contact
- `GET /api/v2/business/{business_uuid}/contact/{contact_uuid}/blogs` - List blogs by category
- `GET /api/v2/business/{business_uuid}/public/blogs` - List public blogs

### Enquiries
- `POST /api/v2/enquiries/business-enqury` - Add enquiry by business code

### Deployments
- `GET /api/v2/deployments/stats` - Get deployment statistics

### Launchpad
- `POST /api/v2/launchpad/click/{bookmark_uuid}` - Track bookmark click
- `POST /api/v2/launchpad/share` - Share bookmark
- `GET /api/v2/launchpad/recent` - Get recent bookmarks

## Interactive API Documentation

### Access Swagger UI
Visit the interactive API documentation to explore and test all endpoints:

```
http://your-domain.com/api-docs
```

or

```
http://your-domain.com/api/docs
```

### Features of Swagger UI:
- Browse all available endpoints
- View request/response schemas
- Test API calls directly from the browser
- Authenticate with your Bearer token
- See example requests and responses
- Download OpenAPI specification

## Generating Documentation

### Method 1: Via CLI Command
```bash
php spark swagger:generate
```

This will generate:
- `public/swagger.json` - JSON format OpenAPI specification
- `public/swagger.yaml` - YAML format OpenAPI specification

### Method 2: Via HTTP Endpoints
```bash
# Generate JSON
curl http://your-domain.com/swagger/json > swagger.json

# Generate YAML
curl http://your-domain.com/swagger/yaml > swagger.yaml
```

### Method 3: View Raw Specification
- JSON: `http://your-domain.com/swagger/json`
- YAML: `http://your-domain.com/swagger/yaml`
- YAML (default): `http://your-domain.com/swagger`

## Response Format

### Success Response
```json
{
  "data": [...],
  "total": 100,
  "message": 200
}
```

### Error Response
```json
{
  "data": "Error message or details",
  "status": 403
}
```

## Common Response Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden (e.g., missing uuid_business_id) |
| 404 | Not Found |
| 500 | Server Error |

## Important Notes

### Business UUID Requirement
⚠️ **Most endpoints require `uuid_business_id` parameter** to filter data by business. This is a security measure to ensure users only access data from their own business.

**Missing uuid_business_id will result in:**
```json
{
  "data": "You must need to specify the User Business ID",
  "status": 403
}
```

### UUID vs ID
- Most endpoints support both UUID and numeric ID for identifying resources
- UUIDs are preferred for external API access
- The system auto-detects whether you're using UUID or ID

### Pagination
- Default page size: 10 records
- Use `page` and `perPage` parameters to control pagination
- Response includes `total` count for implementing pagination UI

## Testing with cURL

### Example: List Projects
```bash
curl -X GET \
  "http://localhost/api/v2/projects?uuid_business_id=329e0405-b544-5051-8d37-d0143e9c8829&page=1&perPage=10" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### Example: Create Customer
```bash
curl -X POST \
  "http://localhost/api/v2/customers" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "company_name": "Acme Corp",
    "email": "contact@acme.com",
    "uuid_business_id": "329e0405-b544-5051-8d37-d0143e9c8829"
  }'
```

### Example: Update Project
```bash
curl -X PUT \
  "http://localhost/api/v2/projects/39e2f8e3-e076-545a-b101-e888ea4c0bd1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Project Name",
    "budget": 75000
  }'
```

### Example: Delete Document
```bash
curl -X DELETE \
  "http://localhost/api/v2/documents/123" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## API Documentation Routes

| Route | Purpose |
|-------|---------|
| `/api-docs` | Interactive Swagger UI |
| `/api/docs` | Alternative Swagger UI URL |
| `/swagger` | Raw YAML specification |
| `/swagger/json` | Generate/download JSON spec |
| `/swagger/yaml` | Generate/download YAML spec |

## Development Notes

### Adding New Endpoints
1. Create controller in `app/Controllers/Api/V2/`
2. Extend `ResourceController`
3. Add OpenAPI annotations using `@OA\` tags
4. Add route in `app/Config/Routes.php`
5. Regenerate documentation: `php spark swagger:generate`

### OpenAPI Annotations Example
```php
/**
 * @OA\Get(
 *     path="/api/v2/resources",
 *     tags={"Resources"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response="200",
 *         description="Success"
 *     )
 * )
 */
public function index() {
    // Implementation
}
```

## SCIM API

The application also includes SCIM 2.0 endpoints for user and group provisioning:

- `POST /scim/v2/Users` - Create user
- `GET /scim/v2/Users` - List users
- `GET /scim/v2/Users/{id}` - Get user
- `PUT /scim/v2/Users/{id}` - Update user
- `DELETE /scim/v2/Users/{id}` - Delete user
- Similar endpoints for Groups

## Support

For issues or questions about the API:
- Check the interactive documentation at `/api-docs`
- Review this documentation
- Contact: support@myworkstation.com

---

**Last Updated:** 2025-01-10
**API Version:** 2.0.0
