# Scripts Update Summary - Host Execution Fix

**Date:** 2025-10-11
**Issue:** SQLs directory not mounted in Docker container
**Solution:** Updated scripts to run from host machine

---

## Problem

The original scripts were designed to run inside the Docker container:
```bash
docker exec workerra-ci-dev php /var/www/html/SQLs/script.php
```

However, the `SQLs/` directory was not mounted in the container, causing:
```
Error: Script not found in container: /var/www/html/SQLs/...
```

## Solution

Updated all PHP scripts to:
1. Run directly from the **host machine**
2. Connect to database via **exposed port 3309**
3. Use **localhost (127.0.0.1)** instead of container hostname

## Files Modified

### 1. grant_all_menu_permissions_to_admin.php
**Changes:**
```php
// OLD (didn't work)
$config = [
    'hostname' => 'workerra-ci-db',
    'username' => 'workerra-ci-dev',
    'password' => 'CHANGE_ME',
    'database' => 'myworkstation_dev',
];

// NEW (works from host)
$config = [
    'hostname' => '127.0.0.1',
    'port' => 3309,
    'username' => 'workerra-ci-dev',
    'password' => 'CHANGE_ME',
    'database' => 'myworkstation_dev',
];
```

**MySQL Connection:**
```php
// Added port parameter
$mysqli = new mysqli(
    $config['hostname'],
    $config['username'],
    $config['password'],
    $config['database'],
    $config['port']  // <-- Added this
);
```

### 2. add_missing_routes_to_menu.php
Same changes as above:
- Updated hostname to `127.0.0.1`
- Added `port` => `3309`
- Added port parameter to mysqli constructor

### 3. check_missing_routes.php
Same changes as above:
- Updated hostname to `127.0.0.1`
- Added `port` => `3309`
- Added port parameter to mysqli constructor

### 4. run_menu_scripts.sh (Complete Rewrite)

**OLD Approach:**
```bash
# Tried to run scripts via Docker
docker exec workerra-ci-dev php /var/www/html/SQLs/script.php
```

**NEW Approach:**
```bash
# Runs scripts directly on host
php SQLs/script.php
```

**New Features:**
- ✅ Checks if PHP is installed
- ✅ Checks if mysqli extension is available
- ✅ Verifies database port 3309 is accessible
- ✅ Shows PHP version
- ✅ Provides helpful error messages
- ✅ Validates script exists before running
- ✅ Handles exit codes properly

## How It Works Now

### Database Connection Flow

```
┌─────────────┐
│ Host Machine│
│             │
│  PHP Script ├──┐
│             │  │
└─────────────┘  │
                 │ TCP Connection
                 │ localhost:3309
                 │
                 ▼
┌─────────────────────────┐
│ Docker Container        │
│                         │
│  workerra-ci-db          │
│  ┌──────────────┐       │
│  │  MariaDB     │       │
│  │  Port: 3306  │◄──────┼── Exposed as 3309
│  └──────────────┘       │
│                         │
└─────────────────────────┘
```

### Port Mapping

- **Container Internal Port:** 3306 (MariaDB default)
- **Host Exposed Port:** 3309
- **Scripts Connect To:** 127.0.0.1:3309

This is configured in `docker-compose.yml`:
```yaml
services:
  workerra-ci-db:
    ports:
      - "3309:3306"
```

## Requirements

### Must Have PHP on Host

**Check:**
```bash
php --version
php -m | grep mysqli
```

**Install if needed:**

Ubuntu/Debian:
```bash
sudo apt install php-cli php-mysqli
```

RHEL/CentOS:
```bash
sudo yum install php-cli php-mysqlnd
```

macOS:
```bash
brew install php
```

## Usage Examples

### Before (Didn't Work)
```bash
# This failed because path didn't exist
docker exec workerra-ci-dev php /var/www/html/SQLs/check_missing_routes.php
```

### After (Works)
```bash
# Option 1: Via wrapper (recommended)
./run_menu_scripts.sh check

# Option 2: Direct PHP execution
php SQLs/check_missing_routes.php
```

