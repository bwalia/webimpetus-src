# PHP Database Anonymization Guide

## Overview

This guide explains how to use the PHP scripts to anonymize your database. These scripts are more reliable than SQL scripts and provide better error handling and progress tracking.

**Created:** 2025-10-12
**Purpose:** Anonymize ALL PII data using PHP scripts
**Database:** myworkstation_dev

---

## 📦 Available Scripts

### 1. `backup_database.php` - Database Backup
- Creates timestamped backup before anonymization
- Compresses backup automatically (gzip)
- Verifies backup integrity
- Shows compression statistics

### 2. `anonymize_database.php` - Main Anonymization
- Anonymizes 35+ tables with PII data
- Preserves ONLY admin@admin.com
- Shows real-time progress
- Provides detailed statistics

### 3. `verify_anonymization.php` - Verification
- Verifies all data is anonymized
- Checks 14+ verification rules
- Reports any issues found
- Exit codes for automation

---

## 🚀 Quick Start

### Step 1: Backup Database
```bash
cd /home/bwalia/workstation-ci4
php scripts/backup_database.php
```

### Step 2: Run Anonymization
```bash
php scripts/anonymize_database.php
```

### Step 3: Verify Results
```bash
php scripts/verify_anonymization.php
```

### All-in-One Command
```bash
cd /home/bwalia/workstation-ci4
php scripts/backup_database.php && php scripts/anonymize_database.php && php scripts/verify_anonymization.php
```

---

## 📝 Detailed Usage

### backup_database.php

**Purpose:** Create backup before anonymization

**Output:**
```
==============================================================
  DATABASE BACKUP BEFORE ANONYMIZATION
==============================================================

[1/3] Creating database backup...
  Database: myworkstation_dev
  File: myworkstation_dev_before_anonymization_20251012_143022.sql
✓ Backup created successfully (45.2MB)

[2/3] Compressing backup...
✓ Backup compressed successfully
  Original size: 45.2MB
  Compressed size: 8.7MB
  Compression: 80.8%

[3/3] Verifying backup...
✓ Backup verified successfully

==============================================================
  BACKUP COMPLETE
==============================================================

Backup Details:
  • File: /home/bwalia/workstation-ci4/backups/myworkstation_dev_before_anonymization_20251012_143022.sql.gz
  • Size: 8.7MB
  • Database: myworkstation_dev
  • Timestamp: 2025-10-12 14:30:22
```

**Features:**
- Automatic compression with gzip
- Backup verification
- Human-readable file sizes
- Timestamped filenames
- Creates backup directory if needed

---

### anonymize_database.php

**Purpose:** Anonymize all PII data in database

**Output:**
```
============================================================
  DATABASE ANONYMIZATION SCRIPT
============================================================

Starting anonymization process...

[1/35] Anonymizing Users...
  ✓ users: 47 rows updated
[2/35] Anonymizing Businesses...
  ✓ businesses: 23 rows updated
[3/35] Anonymizing Employees...
  ✓ employees: 15 rows updated
...
[31/35] Anonymizing Companies...
  ✓ companies: 156 rows updated
[32/35] Anonymizing Contacts...
  ✓ contacts: 342 rows updated
[33/35] Anonymizing Sales Invoices...
  ✓ sales_invoices: 89 rows updated
[34/35] Anonymizing Sales Invoice Items...
  ✓ sales_invoice_items: 234 rows updated
[35/35] Anonymizing Timeslips...
  ✓ timeslips: 567 rows updated

============================================================
  ANONYMIZATION COMPLETE
============================================================

Total rows anonymized: 2,145
Tables processed: 35

Top 10 Tables by Rows Anonymized:
  timeslips:                     567 rows
  contacts:                      342 rows
  sales_invoice_items:           234 rows
  companies:                     156 rows
  sales_invoices:                89 rows
  users:                         47 rows
  businesses:                    23 rows
  employees:                     15 rows
  ...

------------------------------------------------------------
Preserved: admin@admin.com user account ONLY
All other data has been anonymized
------------------------------------------------------------
```

**Features:**
- Progress tracking (1/35, 2/35, etc.)
- Real-time row counts
- Color-coded output (✓ = green, ✗ = red)
- Summary statistics
- Top 10 tables report
- Error handling per table

**What Gets Anonymized:**

| Table | Fields Anonymized |
|-------|------------------|
| **Users** | name, email, phone, address, city, postcode, password |
| **Companies** | company_name, email, phone, address, website, VAT |
| **Contacts** | first_name, surname, email, phone, mobile, LinkedIn |
| **Sales Invoices** | invoice_number, bill_to, notes, project_code, PO |
| **Invoice Items** | description |
| **Timeslips** | task_name, employee_name, description |
| **+ 29 more tables** | All PII fields |

---

### verify_anonymization.php

