# Int Environment Fixes Applied

## Date: October 6, 2025

## Issues Fixed

### 1. PHP 8.4 Session Deprecation Error
**Error**: `ini_set(): session.sid_length INI setting is deprecated`

**Root Cause**: Int environment was running PHP 8.4.13 with old CodeIgniter 4.1.8 from local `system/` directory instead of CI 4.6.3 from `vendor/` directory.

**Fixes Applied**:
- ✅ Updated `ci4/app/Config/Paths.php` to use vendor directory
- ✅ Removed old `ci4/system/` directory from repository

### 2. CodeIgniter 4.5+ Bootstrap Deprecation
**Error**: `This "system/bootstrap.php" is no longer used`

**Root Cause**: CodeIgniter 4.5+ deprecated `bootstrap.php` in favor of `Boot.php` with a new initialization method.

**Fix Applied**:
- ✅ Updated `ci4/public/index.php` to use `CodeIgniter\Boot::bootWeb($paths)` instead of old bootstrap

### 3. Missing Autoload Helpers Property
**Error**: `Undefined property: Config\Autoload::$helpers`

**Root Cause**: CodeIgniter 4.5+ requires the `$helpers` property in the Autoload config class.

**Fix Applied**:
- ✅ Added `public $helpers = [];` property to `ci4/app/Config/Autoload.php`

### 4. Missing Config Properties for CI 4.5+
**Errors**: 
- `Undefined property: Config\Modules::$composerPackages`
- `Undefined property: Config\App::$allowedHostnames`
- `Creation of dynamic property Config\Exceptions::$logDeprecations is deprecated`
- `Creation of dynamic property Config\Exceptions::$deprecationLogLevel is deprecated`

**Root Cause**: CodeIgniter 4.5+ introduced new required properties in configuration classes.

**Fixes Applied**:
- ✅ Added `public $composerPackages = [];` property to `ci4/app/Config/Modules.php`
- ✅ Added `public $allowedHostnames = [];` property to `ci4/app/Config/App.php`
- ✅ Added `public $logDeprecations = true;` property to `ci4/app/Config/Exceptions.php`
- ✅ Added `public $deprecationLogLevel = 'warning';` property to `ci4/app/Config/Exceptions.php`

### 5. Missing Feature Config Class
**Error**: `Attempt to read property "strictLocaleNegotiation" on null`

**Root Cause**: CodeIgniter 4.5+ introduced a new `Feature` config class for backward compatibility settings.

**Fix Applied**:
- ✅ Created `ci4/app/Config/Feature.php` with required properties:
  - `$autoRoutesImproved`
  - `$oldFilterOrder`
  - `$limitZeroAsAll`
  - `$strictLocaleNegotiation`

### 6. Kint Debugging Library Dependency
**Error**: `Class "Kint\Renderer\Renderer" not found`

**Root Cause**: Kint debugging library is not installed in production, but Kint config was importing and using its classes.

**Fix Applied**:
- ✅ Removed `use Kint\Renderer\Renderer;` import from `ci4/app/Config/Kint.php`
- ✅ Changed `Renderer::SORT_FULL` to numeric value `0`

### 7. Missing Cache Config Property
**Error**: `Undefined property: Config\Cache::$reservedCharacters`

**Root Cause**: CodeIgniter 4.5+ requires `$reservedCharacters` property for PSR-6 cache compliance.

**Fix Applied**:
- ✅ Added `public $reservedCharacters = '{}()/\@:';` property to `ci4/app/Config/Cache.php`

### 8. Missing Routing Config Class
**Error**: `Argument #3 ($routing) must be of type Config\Routing, null given`

**Root Cause**: CodeIgniter 4.5+ introduced a new `Routing` config class for routing configuration.

**Fix Applied**:
- ✅ Created `ci4/app/Config/Routing.php` with required routing properties:
  - `$routeFiles`, `$defaultNamespace`, `$defaultController`, `$defaultMethod`
  - `$translateURIDashes`, `$override404`, `$autoRoute`, `$prioritize`
  - `$multipleSegmentsOneParam`, `$moduleRoutes`, `$translateUriToCamelCase`