## Verification

Test the setup:

```bash
# 1. Verify PHP installation
./run_menu_scripts.sh

# Output should show:
# ✓ PHP version: X.X.X
# ✓ PHP mysqli extension: installed
# ✓ Database port 3309: accessible

# 2. Run safe check (no changes)
./run_menu_scripts.sh check

# Should connect successfully and show missing routes

# 3. Test database connection manually
php -r "new mysqli('127.0.0.1', 'workerra-ci-dev', 'CHANGE_ME', 'myworkstation_dev', 3309) or die('Connection failed');"

# Should output nothing (success) or error message
```

## Benefits

### Advantages of New Approach

✅ **Works Immediately** - No Docker path issues
✅ **Faster** - No Docker exec overhead
✅ **Better Debugging** - Direct error messages
✅ **Standard Workflow** - Like any PHP script
✅ **Easier to Maintain** - No container path dependencies
✅ **More Portable** - Works on any system with PHP

### Disadvantages (Minor)

⚠️ **Requires PHP on Host** - Must install PHP
⚠️ **Port Must Be Exposed** - Database port 3309 must be accessible
⚠️ **Credentials in Plain Text** - Dev environment only

## Testing Results

All three scripts tested successfully:

```bash
$ ./run_menu_scripts.sh check

==================================
Menu Management Script Runner
==================================

✓ PHP version: 8.2.12
✓ PHP mysqli extension: installed
✓ Database port 3309: accessible

Running: check_missing_routes.php
==================================

================================================================================
Check Missing Routes (Dry Run)
This script will NOT modify the database
================================================================================

✓ Connected to database: myworkstation_dev

Current Menu Status:
--------------------------------------------------------------------------------
  Total menu items: 45
  Existing routes: 45

[... rest of output ...]
```

## Migration Notes

If you have other scripts that need similar updates:

1. Change hostname from `workerra-ci-db` to `127.0.0.1`
2. Add `'port' => 3309` to config
3. Add port as 5th parameter to mysqli constructor
4. Update documentation to reflect host execution

## Rollback (If Needed)

If you need to revert to Docker execution:

1. Mount SQLs directory in docker-compose.yml:
   ```yaml
   volumes:
     - ./SQLs:/var/www/html/SQLs
   ```

2. Revert scripts to use `workerra-ci-db` hostname
3. Remove port parameter
4. Update run_menu_scripts.sh to use docker exec

However, the new approach is recommended as it's simpler and more reliable.

## Documentation Updated

New/Updated files:

1. ✅ `grant_all_menu_permissions_to_admin.php` - Updated connection
2. ✅ `add_missing_routes_to_menu.php` - Updated connection
3. ✅ `check_missing_routes.php` - Updated connection
4. ✅ `run_menu_scripts.sh` - Complete rewrite
5. ✅ `UPDATED_README.md` - New comprehensive guide
6. ✅ `SCRIPTS_UPDATE_SUMMARY.md` - This file

## Quick Reference

### Connection Parameters
```php
Host: 127.0.0.1
Port: 3309
User: workerra-ci-dev
Pass: CHANGE_ME
DB:   myworkstation_dev
```

### Run Commands
```bash
./run_menu_scripts.sh check  # Dry run
./run_menu_scripts.sh add    # Add routes
./run_menu_scripts.sh grant  # Update perms
./run_menu_scripts.sh all    # Do everything
```

### Test Connection
```bash
php -r "new mysqli('127.0.0.1', 'workerra-ci-dev', 'CHANGE_ME', 'myworkstation_dev', 3309) or die('Failed');"
```

---

## Summary

✅ **Problem Fixed** - Scripts now run from host machine
✅ **Database Connection** - Via exposed port 3309
✅ **Requirements** - PHP + mysqli must be installed on host
✅ **Usage** - `./run_menu_scripts.sh [command]`
✅ **Tested** - All three scripts working correctly

**Status:** Ready to Use

---

*Last Updated: 2025-10-11*
