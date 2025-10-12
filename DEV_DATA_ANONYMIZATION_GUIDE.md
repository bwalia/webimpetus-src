# Dev Environment Data Anonymization Guide

## Overview

This guide provides instructions for anonymizing PII (Personally Identifiable Information) data in the development environment and creating a reusable demo environment.

**Created:** 2025-10-12
**Purpose:** Remove real PII data from dev environment and replace with dummy/synonym data
**Database:** myworkstation_dev

---

## 📋 What Gets Anonymized

### ✅ Tables with PII Data (ANONYMIZED)
- **Users** (except admin@admin.com)
- **Businesses**
- **Employees**
- **Hospital Staff**
- **Patient Logs**
- **Customers**
- **Business Contacts**
- **Addresses**
- **Calendar Events**
- **Meetings**
- **Email Campaigns & Recipients**
- **Blog Comments**
- **Enquiries**
- **Interview Candidates**
- **Job Applications**
- **Incidents & Notifications**
- **Project Comments**
- **Secrets & Audit Logs**
- **Purchase Invoices & Notes**
- **Sales Invoice Notes**
- **Work Orders**
- **Domains**
- **Virtual Machines**
- **Tenants**
- **Deployments**
- **Knowledge Base**

### ⛔ Tables PRESERVED (Not Anonymized)
- **admin@admin.com** user account
- **Companies**
- **Contacts**
- **Sales Invoices**
- **Sales Invoice Items**
- **Timeslips**

---

## 🚀 Quick Start

### Option 1: Anonymize Existing Data

```bash
# Step 1: Backup current database
cd /home/bwalia/webimpetus-src/SQLs
./backup_before_anonymize.sh

# Step 2: Run anonymization script
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < anonymize_dev_data.sql
```

### Option 2: Create Fresh Demo Environment

```bash
# Import demo environment (resets to demo state)
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < create_demo_environment.sql
```

---

## 📝 Detailed Instructions

### Step 1: Backup Database

**ALWAYS backup before anonymizing!**

```bash
# Option A: Use automated backup script
cd /home/bwalia/webimpetus-src/SQLs
./backup_before_anonymize.sh
```

**Option B: Manual backup**
```bash
# Create backup directory
mkdir -p /home/bwalia/webimpetus-src/backups

# Backup database
docker exec webimpetus-db mariadb-dump \
    -u wsl_dev \
    -p'CHANGE_ME' \
    --single-transaction \
    --routines \
    --triggers \
    myworkstation_dev > /home/bwalia/webimpetus-src/backups/dev_backup_$(date +%Y%m%d_%H%M%S).sql

# Compress backup
gzip /home/bwalia/webimpetus-src/backups/dev_backup_*.sql
```

**Backup Location:** `/home/bwalia/webimpetus-src/backups/`

---

### Step 2: Run Anonymization

#### Method 1: Anonymize Existing Data

This preserves all data structure but replaces PII with dummy data.

```bash
cd /home/bwalia/webimpetus-src/SQLs

# Run anonymization script
docker exec -i webimpetus-db mariadb \
    -u wsl_dev \
    -p'CHANGE_ME' \
    myworkstation_dev < anonymize_dev_data.sql
```

**What happens:**
- Replaces names, emails, phones, addresses with dummy data
- Preserves database structure and relationships
- Keeps admin@admin.com intact
- Preserves companies, contacts, invoices, timeslips

#### Method 2: Create Demo Environment

This creates a consistent demo environment that can be imported anytime.

```bash
cd /home/bwalia/webimpetus-src/SQLs

# Import demo environment
docker exec -i webimpetus-db mariadb \
    -u wsl_dev \
    -p'CHANGE_ME' \
    myworkstation_dev < create_demo_environment.sql
```

**What happens:**
- All PII replaced with "Demo" prefixed data
- Consistent demo data (Demo User 1, Demo Business 1, etc.)
- Demo users get password: `demo123`
- Can be re-imported anytime to reset to demo state

---

