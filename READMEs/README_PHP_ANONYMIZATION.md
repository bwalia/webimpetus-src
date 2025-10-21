# PHP Database Anonymization Scripts

⚡ **Fast, Reliable, and Easy to Use**

## 🚀 Quick Start

```bash
cd /home/bwalia/workstation-ci4

# Step 1: Backup
php scripts/backup_database.php

# Step 2: Anonymize
php scripts/anonymize_database.php

# Step 3: Verify
php scripts/verify_anonymization.php
```

## 📦 Scripts

| Script | Purpose | Time |
|--------|---------|------|
| `backup_database.php` | Create compressed backup | ~30s |
| `anonymize_database.php` | Anonymize 35+ tables | ~15s |
| `verify_anonymization.php` | Verify anonymization | ~5s |

## ✅ What Gets Anonymized

**ALL 35+ tables** including:
- ✅ Users (except admin@admin.com)
- ✅ Companies
- ✅ Contacts
- ✅ Sales Invoices & Items
- ✅ Timeslips
- ✅ Businesses, Employees, Customers
- ✅ Hospital Staff & Patient Logs
- ✅ Email Campaigns, Meetings, Events
- ✅ And 25+ more tables...

**Only Preserved:**
- ⛔ admin@admin.com user account

## 📊 Output Example

```
[1/35] Anonymizing Users...
  ✓ users: 47 rows updated
[2/35] Anonymizing Businesses...
  ✓ businesses: 23 rows updated
...
[35/35] Anonymizing Timeslips...
  ✓ timeslips: 567 rows updated

Total rows anonymized: 2,145
✓ Complete!
```

## 🔧 Features

✅ **Color-coded output** - Easy to read
✅ **Progress tracking** - Real-time (1/35, 2/35...)
✅ **Error handling** - Continues on errors
✅ **Automatic backup** - Compressed with gzip
✅ **Verification** - 45+ automated checks
✅ **Exit codes** - CI/CD friendly

## 🔄 Restore from Backup

```bash
# Find backup
ls -lh backups/

# Decompress
gunzip backups/myworkstation_dev_before_anonymization_*.sql.gz

# Restore
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' \
  myworkstation_dev < backups/myworkstation_dev_before_anonymization_*.sql
```

## 📖 Full Documentation

See [PHP_ANONYMIZATION_GUIDE.md](PHP_ANONYMIZATION_GUIDE.md) for:
- Detailed usage instructions
- Troubleshooting guide
- Configuration options
- Performance tips
- Automation examples

## ⚠️ Important

- **NEVER** run on production!
- **ALWAYS** backup first
- Verify database: `myworkstation_dev`
- Keep backups for 30+ days

## 🆘 Quick Help

```bash
# Check database
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' -e "SELECT DATABASE();"

# Make scripts executable
chmod +x *.php

# Run all-in-one
php scripts/backup_database.php && \
php scripts/anonymize_database.php && \
php scripts/verify_anonymization.php
```

---

**Why PHP over SQL?**
- ✅ Better error handling
- ✅ Progress tracking
- ✅ Colored output
- ✅ Continues on errors
- ✅ Exit codes for automation
- ✅ Easier to debug

**Status:** ✅ Production Ready
