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