### Step 3: Verify Anonymization

```bash
# Check anonymized data
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev -e "
SELECT 'Users' AS Table_Name, COUNT(*) AS Total,
       SUM(CASE WHEN email LIKE '%@example.com' OR email LIKE '%@demo.example.com' THEN 1 ELSE 0 END) AS Anonymized
FROM users WHERE email != 'admin@admin.com'
UNION ALL
SELECT 'Businesses', COUNT(*), SUM(CASE WHEN email LIKE '%@example.com' OR email LIKE '%@demo.example.com' THEN 1 ELSE 0 END) FROM businesses
UNION ALL
SELECT 'Employees', COUNT(*), SUM(CASE WHEN email LIKE '%@example.com' OR email LIKE '%@demo.example.com' THEN 1 ELSE 0 END) FROM employees
UNION ALL
SELECT 'Customers', COUNT(*), SUM(CASE WHEN email LIKE '%@example.com' OR email LIKE '%@demo.example.com' THEN 1 ELSE 0 END) FROM customers;
"
```

**Expected Output:**
- All anonymized records should show matching Total and Anonymized counts
- Emails should end with @example.com or @demo.example.com

---

### Step 4: Test Login

#### Admin Account (Preserved)
```
Email: admin@admin.com
Password: [your original password]
```

#### Demo Users (After anonymization)
```
Email: demo_user_1@demo.example.com
Password: demo123

Email: demo_user_2@demo.example.com
Password: demo123
```

---

## 🔄 Restore from Backup

If something goes wrong, restore from backup:

```bash
# Find your backup file
ls -lh /home/bwalia/webimpetus-src/backups/

# Decompress if needed
gunzip /home/bwalia/webimpetus-src/backups/[backup_file].sql.gz

# Restore database
docker exec -i webimpetus-db mariadb \
    -u wsl_dev \
    -p'CHANGE_ME' \
    myworkstation_dev < /home/bwalia/webimpetus-src/backups/[backup_file].sql
```

---

## 📊 Data Transformation Examples

### Users
| Original | Anonymized |
|----------|------------|
| john.smith@company.com | user_123@example.com |
| 07712345678 | 555-0123 |
| 123 Real Street | 123 Anonymous Street |

### Businesses
| Original | Anonymized |
|----------|------------|
| Acme Corp Ltd | Business_456 |
| john@acmecorp.com | business_456@example.com |
| GB123456789 (VAT) | VAT00000456 |

### Hospital Staff
| Original | Anonymized |
|----------|------------|
| Dr. Jane Smith | Doctor123 Staff123 |
| jane.smith@hospital.nhs.uk | doctor_123@hospital.example.com |
| Patient notes with PII | Anonymized patient note 123 |

### Patient Logs
| Original | Anonymized |
|----------|------------|
| Real medication names | Medication_123 |
| Actual lab results | Result_123 |
| Doctor's private notes | Doctor note 123 |

---

## 🗂️ Files Created

### 1. Anonymization Scripts
- **Location:** `/home/bwalia/webimpetus-src/SQLs/`
- **Files:**
  - `anonymize_dev_data.sql` - Main anonymization script
  - `create_demo_environment.sql` - Demo environment setup
  - `backup_before_anonymize.sh` - Automated backup script

### 2. Backups
- **Location:** `/home/bwalia/webimpetus-src/backups/`
- **Format:** `myworkstation_dev_before_anonymization_YYYYMMDD_HHMMSS.sql.gz`
- **Retention:** Keep at least 3 most recent backups

### 3. Documentation
- **Location:** `/home/bwalia/webimpetus-src/`
- **File:** `DEV_DATA_ANONYMIZATION_GUIDE.md` (this file)

---

## 🔐 Security Notes

### ⚠️ CRITICAL
- **NEVER run these scripts on production database!**
- Always check environment before running
- Verify you're connected to dev database
- Keep backups for at least 30 days