**Purpose:** Verify anonymization was successful

**Output:**
```
======================================================================
  ANONYMIZATION VERIFICATION
======================================================================

[1] Verifying Users...
  ✓ Users with anonymized emails
  ✓ Users with anonymized phones

[2] Verifying Businesses...
  ✓ Businesses with anonymized emails
  ✓ Businesses with anonymized names

[3] Verifying Employees...
  ✓ Employees with anonymized emails
  ✓ Employees with anonymized names

...

[6] Verifying Companies...
  ✓ Companies with anonymized emails
  ✓ Companies with anonymized names
  ℹ Total companies: 156 records

[7] Verifying Contacts...
  ✓ Contacts with anonymized emails
  ✓ Contacts with anonymized first names
  ℹ Total contacts: 342 records

[8] Verifying Sales Invoices...
  ✓ Invoices with anonymized numbers
  ✓ Invoices with anonymized bill_to
  ℹ Total sales invoices: 89 records

...

[14] Verifying Admin User Preservation...
  ✓ Admin user preserved (admin@admin.com)

======================================================================
  VERIFICATION SUMMARY
======================================================================

Total Checks: 45
Passed: 45
Failed: 0

✓ All checks passed!
✓ Database has been successfully anonymized
✓ Only admin@admin.com user preserved
```

**Features:**
- 45+ verification checks
- Pattern matching for anonymized data
- Admin user preservation check
- Exit code 0 = success, 1 = failure
- Detailed issue reporting if problems found

**Verification Rules:**
- Email domains must be `@example.com` or `@*.example.com`
- Names must match patterns (User_, Contact%, etc.)
- Phone numbers must match `555-XXX-XXXX` pattern
- Invoice numbers must match `INV-XXXXXX` pattern
- Admin user must exist and be unchanged

---

## 🔄 Restore from Backup

If something goes wrong, restore from backup:

```bash
# Find your backup
ls -lh /home/bwalia/workstation-ci4/backups/

# Decompress
gunzip /home/bwalia/workstation-ci4/backups/myworkstation_dev_before_anonymization_TIMESTAMP.sql.gz

# Restore
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < \
  /home/bwalia/workstation-ci4/backups/myworkstation_dev_before_anonymization_TIMESTAMP.sql
```

---

## ⚙️ Configuration

All scripts use the same configuration:

```php
// Database connection
define('DB_HOST', 'webimpetus-db');
define('DB_NAME', 'myworkstation_dev');
define('DB_USER', 'wsl_dev');
define('DB_PASS', 'CHANGE_ME');

// Backup settings
define('BACKUP_DIR', '/home/bwalia/workstation-ci4/backups');

// Preserved data
define('ADMIN_EMAIL', 'admin@admin.com');
```

**To modify:**
1. Edit the script file
2. Change the `define()` values at the top
3. Save and run

---

## 🐛 Troubleshooting

### Issue: Permission Denied

```bash
chmod +x /home/bwalia/workstation-ci4/*.php
```

### Issue: Database Connection Failed

**Check Docker container:**
```bash
docker ps | grep webimpetus-db
```

**Verify credentials:**
```bash
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' -e "SELECT DATABASE();"
```

### Issue: Backup Failed

**Check disk space:**
```bash
df -h /home/bwalia/workstation-ci4/backups
```

**Check Docker exec:**
```bash
docker exec webimpetus-db mariadb-dump --version
```

### Issue: Some Tables Not Anonymized

**Run verification to see which tables:**
```bash
php scripts/verify_anonymization.php
```

**Check for table existence:**
```bash
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev -e "SHOW TABLES;"
```

### Issue: Script Hangs or Crashes

**Check PHP memory limit:**
```bash
php -i | grep memory_limit
```

**Increase if needed (in php.ini):**
```ini
memory_limit = 512M
```

**Or run with:**
```bash
php -d memory_limit=512M anonymize_database.php
```

---

## 📊 Data Transformation Examples

### Before → After

**Users:**
```
john.smith@company.com → user_123@example.com
07712345678 → 555-0000123
```

**Companies:**
```
Acme Corp Ltd → Anonymous Company 123
info@acme.com → company_123@example.com
```

**Contacts:**
```
John Smith → Contact123 Person123
j.smith@company.com → contact_123@example.com
```

**Invoices:**
```
INV-REAL-12345 → INV-000123
"ABC Corp, 123 Real St" → "Anonymous Customer 123\n123 Customer Street"
```

**Timeslips:**
```
Task: Build homepage → Task: Task 123
Employee: John Smith → Employee: Employee 1
```

---

## 🔐 Security Notes

### ⚠️ CRITICAL
- **NEVER run on production!** Check database name before running
- **ALWAYS backup first** - Script does NOT auto-backup
- **Verify database:** Confirm you're on `myworkstation_dev`
- **Keep backups:** Retain for at least 30 days

