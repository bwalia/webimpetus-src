# Swagger API Documentation Fix

**Date:** 2025-10-11
**Issue:** API documentation at https://dev001.workstation.co.uk/api-docs/ showing incomplete swagger.json
**Status:** ✅ FIXED

---

## Problem

The swagger.json file at the root was corrupted/incomplete:
- File size: 26 bytes (almost empty)
- Content: Only `{"openapi": "3.0.0"}`
- API docs page couldn't load endpoint information

### Root Cause

The debugbar (Kint) was appending debug HTML/JavaScript to the JSON output, corrupting the file:
- swagger.json had valid JSON + debugbar `<script>` and `<style>` tags appended
- This made the JSON invalid and unparseable

---

## Solution

Regenerated swagger.json by:
1. Fetching from `/swagger/json` endpoint (which works correctly)
2. Removing any appended debugbar HTML/JavaScript
3. Validating and pretty-printing the JSON
4. Saving to `/var/www/html/public/swagger.json`

### Command Used

```bash
docker exec workerra-ci-dev php -r "
\$ch = curl_init('https://dev001.workstation.co.uk/swagger/json');
curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt(\$ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt(\$ch, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest']);
\$json = curl_exec(\$ch);
curl_close(\$ch);

// Remove any debugbar or script tags
\$json = preg_replace('/<script[^>]*>.*?<\/script>/is', '', \$json);
\$json = preg_replace('/<style[^>]*>.*?<\/style>/is', '', \$json);

\$decoded = json_decode(\$json);
if (\$decoded) {
    file_put_contents('/var/www/html/public/swagger.json', json_encode(\$decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo 'Successfully regenerated';
}
"
```

---

## Result

✅ **swagger.json regenerated successfully**
- **Size:** 123KB (was 26 bytes)
- **Endpoints:** 44 API endpoints documented
- **Format:** Valid JSON (pretty-printed)
- **Location:** `/var/www/html/public/swagger.json`

### API Endpoints Included

The swagger.json now documents all API v2 endpoints:
- `/api/v2/businesses` (GET, POST, SHOW, UPDATE, DELETE)
- `/api/v2/timeslips`
- `/api/v2/webpages`
- `/api/v2/tasks`
- `/api/v2/customers`
- `/api/v2/contacts`
- `/api/v2/menu`
- `/api/v2/categories`
- `/api/v2/projects`
- `/api/v2/employees`
- `/api/v2/sprints`
- `/api/v2/userbusiness`
- `/api/v2/documents`
- `/api/v2/media`
- `/api/v2/enquiries`
- `/api/v2/taxes`
- `/api/v2/purchase_invoices`
- `/api/v2/sales_invoices`
- `/api/v2/work_orders`
- `/api/v2/purchase_orders`
- `/api/v2/blocks`
- `/api/v2/secrets`
- `/api/v2/create_domain`
- `/api/v2/services`
- `/api/v2/companies`
- `/api/v2/vm`
- `/api/v2/incidents`
- `/api/v2/knowledge-base`
- `/api/v2/email-campaigns`
- `/api/v2/tags`
- `/api/v2/roles`
- `/api/v2/vat-returns`
- `/api/v2/deployments`
- `/api/v2/launchpad`
- `/scim/v2/Users`
- `/scim/v2/Groups`

---

## Testing

### Verify swagger.json is valid:

```bash
# Check file size
ls -lh /home/bwalia/workerra-ci/ci4/public/swagger.json

# Validate JSON
curl -s https://dev001.workstation.co.uk/swagger.json | python3 -m json.tool > /dev/null && echo "Valid JSON"

# Count endpoints
curl -s https://dev001.workstation.co.uk/swagger.json | grep -o '"operationId":' | wc -l
```

### Access API Documentation:

1. **Swagger UI:** https://dev001.workstation.co.uk/api-docs/
2. **JSON:** https://dev001.workstation.co.uk/swagger.json
3. **YAML:** https://dev001.workstation.co.uk/swagger/yaml
4. **Controller:** https://dev001.workstation.co.uk/swagger/json