### Database Connection Check
```bash
# Verify you're on DEV database
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' -e "SELECT DATABASE();"

# Should output: myworkstation_dev
```

### Password Security
- All demo passwords are: `demo123`
- Original admin password is preserved
- Real passwords are replaced with bcrypt hash of "password"

---

## 📅 Scheduled Anonymization

### Weekly Refresh (Recommended)

Create a cron job to refresh demo data weekly:

```bash
# Edit crontab
crontab -e

# Add this line (runs every Monday at 2 AM)
0 2 * * 1 /home/bwalia/webimpetus-src/SQLs/backup_before_anonymize.sh && docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < /home/bwalia/webimpetus-src/SQLs/create_demo_environment.sql
```

---

## 🐛 Troubleshooting

### Issue: Backup Script Fails
```bash
# Check Docker container is running
docker ps | grep webimpetus-db

# Check database exists
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' -e "SHOW DATABASES;"
```

### Issue: Anonymization Script Fails
```bash
# Check SQL syntax
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev -e "SELECT VERSION();"

# Run with verbose error output
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' --verbose myworkstation_dev < anonymize_dev_data.sql
```

### Issue: Can't Login After Anonymization
```bash
# Reset admin password
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev -e "
UPDATE users SET password = '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = 'admin@admin.com';
"
# Password is now: password
```

### Issue: Foreign Key Errors
```bash
# Temporarily disable foreign key checks
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev -e "
SET FOREIGN_KEY_CHECKS = 0;
SOURCE /src/SQLs/anonymize_dev_data.sql;
SET FOREIGN_KEY_CHECKS = 1;
"
```

---

## ✅ Verification Checklist

After anonymization, verify:

- [ ] Backup file created and compressed
- [ ] No real names in users table (except admin)
- [ ] No real email addresses (except admin@admin.com)
- [ ] No real phone numbers
- [ ] No real addresses
- [ ] No real company information
- [ ] No real patient data
- [ ] Admin login still works
- [ ] Companies, contacts, invoices preserved
- [ ] Timeslips data intact

### Quick Verification Script
```bash
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev -e "
SELECT 'Real Data Check' AS Test;
SELECT 'Users with real emails' AS Issue, COUNT(*) AS Count
FROM users
WHERE email NOT LIKE '%@example.com'
  AND email NOT LIKE '%@demo.example.com'
  AND email != 'admin@admin.com';
"
```

**Expected:** Count should be 0

---

## 📞 Support

### Files Location
- Scripts: `/home/bwalia/webimpetus-src/SQLs/`
- Backups: `/home/bwalia/webimpetus-src/backups/`
- Docs: `/home/bwalia/webimpetus-src/DEV_DATA_ANONYMIZATION_GUIDE.md`

### Quick Commands Reference

```bash
# Backup
./backup_before_anonymize.sh

# Anonymize existing data
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < anonymize_dev_data.sql

# Create demo environment
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < create_demo_environment.sql

# Restore from backup
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < /path/to/backup.sql

# Verify anonymization
docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev -e "SELECT COUNT(*) FROM users WHERE email LIKE '%@example.com';"
```

---

## 📈 Best Practices

1. **Always Backup First** - Run backup script before any anonymization
2. **Test on Copy** - Test scripts on a database copy first
3. **Verify Results** - Always run verification queries
4. **Keep Backups** - Retain backups for at least 30 days
5. **Document Changes** - Note any custom anonymization rules
6. **Schedule Regular Refreshes** - Weekly demo environment refresh recommended
7. **Monitor Disk Space** - Backups can grow large, monitor storage

---

## 🔄 Regular Maintenance

### Daily
- Check dev environment is using anonymized data
- Verify no new PII has been added

### Weekly
- Create fresh demo environment
- Verify all anonymization rules still apply
- Test login functionality

### Monthly
- Review and update anonymization rules
- Clean old backups (keep latest 5)
- Update documentation if needed

---

**Last Updated:** 2025-10-12
**Version:** 1.0
**Maintained By:** Development Team
