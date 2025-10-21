# Menu Management Scripts - Updated for Host Execution

## What Changed

All PHP scripts have been updated to run directly from the **host machine** instead of inside the Docker container. This resolves the issue where the `SQLs/` directory wasn't mounted in the container.

### Previous Setup (❌ Didn't Work)
```bash
docker exec workerra-ci-dev php /var/www/html/SQLs/script.php  # Path didn't exist
```

### New Setup (✅ Works)
```bash
php SQLs/script.php  # Runs directly on host, connects via port 3309
```

## Database Connection

All scripts now connect to the database via the **exposed Docker port**:

- **Host:** 127.0.0.1 (localhost)
- **Port:** 3309
- **Database:** myworkstation_dev
- **User:** workerra-ci-dev
- **Password:** CHANGE_ME

The connection is configured in each script:
```php
$config = [
    'hostname' => '127.0.0.1',
    'port' => 3309,
    'username' => 'workerra-ci-dev',
    'password' => 'CHANGE_ME',
    'database' => 'myworkstation_dev',
];
```

## Requirements

### PHP Must Be Installed on Host

Check if you have PHP:
```bash
php --version
```

If not installed:

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install php-cli php-mysqli
```

**RHEL/CentOS:**
```bash
sudo yum install php-cli php-mysqlnd
```

**macOS (Homebrew):**
```bash
brew install php
```

### Verify PHP Extensions

Check if mysqli is available:
```bash
php -m | grep mysqli
```

Should output: `mysqli`

## Usage

### Option 1: Using the Shell Wrapper (Recommended)

The wrapper script checks requirements and provides helpful error messages:

```bash
# Show available commands
./run_menu_scripts.sh

# Check missing routes (safe - no changes)
./run_menu_scripts.sh check

# Add missing routes to menu
./run_menu_scripts.sh add

# Grant all permissions to admin
./run_menu_scripts.sh grant

# Run all steps interactively
./run_menu_scripts.sh all
```

### Option 2: Running Scripts Directly

```bash
# Check missing routes
php SQLs/check_missing_routes.php

# Add missing routes
php SQLs/add_missing_routes_to_menu.php

# Grant permissions
php SQLs/grant_all_menu_permissions_to_admin.php
```

## What the Wrapper Does

When you run `./run_menu_scripts.sh`, it:

1. ✅ Checks if PHP is installed
2. ✅ Checks if mysqli extension is available
3. ✅ Verifies database port 3309 is accessible
4. ✅ Shows PHP version
5. ✅ Runs the requested script
6. ✅ Handles errors gracefully

**Example Output:**
```bash
$ ./run_menu_scripts.sh check

==================================
Menu Management Script Runner
==================================

✓ PHP version: 8.2.0
✓ PHP mysqli extension: installed
✓ Database port 3309: accessible

Running: check_missing_routes.php
==================================

✓ Connected to database: myworkstation_dev
...
```

## Troubleshooting

### Error: "PHP is not installed"

Install PHP using the commands in the Requirements section above.

### Error: "mysqli extension is not installed"

**Ubuntu/Debian:**
```bash
sudo apt install php-mysqli
```

**RHEL/CentOS:**
```bash
sudo yum install php-mysqlnd
```

**macOS:**
```bash
# mysqli is usually included with PHP from Homebrew
brew reinstall php
```

### Error: "Cannot connect to database on localhost:3309"

1. Check if database container is running:
   ```bash
   docker ps | grep workerra-ci-db
   ```

2. Check if port 3309 is exposed:
   ```bash
   docker port workerra-ci-db
   ```

   Should show: `3306/tcp -> 0.0.0.0:3309`

3. Test connection manually:
   ```bash
   mysql -h 127.0.0.1 -P 3309 -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev
   ```

### Error: "Connection failed: Access denied"

Check database credentials in the script match your `.env` file:
```bash
grep database.default .env
```

## Scripts Updated

All three PHP scripts have been updated:

1. ✅ `grant_all_menu_permissions_to_admin.php`
2. ✅ `add_missing_routes_to_menu.php`
3. ✅ `check_missing_routes.php`

Changes made to each:
- Updated connection to use `127.0.0.1:3309`
- Added port parameter to mysqli constructor
- Updated usage documentation
- Removed Docker-specific instructions

## Testing

Test the setup:

```bash
# 1. Check PHP
php --version

# 2. Check mysqli
php -m | grep mysqli

# 3. Test database connection
php -r "new mysqli('127.0.0.1', 'workerra-ci-dev', 'CHANGE_ME', 'myworkstation_dev', 3309) or die('Failed');"

# 4. Run check script (safe)
./run_menu_scripts.sh check
```

If all tests pass, you're ready to use the scripts!

## Benefits of New Setup

✅ **No Docker path issues** - Scripts run directly on host
✅ **Faster execution** - No Docker exec overhead
✅ **Better error messages** - Direct PHP error output
✅ **Easier debugging** - Can add debug statements easily
✅ **Standard workflow** - Works like any PHP script
✅ **Port forwarding** - Uses exposed database port

## Security Note

The scripts connect to the database using credentials stored in plain text. This is acceptable for development but ensure:

- Scripts are not committed with real production credentials
- Port 3309 is not exposed to the internet
- Scripts are only run in development/staging environments

## Next Steps

1. Test the scripts with `./run_menu_scripts.sh check`
2. If successful, add missing routes with `./run_menu_scripts.sh add`
3. Check the results in your browser

## Support

If you encounter issues:

1. Run `./run_menu_scripts.sh` without arguments to see requirements
2. Check the error messages - they provide specific guidance
3. Verify database connection manually
4. Check PHP and mysqli are properly installed

---

**Updated:** 2025-10-11
**Author:** Claude Code
