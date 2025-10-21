# CodeIgniter 4 Upgrade Notes

**Date:** October 6, 2025  
**Status:** ✅ Production Already Upgraded  
**Action:** Local Development Environment Updated

---

## Production Environment Status

### Current Versions (Verified in Kubernetes)
- **PHP:** 8.3.12 (fpm-fcgi) - Built Sep 26, 2024
- **CodeIgniter:** 4.6.3 (Latest)
- **Base Image:** bwalia/openresty-php:latest (Alpine Linux 3.22.1)
- **Zend Engine:** v4.3.12
- **Xdebug:** v3.3.2

### Environment Verification
| Environment | PHP Version | CI4 Version | Pod Status |
|-------------|-------------|-------------|------------|
| Production  | 8.3.12      | 4.6.3       | ✅ Running |
| Test        | 8.3.12      | 4.6.3       | ✅ Running |
| ACC         | 8.3.12      | 4.6.3       | ✅ Running |

---

## Changes Made

### 1. Updated `ci4/composer.json`
**Before:**
```json
"php": "^7.3 || ^8.0",
"codeigniter4/framework": "^4.2.10",
```

**After:**
```json
"php": "^8.1 || ^8.2 || ^8.3",
"codeigniter4/framework": "^4.6",
```

### 2. Updated Dockerfile Comments
Clarified that the application is now running CI 4.6.x with PHP 8.3.12 in production.

---

## Next Steps for Local Development

### Prerequisites
1. **Install PHP 8.1+ locally** (recommended: PHP 8.3)
   - macOS: `brew install php@8.3`
   - Check version: `php --version`

2. **Install Composer** (if not already installed)
   - Check version: `composer --version`

### Update Local Dependencies

```bash
cd /Users/balinderwalia/Documents/Work/workstation-ci4/ci4

# Clear composer cache
composer clear-cache

# Update dependencies to latest versions
composer update

# Or install fresh (if you want clean install)
rm -rf vendor composer.lock
composer install
```

### Verify the Update

```bash
# Check installed CodeIgniter version
cat vendor/codeigniter4/framework/system/CodeIgniter.php | grep CI_VERSION

# Expected output: public const CI_VERSION = '4.6.3';
```

---

## Breaking Changes from 4.2.10 to 4.6.3

### Major Updates Included:
- **4.3.x:** Enhanced routing, improved validation
- **4.4.x:** PHP 8.2 support, security improvements
- **4.5.x:** Performance optimizations, bug fixes
- **4.6.x:** PHP 8.3 support, latest features

### PHP Requirements:
- **Minimum:** PHP 8.1
- **Recommended:** PHP 8.3.12 (matches production)
- **EOL Warning:** PHP 8.1 reaches end-of-life on December 31, 2025

### Dependencies Compatibility:
All your current dependencies are compatible with PHP 8.3 and CI 4.6:
- ✅ firebase/php-jwt: ^5.4
- ✅ agungsugiarto/codeigniter4-cors: ^2.0
- ✅ mpdf/mpdf: ^8.1
- ✅ phpmailer/phpmailer: ^6.8
- ✅ zircote/swagger-php: ^4.7
- ✅ symfony/yaml: ^6.4
- ✅ jumbojett/openid-connect-php: ^1.0

---

## Docker Build Process

The Dockerfile automatically handles the upgrade:
```dockerfile
COPY ci4 /src
WORKDIR /src
RUN composer update  # ← This ensures latest versions
```

When building the image:
```bash
./build.sh
# or
docker build -f devops/docker/Dockerfile --build-arg BASE_TAG=latest -t webimpetus .
```

---

## Testing Checklist

After updating local environment:

- [ ] Run local tests: `composer test`
- [ ] Test API endpoints with authentication
- [ ] Verify CORS configuration
- [ ] Test JWT token generation/validation
- [ ] Test OpenID Connect integration
- [ ] Verify database connections
- [ ] Test file uploads (if applicable)
- [ ] Check error logging and debugging

---

## Rollback Plan

If issues occur, you can temporarily revert:

```bash
# Revert composer.json
git checkout HEAD -- ci4/composer.json

# Install old versions
cd ci4
composer install
```

However, note that production is already running 4.6.3 successfully.

---

## Resources

- [CodeIgniter 4.6.3 Release Notes](https://github.com/codeigniter4/CodeIgniter4/releases/tag/v4.6.3)
- [CodeIgniter 4 User Guide](https://codeigniter4.github.io/userguide/)
- [Upgrade Instructions](https://codeigniter4.github.io/userguide/installation/upgrading.html)
- [PHP 8.3 Migration Guide](https://www.php.net/manual/en/migration83.php)

---

## Notes

- Production has been running CI 4.6.3 + PHP 8.3.12 successfully
- The Docker build process with `composer update` ensures consistency
- Base image `bwalia/openresty-php:latest` provides the PHP 8.3.12 runtime
- All environments (prod, test, acc) are synchronized and working

**No urgent action required** - this update aligns local development with production.
