# Menu Management Scripts - Final Update

## What Was Fixed

All PHP scripts were updated to run from the **host machine** instead of inside Docker containers, fixing the path resolution issue.

---

## Quick Start

### 1. Test Your Setup

```bash
# Test database connection
php SQLs/test_connection.php
```

**Expected output:**
```
✓ Connection successful!
✓ Query successful
  Menu items in database: 45
✓ Admin user found
✓ All tests passed!
```

### 2. Run Menu Scripts

```bash
# Check what routes are missing (safe - no changes)
./run_menu_scripts.sh check

# Add missing routes to menu table
./run_menu_scripts.sh add

# Grant all permissions to admin user
./run_menu_scripts.sh grant

# Or run everything interactively
./run_menu_scripts.sh all
```

---

## What Changed

### Files Updated (4 total)

1. **grant_all_menu_permissions_to_admin.php**
   - Now connects to `127.0.0.1:3309`
   - Runs from host machine

2. **add_missing_routes_to_menu.php**
   - Now connects to `127.0.0.1:3309`
   - Runs from host machine

3. **check_missing_routes.php**
   - Now connects to `127.0.0.1:3309`
   - Runs from host machine

4. **run_menu_scripts.sh**
   - Complete rewrite
   - Checks PHP requirements
   - Runs scripts directly (not via Docker)

### New Files (3 total)

1. **test_connection.php**
   - Quick database connection test
   - Validates setup before running main scripts

2. **UPDATED_README.md**
   - Comprehensive guide for new setup
   - Troubleshooting section
   - Requirements and installation

3. **SCRIPTS_UPDATE_SUMMARY.md**
   - Technical details of changes
   - Before/after comparison
   - Migration notes

---

## Requirements

### PHP Must Be Installed

The scripts now run on your host machine, so PHP must be installed.

**Check if you have PHP:**
```bash
php --version
php -m | grep mysqli
```

**If not installed:**

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

---

## Troubleshooting

### "PHP is not installed"

Install PHP using the commands above in the Requirements section.

### "mysqli extension is not installed"

**Ubuntu/Debian:**
```bash
sudo apt install php-mysqli
```

**RHEL/CentOS:**
```bash
sudo yum install php-mysqlnd
```

### "Cannot connect to database"

1. **Check if database container is running:**
   ```bash
   docker ps | grep webimpetus-db
   ```

2. **Check if port 3309 is exposed:**
   ```bash
   docker port webimpetus-db
   ```
   Should show: `3306/tcp -> 0.0.0.0:3309`

3. **Test connection manually:**
   ```bash
   mysql -h 127.0.0.1 -P 3309 -u wsl_dev -p'CHANGE_ME' myworkstation_dev
   ```

4. **Run the test script:**
   ```bash
   php SQLs/test_connection.php
   ```

---

## How It Works

### Connection Architecture

```
┌──────────────────┐
│   Host Machine   │
│                  │
│  PHP Scripts ────┼───► TCP: localhost:3309
│                  │
└──────────────────┘
          │
          │ Network Connection
          ▼
┌──────────────────┐
│ Docker Container │
│                  │
│  MariaDB:3306 ───┼───► Exposed as port 3309
│                  │
└──────────────────┘
```

### Database Credentials

All scripts use:
- **Host:** 127.0.0.1 (localhost)
- **Port:** 3309
- **Database:** myworkstation_dev
- **Username:** wsl_dev
- **Password:** CHANGE_ME

---

## Usage Examples

### Example 1: Check Missing Routes (Safe)

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

✓ Connected to database: myworkstation_dev

Routes NOT in Menu Table (7):
Name                           Link                           Category
------------------------------------------------------------------------------------
Chart of Accounts              /accounts                      Accounting
Journal Entries                /journal-entries               Accounting
Accounting Periods             /accounting-periods            Accounting
Balance Sheet                  /balance-sheet                 Financial Reports
Trial Balance                  /trial-balance                 Financial Reports
Profit & Loss                  /profit-loss                   Financial Reports
API Documentation              /swagger                       Developer Tools
```

### Example 2: Add Missing Routes

```bash
$ ./run_menu_scripts.sh add

✓ PHP version: 8.2.12
✓ PHP mysqli extension: installed
✓ Database port 3309: accessible

Running: add_missing_routes_to_menu.php
==================================

Step 1: Checking current menu items...
  Current menu items: 45

Step 2: Checking for existing routes...
  Found 45 existing menu links

Step 3: Calculating next sort order...
  Next sort order: 460

Step 4: Adding missing routes...
------------------------------------------------------------------------------------
  ✓ ADDED: Chart of Accounts (ID: 46, Link: /accounts)
  ✓ ADDED: Journal Entries (ID: 47, Link: /journal-entries)
  ✓ ADDED: Accounting Periods (ID: 48, Link: /accounting-periods)
  ✓ ADDED: Balance Sheet (ID: 49, Link: /balance-sheet)
  ✓ ADDED: Trial Balance (ID: 50, Link: /trial-balance)
  ✓ ADDED: Profit & Loss (ID: 51, Link: /profit-loss)
  ✓ ADDED: API Documentation (ID: 52, Link: /swagger)
------------------------------------------------------------------------------------

Step 5: Verifying results...
  New total menu items: 52
  Items added: 7
  Items skipped: 0

Step 7: Updating admin user permissions...
  ✓ Updated admin@admin.com permissions
  Total permissions: 52

✓ SUCCESS
Added 7 new menu item(s) to the system.
Admin user permissions have been updated.
```

---

## What's Next

After running the scripts:

1. **Refresh your browser**
   - New menu items should appear in the sidebar

2. **Check admin access**
   - Login as admin@admin.com
   - Verify all menu items are visible

3. **Test new routes**
   - Click on new menu items
   - Verify pages load correctly

---

## Commands Reference

### Test Connection
```bash
php SQLs/test_connection.php
```

### Check Missing Routes (Safe)
```bash
./run_menu_scripts.sh check
# or
php SQLs/check_missing_routes.php
```

### Add Missing Routes
```bash
./run_menu_scripts.sh add
# or
php SQLs/add_missing_routes_to_menu.php
```

### Grant All Permissions to Admin
```bash
./run_menu_scripts.sh grant
# or
php SQLs/grant_all_menu_permissions_to_admin.php
```

### Run All Steps Interactively
```bash
./run_menu_scripts.sh all
```

### Show Help
```bash
./run_menu_scripts.sh
```

---

## Support

### If Scripts Don't Work

1. Run the test script first:
   ```bash
   php SQLs/test_connection.php
   ```

2. Check the wrapper script output:
   ```bash
   ./run_menu_scripts.sh
   ```
   It will show what's missing (PHP, mysqli, database connection)

3. Review error messages - they provide specific guidance

### Additional Help

- **UPDATED_README.md** - Comprehensive guide
- **SCRIPTS_UPDATE_SUMMARY.md** - Technical details
- Test connection manually: `mysql -h 127.0.0.1 -P 3309 -u wsl_dev -p'CHANGE_ME'`

---

## Summary

✅ **Scripts Updated** - Run from host, not Docker
✅ **Database Connection** - Via localhost:3309
✅ **Test Script Added** - Quick validation tool
✅ **Requirements Checker** - Validates PHP and mysqli
✅ **Error Messages** - Helpful troubleshooting guidance

**Status:** Ready to Use

**Quick Test:**
```bash
php SQLs/test_connection.php && echo "All good! Ready to run scripts."
```

---

**Updated:** 2025-10-11
