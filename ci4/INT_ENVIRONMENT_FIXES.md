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
   cd /path/to/webimpetus-src
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