### 9. Missing Session Config Class
**Error**: `Attempt to read property "driver" on null` in Services.php line 675

**Root Cause**: CodeIgniter 4.5+ requires the `Session` config class for session handling.

**Fix Applied**:
- ✅ Created `ci4/app/Config/Session.php` with all required session properties:
  - `$driver` - Session storage driver (FileHandler)
  - `$cookieName` - Session cookie name
  - `$expiration` - Session expiration time in seconds
  - `$savePath` - Session save path (WRITEPATH . 'session')
  - `$matchIP` - Whether to match user's IP address
  - `$timeToUpdate` - Session ID regeneration interval
  - `$regenerateDestroy` - Whether to destroy old session data
  - `$DBGroup` - Database group for database sessions
  - `$lockRetryInterval` - Lock retry interval for Redis (microseconds)
  - `$lockMaxRetries` - Maximum lock acquisition attempts for Redis

### 10. ProxyIPs Type Error
**Error**: `foreach() argument must be of type array|object, string given` in RequestTrait.php line 87

**Root Cause**: CodeIgniter 4.5+ expects `$proxyIPs` to be an array, but it was set to an empty string.

**Fix Applied**:
- ✅ Changed `public $proxyIPs = '';` to `public $proxyIPs = [];` in `ci4/app/Config/App.php`

### 11. Protected Request Config Property
**Error**: `Cannot access protected property CodeIgniter\HTTP\IncomingRequest::$config` in BaseController.php line 70

**Root Cause**: CodeIgniter 4.5+ made the `$config` property protected, preventing direct access via `$request->config`.

**Fix Applied**:
- ✅ Changed `$request->config->supportedLocales` to `config('App')->supportedLocales` in `ci4/app/Controllers/BaseController.php`
- Uses the `config()` helper function which is the recommended way to access config in CI 4.5+

### 12. Improved Auto-Routing Compatibility
**Error**: `404 - Section not found: Controller or its method is not found: \App\Controllers\Home::postLogin`

**Root Cause**: CI 4.5+ introduced improved auto-routing that requires HTTP method prefixes (e.g., `postLogin` for POST to `/login`). The application was built for legacy auto-routing.

**Fix Applied**:
- ✅ Set `$autoRoutesImproved = false` in `ci4/app/Config/Feature.php` to use legacy auto-routing
- ✅ Set `$autoRoute = true` in `ci4/app/Config/Routing.php` to enable auto-routing
- This restores backward compatibility with the existing controller structure

### 13. Query Builder limit() Type Safety
**Error**: `BaseBuilder::limit(): Argument #1 ($value) must be of type ?int, string given`

**Root Cause**: CI 4.5+ enforces strict type checking - `limit()` and `offset()` now require integer types, but `$_GET` and `getVar()` return strings.

**Fix Applied**:
- ✅ Cast all `$_GET['limit']` and `$_GET['offset']` to `(int)` in API V2 controllers (11 files)
- ✅ Cast all `$this->request->getVar('limit')` and `getVar('offset')` to `(int)` in regular controllers (20 files)
- ✅ Cast all `$limit`, `$offset`, and `$perPage` parameters in Model methods (6 files)
  - Tasks_model::getTaskList() - cast $page and $perPage
  - Dashboard_model::jobsbycat() - cast $limit and $offset
  - Content_model::jobsbycat() - cast $limit and $offset
  - Content_model::blogposts() - cast $limit and $offset
  - Users_model::getApiV2Users() - cast $_GET['perPage'] for paginate()
  - User_business_model::getAllList() - cast $_GET['perPage'] for paginate()
  - TimeslipsModel::getApiV2Timeslips() - cast $_GET['perPage'] for paginate() (2 locations)
- Affected controllers: Sales_invoices, Secrets, Users, Companies, Purchase_orders, Work_orders, ScimGroupController, Webpages, Timeslips, Sprints, ScimUserController, Contacts, Categories, Customers, Domains, User_business, Products, Enquiries, Blog, Menu, Jobs, Blog_comments, Gallery, Tasks, Jobapps, Templates, Projects, Employees, Purchase_invoices, Tenants