### Verification Before Running

```bash
# Check current database
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' -e "SELECT DATABASE();"

# Should output: myworkstation_dev
# If not, DO NOT RUN SCRIPTS!
```

### Password Security
- All anonymized users get password: `password`
- Hash: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`
- Admin password is preserved

---

## 📅 Automation

### Weekly Anonymization (Cron Job)

```bash
# Edit crontab
crontab -e

# Add this line (runs every Monday at 2 AM)
0 2 * * 1 cd /home/bwalia/workstation-ci4 && php scripts/backup_database.php && php scripts/anonymize_database.php
```

### CI/CD Integration

```yaml
# Example GitHub Actions
steps:
  - name: Backup Database
    run: php scripts/backup_database.php

  - name: Anonymize Database
    run: php scripts/anonymize_database.php

  - name: Verify Anonymization
    run: php scripts/verify_anonymization.php
```

### Shell Script Wrapper

```bash
#!/bin/bash
# anonymize.sh

cd /home/bwalia/workstation-ci4

echo "Step 1: Backup..."
php scripts/backup_database.php || exit 1

echo "Step 2: Anonymize..."
php scripts/anonymize_database.php || exit 1

echo "Step 3: Verify..."
php scripts/verify_anonymization.php || exit 1

echo "✓ Complete!"
```

---

## 📈 Performance

### Typical Run Times

| Database Size | Backup Time | Anonymize Time | Verify Time | Total |
|---------------|-------------|----------------|-------------|-------|
| Small (< 1GB) | 10-30s | 5-15s | 3-5s | ~1 min |
| Medium (1-5GB) | 30s-2min | 15-45s | 5-10s | ~3 min |
| Large (5-20GB) | 2-10min | 1-3min | 10-30s | ~15 min |

### Optimization Tips

1. **Run during off-hours** - Less database load
2. **Use SSD storage** - Faster backup/restore
3. **Increase PHP memory** - For large datasets
4. **Disable logging temporarily** - Faster updates

---

## ✅ Verification Checklist

After running scripts:

- [ ] Backup file created and compressed
- [ ] Backup file size is reasonable (not 0 bytes)
- [ ] Anonymization script ran without errors
- [ ] All 35 tables processed successfully
- [ ] Verification script passed all checks (45/45)
- [ ] Admin login works (admin@admin.com)
- [ ] Sample data check shows anonymized values
- [ ] No real names visible in database
- [ ] No real emails (except admin@admin.com)
- [ ] No real phone numbers visible

### Manual Spot Check

```bash
# Check a few random records
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev -e "
SELECT id, email, phone FROM users LIMIT 5;
SELECT id, company_name, email FROM companies LIMIT 5;
SELECT id, first_name, surname, email FROM contacts LIMIT 5;
SELECT id, custom_invoice_number, bill_to FROM sales_invoices LIMIT 3;
"
```

**Expected:** All should show anonymized data

---

## 🆘 Support

### Files Location
- Scripts: `/home/bwalia/workstation-ci4/`
  - `backup_database.php`
  - `anonymize_database.php`
  - `verify_anonymization.php`
- Backups: `/home/bwalia/workstation-ci4/backups/`
- Documentation: `/home/bwalia/workstation-ci4/PHP_ANONYMIZATION_GUIDE.md`

### Quick Commands

```bash
# Backup only
php scripts/backup_database.php

# Anonymize only (DANGEROUS without backup!)
php scripts/anonymize_database.php

# Verify only
php scripts/verify_anonymization.php

# All in one
php scripts/backup_database.php && php scripts/anonymize_database.php && php scripts/verify_anonymization.php

# Restore latest backup
LATEST=$(ls -t backups/*.sql.gz | head -1)
gunzip $LATEST
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < ${LATEST%.gz}
```

### Exit Codes

| Script | Code 0 | Code 1 |
|--------|--------|--------|
| `backup_database.php` | Success | Backup failed |
| `anonymize_database.php` | Success | Errors occurred |
| `verify_anonymization.php` | All passed | Issues found |

---

## 🔄 Comparison with SQL Scripts

| Feature | SQL Scripts | PHP Scripts |
|---------|-------------|-------------|
| **Error Handling** | Limited | Comprehensive |
| **Progress Tracking** | None | Real-time |
| **Verification** | Manual | Automated |
| **Rollback** | Manual | Automated backup |
| **Table Errors** | Stops all | Continues |
| **Output** | SQL only | Colored, formatted |
| **Debugging** | Difficult | Easy |
| **Automation** | Possible | Easy |

**Recommendation:** Use PHP scripts for reliability and better control.

---

**Last Updated:** 2025-10-12
**Version:** 1.0
**Maintained By:** Development Team