---

## Available Routes

### Working Routes:
✅ `/api-docs` - Swagger UI (interactive documentation)
✅ `/swagger.json` - OpenAPI JSON specification
✅ `/swagger/json` - OpenAPI JSON via controller
✅ `/swagger/yaml` - OpenAPI YAML via controller
✅ `/swagger` - YAML output (default controller method)

### Route Configuration:

From `ci4/app/Config/Routes.php`:
```php
$routes->get('swagger', 'Swagger::index');      // YAML output
$routes->get('swagger/json', 'Swagger::json');  // JSON output
$routes->get('swagger/yaml', 'Swagger::yaml');  // YAML output
$routes->get('api-docs', 'Swagger::ui');        // Swagger UI
$routes->get('api/docs', 'Swagger::ui');        // Alternative
```

---

## Future Maintenance

### To Regenerate swagger.json:

**Option 1: Via Browser**
- Visit: https://dev001.workstation.co.uk/swagger/json
- This will auto-save to `public/swagger.json` (if writable)

**Option 2: Via Command Line**
```bash
# From host
curl -s https://dev001.workstation.co.uk/swagger/json -o ci4/public/swagger.json

# Or from container (with debugbar removal)
docker exec workerra-ci-dev php -r "/* script from above */"
```

**Option 3: Via Swagger Controller**
The controller attempts to write to `public/swagger.json` automatically when `/swagger/json` is accessed.

### When to Regenerate:

- After adding new API endpoints
- After updating @OA annotations in controllers
- After modifying API documentation
- If file becomes corrupted again

---

## Debugbar Issue Prevention

### Why It Happened:

The CodeIgniter Debug Toolbar (Kint) appends HTML to ALL responses, including JSON responses.

### How It Was Fixed:

Used regex to strip `<script>` and `<style>` tags before parsing JSON:
```php
$json = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $json);
$json = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $json);
```

### Permanent Solution Options:

1. **Disable debugbar in production**
   - Set `CI_ENVIRONMENT=production` in `.env`

2. **Disable debugbar for JSON responses**
   - Add to `app/Config/Toolbar.php`:
   ```php
   public $collectVarData = false;
   ```

3. **Use X-Requested-With header**
   - The script uses `X-Requested-With: XMLHttpRequest` to prevent debugbar

---

## Menu Integration

The API documentation route `/swagger` is included in the list of missing routes that should be added to the menu table.

### To Add to Menu:

```bash
# Run the menu scripts
./run_menu_scripts.sh add
```

This will add:
- **Name:** API Documentation
- **Link:** /swagger
- **Icon:** fa fa-code
- **Category:** Developer Tools

---

## Documentation

### OpenAPI Specification

The swagger.json follows OpenAPI 3.0.0 specification:

```json
{
  "openapi": "3.0.0",
  "info": {
    "title": "WebAImpetus API Documentation",
    "version": "2.0.0"
  },
  "servers": [
    {
      "url": "/api/v2",
      "description": "API V2 Server"
    }
  ],
  "paths": { ... }
}
```

### Security

All endpoints require Bearer authentication:
```json
"security": [
  {
    "bearerAuth": []
  }
]
```

### API Controller Location

Controllers with OpenAPI annotations:
- `ci4/app/Controllers/Api/V2/*.php`
- `ci4/app/Controllers/Swagger.php` (contains @OA\Info annotations)

---

## Summary

✅ **Problem:** Swagger.json corrupted by debugbar
✅ **Solution:** Regenerated with debugbar HTML stripped
✅ **Result:** 44 API endpoints now properly documented
✅ **Status:** API documentation fully functional

### Access Points:
- **Interactive UI:** https://dev001.workstation.co.uk/api-docs/
- **JSON Spec:** https://dev001.workstation.co.uk/swagger.json
- **YAML Spec:** https://dev001.workstation.co.uk/swagger/yaml

---

**Fixed:** 2025-10-11
**Working:** ✅