### 14. Filter header() Function Compatibility
**Error**: `CodeIgniter\Debug\Exceptions->errorHandler` in `APPPATH/Filters/Options.php : 12 — header()`

**Root Cause**: CI 4.5+ filters should use the Response object methods instead of raw PHP `header()` function. Using raw `header()` can cause exceptions when headers have already been sent.

**Fix Applied**:
- ✅ Replaced `header()` calls with `$response->setHeader()` in Options.php filter
- ✅ Use `$request->getMethod()` instead of `$_SERVER['REQUEST_METHOD']`
- ✅ Return proper Response object with status 200 for OPTIONS requests instead of `die()`
- ✅ Return `$request` object from before() method for proper filter chain
- ✅ Return `$response` object from after() method

### 15. PHP 8.4 E_STRICT Deprecation
**Error**: `Deprecated: Constant E_STRICT is deprecated in /var/www/html/app/Config/Boot/production.php on line 11`

**Root Cause**: PHP 8.4 deprecated the `E_STRICT` error constant as it's no longer used in modern PHP versions.

**Fix Applied**:
- ✅ Removed `E_STRICT` from error_reporting() in production.php boot file
- ✅ Updated to: `error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_NOTICE & ~E_USER_DEPRECATED)`

### 16. Spark CLI Bootstrap Deprecation
**Error**: `This "system/bootstrap.php" is no longer used` and `Undefined constant "ENVIRONMENT"` / `Undefined constant "CI_DEBUG"`, then `Call to undefined method CodeIgniter\Boot::bootCLI()`

**Root Cause**: The `spark` CLI tool was still using the deprecated `bootstrap.php` file instead of the new Boot system. CI 4.5+ requires using `Boot::bootSpark()` for command-line operations (not `bootCLI()` which doesn't exist).

**Fix Applied**:
- ✅ Updated spark file to use `Boot::bootSpark($paths)` instead of requiring `bootstrap.php`
- ✅ Removed old Console initialization code
- ✅ This fixes CLI commands, migrations, and all spark-based operations

## Git Commits

1. `88b218b` - Fix: Update Paths.php to use vendor directory for CI 4.6.3
2. `047e231` - Remove old ci4/system directory - now using vendor/codeigniter4/framework/system
3. `f9aeeea` - Update index.php to use Boot.php for CI 4.5+ - Fix bootstrap.php deprecation
4. `a463b58` - Fix index.php to properly call Boot::bootWeb() for CI 4.5+
5. `bc50b64` - docs: Add int environment fix documentation and deployment instructions
6. `0e1a501` - Fix: Add missing helpers property to Autoload.php for CI 4.5+ compatibility
7. `82c93e8` - docs: Update INT_ENVIRONMENT_FIXES.md with all applied fixes and deployment status
8. `5b79f60` - Add missing CI 4.5+ config properties (Modules, App, Exceptions)
9. `0ed5d19` - docs: Update INT_ENVIRONMENT_FIXES.md with config property fixes
10. `4c4f348` - Add missing Feature config class for CI 4.5+
11. `4fb0bb3` - docs: Update INT_ENVIRONMENT_FIXES.md with Feature config fix
12. `0fb87fc` - Fix: Remove Kint\Renderer\Renderer dependency from Kint config
13. `926c192` - Fix: Add missing reservedCharacters property to Cache config
14. `64c7f93` - docs: Update INT_ENVIRONMENT_FIXES.md with Kint and Cache fixes
15. `8a9a55a` - Add missing Routing config class for CI 4.5+
16. `a926d97` - docs: Update INT_ENVIRONMENT_FIXES.md with Routing config fix
17. `f589b90` - Add Session config class for CI 4.5+ compatibility
18. `f44b83c` - docs: Update INT_ENVIRONMENT_FIXES.md with Session config fix
19. `10647d4` - Fix: Change proxyIPs from empty string to empty array for CI 4.5+ compatibility
20. `0934816` - docs: Update INT_ENVIRONMENT_FIXES.md with proxyIPs fix
21. `89fda58` - Fix: Use config helper instead of protected request->config property for CI 4.5+ compatibility
22. `e0afe38` - docs: Update INT_ENVIRONMENT_FIXES.md with BaseController config fix
23. `d0fc27b` - Fix: Disable improved auto-routing and enable legacy auto-routing for backward compatibility
24. `047e231` - Remove outdated CI 4.1.8 system directory (497 files)
25. `f4928b8` - docs: Update INT_ENVIRONMENT_FIXES.md - comprehensive documentation of all 12 fixes
26. `6d72c40` - Fix: Cast limit and offset to int in all API V2 controllers for CI 4.5+ type safety
27. `80491fc` - Fix: Cast limit and offset to int in all non-API controllers for CI 4.5+ type safety
28. `bc540ea` - docs: Update INT_ENVIRONMENT_FIXES.md - Add fix #13 (limit/offset type safety)
29. `126ea89` - Fix: Cast limit/offset/paginate parameters to int in all Models for CI 4.5+ type safety
30. `9040637` - docs: Update INT_ENVIRONMENT_FIXES.md - Add Model fixes for limit/offset/paginate type safety
31. `c497f9d` - Fix: Use Response object instead of raw header() in Options filter for CI 4.5+ compatibility
32. `afebea0` - Fix: Remove deprecated E_STRICT constant for PHP 8.4 compatibility
33. `7282c48` - docs: Update INT_ENVIRONMENT_FIXES.md - Add fix #15 (E_STRICT PHP 8.4 deprecation)
34. `caf9b2f` - Fix: Update spark CLI tool to use Boot::bootCLI() for CI 4.5+ compatibility (incorrect)
35. `b8b6933` - docs: Update INT_ENVIRONMENT_FIXES.md - Add fix #16 (spark CLI bootstrap)
36. `16ded32` - Fix: Update spark to use Boot::bootSpark() (corrected from bootCLI)

## Deployment Status

### Completed
- [x] Code changes committed and pushed to `devops-full-automation` branch
- [x] Kubernetes deployment restarted (pods running with old image)

### Required Actions
The int environment is currently running Docker image:
```
bwalia/webimpetus:2b10e43045e11416180f5f979757a41c3ec42072
```

**To apply the fixes, a new Docker image must be built with the latest code and deployed:**

1. **Build New Docker Image**:
   ```bash
   cd /path/to/workstation-ci4
   git checkout devops-full-automation
   git pull origin devops-full-automation
   
   # Build with new commit hash
   NEW_TAG=$(git rev-parse HEAD)
   cd devops/docker
   docker build -t bwalia/webimpetus:${NEW_TAG} -f Dockerfile ../..
   docker push bwalia/webimpetus:${NEW_TAG}
   ```

2. **Update Kubernetes Deployment**:
   ```bash
   kubectl set image deployment/wsl-int wsl-int=bwalia/webimpetus:${NEW_TAG} -n int
   kubectl rollout status deployment/wsl-int -n int
   ```

3. **Verify Fix**:
   ```bash
   # Test from inside pod
   kubectl exec -n int $(kubectl get pods -n int | grep wsl-int | grep Running | head -1 | awk '{print $1}') -- curl -s http://localhost/
   
   # Test from browser
   open https://int-my.workstation.co.uk/
   ```

## Expected Result

After deployment, the int environment should:
- ✅ No longer show PHP 8.4 session deprecation errors
- ✅ No longer show bootstrap.php deprecation message
- ✅ Successfully load CodeIgniter 4.6.3 from vendor directory
- ✅ Display the login page correctly

## Additional Notes

- Production, Test, and ACC environments are already running correctly with CI 4.6.3
- These fixes ensure all environments use the same modern CI 4.6+ architecture
- The old `ci4/system/` directory has been permanently removed from the repository
- All future deployments will automatically use CI 4.6.3 from vendor directory
